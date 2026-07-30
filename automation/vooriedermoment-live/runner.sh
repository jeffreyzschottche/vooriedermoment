#!/bin/zsh
set -euo pipefail

script_dir=${0:A:h}
api_base=${VOORIEDERMOMENT_API_BASE:-https://api.vooriedermoment.nl/api/v1}
root_dir=${VOORIEDERMOMENT_ORDERS_DIR:-$HOME/Desktop/vooriedermoment-live-aanvragen}
state_dir="$root_dir/.runner"
central_log="$root_dir/macro.log"
compat_pid_file="$state_dir/compat-server.pid"
automation_key_file="$state_dir/automation-key"
compat_server="$script_dir/compat_server.py"
worker_id=${VOORIEDERMOMENT_WORKER_ID:-studio-mac}
ffmpeg_bin=${FFMPEG_BIN:-/opt/homebrew/bin/ffmpeg}
jq_bin=${JQ_BIN:-/usr/bin/jq}
curl_bin=${CURL_BIN:-/usr/bin/curl}

die() {
  print -u2 -- "$*"
  exit 1
}

require_bin() {
  [[ -x "$1" ]] || die "Ontbrekend programma: $1"
}

automation_key() {
  if [[ -n "${AUTOMATION_API_KEY:-}" ]]; then
    print -r -- "$AUTOMATION_API_KEY"
    return
  fi

  if [[ -s "$automation_key_file" ]]; then
    /bin/cat "$automation_key_file"
    return
  fi

  return 1
}

configure_key() {
  local key=${1:-}
  [[ -n "$key" ]] ||
    die "Automation-key is leeg. Vul automationApiKey bovenaan AppleScript-stap 1 in."

  /bin/mkdir -p "$root_dir" "$state_dir"
  /usr/bin/printf '%s' "$key" > "$automation_key_file"
  /bin/chmod 600 "$automation_key_file"
  print -r -- "Automation-key ingesteld."
}

slugify() {
  print -rn -- "$1" |
    /usr/bin/iconv -f UTF-8 -t ASCII//TRANSLIT 2>/dev/null |
    /usr/bin/tr '[:upper:]' '[:lower:]' |
    /usr/bin/sed -E 's/[^a-z0-9]+/-/g; s/^-+|-+$//g; s/-+/-/g'
}

log_line() {
  local order_dir=$1
  shift
  local message="$*"
  local line
  line="$(/bin/date -u '+%Y-%m-%dT%H:%M:%SZ') $message"
  printf '%s\n' "$line" >> "$order_dir/run.log"
  printf '%s order_dir=%s\n' "$line" "$order_dir" >> "$central_log"
}

log_global() {
  /bin/mkdir -p "$root_dir"
  printf '%s %s\n' "$(/bin/date -u '+%Y-%m-%dT%H:%M:%SZ')" "$*" >> "$central_log"
}

current_order_dir() {
  local state_file="$state_dir/current-order-dir.txt"
  [[ -s "$state_file" ]] || die "Geen actieve order in $state_file"
  local order_dir
  order_dir=$(/bin/cat "$state_file")
  [[ -d "$order_dir" ]] || die "Actieve ordermap bestaat niet: $order_dir"
  print -r -- "$order_dir"
}

set_step() {
  local order_dir=$1
  local step=$2
  "$jq_bin" -cn \
    --arg step "$step" \
    --arg updated_at "$(/bin/date -u '+%Y-%m-%dT%H:%M:%SZ')" \
    '{step:$step,updated_at:$updated_at}' > "$order_dir/status.json"
  log_line "$order_dir" "stap=$step"
}

claim_order() {
  local key response data order_id category recipient slug order_dir
  key=$(automation_key) || die "Automation-key ontbreekt. Vul automationApiKey bovenaan AppleScript-stap 1 in."
  [[ -n "$key" ]] || die "Automation-key ontbreekt. Vul automationApiKey bovenaan AppleScript-stap 1 in."

  /bin/mkdir -p "$root_dir" "$state_dir"
  response=$("$curl_bin" -fsS \
    -X POST \
    -H "X-Automation-Key: $key" \
    -H 'Content-Type: application/json' \
    --data "$("$jq_bin" -cn --arg worker_id "$worker_id" '{worker_id:$worker_id}')" \
    "$api_base/automation/orders/claim")

  data=$(print -rn -- "$response" | "$jq_bin" -c '.data')
  if [[ "$data" == "null" ]]; then
    print -r -- '{"empty":true}'
    return
  fi

  print -rn -- "$response" | "$jq_bin" -e '
    .data.order.order_id and
    .data.order.suno.title and
    .data.order.suno.style and
    .data.order.suno.lyrics and
    .data.claim_token
  ' >/dev/null || die "Claim-response mist verplichte velden."

  order_id=$(print -rn -- "$response" | "$jq_bin" -r '.data.order.order_id')
  category=$(print -rn -- "$response" | "$jq_bin" -r '.data.order.category // "aanvraag"')
  recipient=$(print -rn -- "$response" | "$jq_bin" -r '.data.order.recipient_name // "ontvanger"')
  slug=$(slugify "$category-voor-$recipient")
  [[ -n "$slug" ]] || slug=aanvraag
  order_dir="$root_dir/order-$order_id-$slug"

  /bin/mkdir -p "$order_dir/full" "$order_dir/covers" "$order_dir/previews"
  print -rn -- "$response" | "$jq_bin" '.data.order' > "$order_dir/order.json"
  print -rn -- "$response" | "$jq_bin" '{
    order_id:.data.order.order_id,
    claim_token:.data.claim_token,
    claimed_by:.data.claimed_by,
    claim_expires_at:.data.claim_expires_at
  }' > "$order_dir/claim.json"

  # De bestaande Firefox/Suno-invoerstappen lezen current.json.
  /bin/cp "$order_dir/order.json" "$root_dir/current.json"
  print -r -- "$order_dir" > "$state_dir/current-order-dir.txt"

  log_line "$order_dir" "Order $order_id geclaimd."
  set_step "$order_dir" "claimed"
  "$jq_bin" -cn \
    --arg order_dir "$order_dir" \
    --argjson order_id "$order_id" \
    '{empty:false,order_id:$order_id,order_dir:$order_dir}'
}

fail_order() {
  local order_dir=${1:?Ordermap ontbreekt}
  local error_message=${2:-Onbekende fout in lokale Suno-run}
  local key order_id token payload
  key=$(automation_key) || die "Automation-key ontbreekt."
  order_id=$("$jq_bin" -r '.order_id' "$order_dir/claim.json")
  token=$("$jq_bin" -r '.claim_token' "$order_dir/claim.json")
  payload=$("$jq_bin" -cn --arg error "$error_message" '{error:$error}')

  "$curl_bin" -fsS \
    -X POST \
    -H "X-Automation-Key: $key" \
    -H "X-Claim-Token: $token" \
    -H 'Content-Type: application/json' \
    --data "$payload" \
    "$api_base/automation/orders/$order_id/fail" >/dev/null

  log_line "$order_dir" "Order vrijgegeven na fout: $error_message"
}

validate_sample_files() {
  local order_dir=$1
  local position audio cover

  [[ -f "$order_dir/samples.json" ]] || die "samples.json ontbreekt in $order_dir"
  "$jq_bin" -e 'type == "array" and length == 4' "$order_dir/samples.json" >/dev/null ||
    die "samples.json moet exact vier samples bevatten."

  for position in 1 2 3 4; do
    audio=$("$jq_bin" -r --argjson p "$position" \
      '.[] | select(.position == $p) | .audio_path // empty' "$order_dir/samples.json")
    cover=$("$jq_bin" -r --argjson p "$position" \
      '.[] | select(.position == $p) | .cover_path // empty' "$order_dir/samples.json")

    [[ -n "$audio" && -f "$audio" ]] || die "Volledig nummer $position ontbreekt."
    [[ -n "$cover" && -f "$cover" ]] || die "Cover $position ontbreekt."
  done
}

make_previews() {
  local order_dir=$1
  local position audio preview duration

  for position in 1 2 3 4; do
    audio=$("$jq_bin" -r --argjson p "$position" \
      '.[] | select(.position == $p) | .audio_path' "$order_dir/samples.json")
    preview="$order_dir/previews/$position.mp3"

    "$ffmpeg_bin" -hide_banner -loglevel error -y \
      -ss 00:00:50 \
      -i "$audio" \
      -t 15 \
      -vn \
      -codec:a libmp3lame \
      -b:a 192k \
      "$preview"

    duration=$("$ffmpeg_bin" -i "$preview" 2>&1 |
      /usr/bin/sed -nE 's/.*Duration: ([0-9:.]+).*/\1/p' |
      /usr/bin/head -1)
    [[ -s "$preview" ]] || die "Preview $position kon niet worden gemaakt."
    log_line "$order_dir" "Preview $position gemaakt (duur $duration)."
  done
}

upload_samples() {
  local order_dir=$1
  local key order_id token position title source_url cover preview
  local -a args

  key=$(automation_key) || die "Automation-key ontbreekt."
  order_id=$("$jq_bin" -r '.order_id' "$order_dir/claim.json")
  token=$("$jq_bin" -r '.claim_token' "$order_dir/claim.json")

  args=(
    -fsS
    -X POST
    -H "X-Automation-Key: $key"
    -H "X-Claim-Token: $token"
  )

  for position in 1 2 3 4; do
    title=$("$jq_bin" -r --argjson p "$position" \
      '.[] | select(.position == $p) | .title' "$order_dir/samples.json")
    source_url=$("$jq_bin" -r --argjson p "$position" \
      '.[] | select(.position == $p) | .suno_source_url' "$order_dir/samples.json")
    cover=$("$jq_bin" -r --argjson p "$position" \
      '.[] | select(.position == $p) | .cover_path' "$order_dir/samples.json")
    preview="$order_dir/previews/$position.mp3"

    [[ "$source_url" == https://suno.com/* ]] ||
      die "Ongeldige Suno-URL bij sample $position."

    local index=$((position - 1))
    args+=(
      -F "samples[$index][position]=$position"
      -F "samples[$index][title]=$title"
      -F "samples[$index][suno_source_url]=$source_url"
      -F "samples[$index][preview]=@$preview;type=audio/mpeg"
      -F "samples[$index][cover]=@$cover"
    )
  done

  "$curl_bin" "${args[@]}" \
    "$api_base/automation/orders/$order_id/samples" |
    "$jq_bin" '.' > "$order_dir/upload-response.json"

  "$jq_bin" -e '.data.status == "samples_ready"' \
    "$order_dir/upload-response.json" >/dev/null ||
    die "Backend bevestigde samples_ready niet."

  log_line "$order_dir" "Vier samples geüpload; klantmail staat in de backend-queue."
}

finish_order() {
  local order_dir=${1:?Ordermap ontbreekt}
  validate_sample_files "$order_dir"
  make_previews "$order_dir"
  upload_samples "$order_dir"
}

validate_current() {
  local order_dir
  order_dir=$(current_order_dir)
  validate_sample_files "$order_dir"
  set_step "$order_dir" "downloads_validated"
  print -r -- "$order_dir"
}

previews_current() {
  local order_dir
  order_dir=$(current_order_dir)
  validate_sample_files "$order_dir"
  make_previews "$order_dir"
  set_step "$order_dir" "previews_ready"
  print -r -- "$order_dir"
}

upload_current() {
  local order_dir
  order_dir=$(current_order_dir)
  validate_sample_files "$order_dir"
  for position in 1 2 3 4; do
    [[ -s "$order_dir/previews/$position.mp3" ]] ||
      die "Preview $position ontbreekt; voer eerst previews uit."
  done
  upload_samples "$order_dir"
  set_step "$order_dir" "uploaded"
  /bin/rm -f "$state_dir/current-order-dir.txt"
  print -r -- "$order_dir"
}

fail_current() {
  local error_message=${1:-Onbekende fout in lokale Suno-run}
  local order_dir
  order_dir=$(current_order_dir)
  fail_order "$order_dir" "$error_message"
  set_step "$order_dir" "failed"
  /bin/rm -f "$state_dir/current-order-dir.txt"
}

show_current() {
  current_order_dir
}

compat_start() {
  /bin/mkdir -p "$root_dir" "$state_dir"
  if "$curl_bin" -fsS http://127.0.0.1:8000/health >/dev/null 2>&1; then
    log_global "compat-server reeds actief"
    return
  fi

  /usr/bin/nohup /usr/bin/python3 "$compat_server" \
    --root "$root_dir" \
    --port 8000 \
    >> "$root_dir/compat-server.log" 2>&1 &
  print -r -- "$!" > "$compat_pid_file"

  local attempt
  for attempt in {1..20}; do
    if "$curl_bin" -fsS http://127.0.0.1:8000/health >/dev/null 2>&1; then
      log_global "compat-server gestart pid=$(/bin/cat "$compat_pid_file")"
      return
    fi
    /bin/sleep 0.1
  done

  die "Lokale compatibility-server kon niet starten op poort 8000."
}

compat_stop() {
  if [[ -s "$compat_pid_file" ]]; then
    local pid
    pid=$(/bin/cat "$compat_pid_file")
    if [[ "$pid" == <-> ]]; then
      /bin/kill "$pid" 2>/dev/null || true
    fi
    /bin/rm -f "$compat_pid_file"
    log_global "compat-server gestopt"
  fi
}

doctor() {
  require_bin "$curl_bin"
  require_bin "$jq_bin"
  require_bin "$ffmpeg_bin"
  local key
  key=$(automation_key) ||
    die "Automation-key ontbreekt. Vul automationApiKey bovenaan AppleScript-stap 1 in."
  [[ -n "$key" ]] ||
    die "Automation-key ontbreekt. Vul automationApiKey bovenaan AppleScript-stap 1 in."
  /bin/mkdir -p "$root_dir"
  /bin/mkdir -p "$state_dir"
  print -r -- "OK: curl, jq, ffmpeg, automation-key en doelmap zijn beschikbaar."
}

dry_run() {
  require_bin "$jq_bin"
  require_bin "$ffmpeg_bin"
  /bin/mkdir -p "$root_dir"
  local fixture_dir="$root_dir/dry-run"
  /bin/mkdir -p "$fixture_dir/full" "$fixture_dir/covers" "$fixture_dir/previews"
  print -r -- "OK: dry-run claimt geen order en verstuurt geen mail."
  print -r -- "Doelmap: $fixture_dir"
}

case "${1:-}" in
  configure-key)
    configure_key "${2:-}"
    ;;
  claim)
    doctor >/dev/null
    claim_order
    ;;
  current)
    show_current
    ;;
  compat-start)
    compat_start
    ;;
  compat-stop)
    compat_stop
    ;;
  validate)
    doctor >/dev/null
    validate_current
    ;;
  previews)
    doctor >/dev/null
    previews_current
    ;;
  upload)
    doctor >/dev/null
    upload_current
    ;;
  fail-current)
    doctor >/dev/null
    fail_current "${2:-}"
    ;;
  log)
    shift
    log_global "$*"
    ;;
  finish)
    doctor >/dev/null
    finish_order "${2:-}"
    ;;
  fail)
    doctor >/dev/null
    fail_order "${2:-}" "${3:-}"
    ;;
  doctor)
    doctor
    ;;
  dry-run)
    dry_run
    ;;
  *)
    die "Gebruik: $0 {configure-key KEY|doctor|dry-run|claim|current|compat-start|compat-stop|validate|previews|upload|fail-current FOUT|finish ORDERMAP|fail ORDERMAP FOUT|log BERICHT}"
    ;;
esac
