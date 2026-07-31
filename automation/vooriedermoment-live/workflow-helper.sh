#!/bin/zsh
set -euo pipefail

api_base=${VOORIEDERMOMENT_API_BASE:-https://api.vooriedermoment.nl/api/v1}
orders_dir=${VOORIEDERMOMENT_ORDERS_DIR:-$HOME/Desktop/vooriedermoment-live-aanvragen}
state_dir="$orders_dir/.runner"
automation_key_file="$state_dir/automation-key"
current_order_file="$state_dir/current-order-dir.txt"
downloads_dir=${VOORIEDERMOMENT_DOWNLOADS_DIR:-$HOME/Downloads}
worker_id=${VOORIEDERMOMENT_WORKER_ID:-studio-mac}
curl_bin=${CURL_BIN:-/usr/bin/curl}
jq_bin=${JQ_BIN:-/usr/bin/jq}
ffmpeg_bin=${FFMPEG_BIN:-/opt/homebrew/bin/ffmpeg}
ffprobe_bin=${FFPROBE_BIN:-/opt/homebrew/bin/ffprobe}

die() {
  print -u2 -- "$*"
  exit 1
}

require_bin() {
  [[ -x "$1" ]] || die "Ontbrekend programma: $1"
}

current_order_dir() {
  [[ -s "$current_order_file" ]] || die "Er is geen actieve order."
  local order_dir
  order_dir=$(/bin/cat "$current_order_file")
  [[ -d "$order_dir" ]] || die "Ordermap bestaat niet: $order_dir"
  print -r -- "$order_dir"
}

automation_key() {
  [[ -s "$automation_key_file" ]] || die "Automation-key ontbreekt."
  /bin/cat "$automation_key_file"
}

slugify() {
  /usr/bin/printf '%s' "$1" |
    /usr/bin/iconv -f UTF-8 -t ASCII//TRANSLIT 2>/dev/null |
    /usr/bin/tr '[:upper:]' '[:lower:]' |
    /usr/bin/sed -E 's/[^a-z0-9]+/-/g; s/^-+|-+$//g; s/-+/-/g'
}

log_line() {
  local order_dir=$1
  shift
  local timestamp
  timestamp=$(/bin/date -u '+%Y-%m-%dT%H:%M:%SZ')
  /usr/bin/printf '%s %s\n' "$timestamp" "$*" >> "$order_dir/run.log"
  /usr/bin/printf '%s %s order_dir=%s\n' "$timestamp" "$*" "$order_dir" >> "$orders_dir/macro.log"
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

configure_key() {
  local key=${1:-}
  [[ -n "$key" ]] || die "automationApiKey is leeg."
  /bin/mkdir -p "$orders_dir" "$state_dir"
  /usr/bin/printf '%s' "$key" > "$automation_key_file"
  /bin/chmod 600 "$automation_key_file"
}

extract_upload_token() {
  /usr/bin/printf '%s' "$1" |
    /usr/bin/sed -nE 's|^https?://api\.vooriedermoment\.nl/admin/upload/([A-Za-z0-9]+)([/?#].*)?$|\1|p'
}

claim_url() {
  local page_url=${1:-}
  local upload_token key payload response order_id category recipient slug order_dir
  upload_token=$(extract_upload_token "$page_url")
  [[ -n "$upload_token" ]] || die "Open eerst de juiste api.vooriedermoment.nl/admin/upload/... pagina."

  # Een afgebroken run moet dezelfde lokaal bewaarde claim kunnen hervatten
  # zonder opnieuw vier Suno-nummers te genereren.
  if [[ -s "$current_order_file" ]]; then
    order_dir=$(/bin/cat "$current_order_file")
    if [[ -s "$order_dir/order.json" && -s "$order_dir/claim.json" ]] &&
      "$jq_bin" -e --arg token "$upload_token" \
        '(.admin_upload_url // "") | contains($token)' "$order_dir/order.json" >/dev/null &&
      (( $("$jq_bin" -r '.claim_expires_at | sub("\\+00:00$"; "Z") | fromdateiso8601' "$order_dir/claim.json") > $(/bin/date +%s) )); then
      log_line "$order_dir" "bestaande lokale claim hervat upload_token=$upload_token"
      print -r -- "$order_dir"
      return
    fi
  fi

  key=$(automation_key)
  payload=$("$jq_bin" -cn \
    --arg worker_id "$worker_id" \
    --arg admin_upload_token "$upload_token" \
    '{worker_id:$worker_id,admin_upload_token:$admin_upload_token}')
  response=$("$curl_bin" -fsS --max-time 45 \
    -X POST \
    -H "X-Automation-Key: $key" \
    -H 'Content-Type: application/json' \
    --data "$payload" \
    "$api_base/automation/orders/claim")

  print -rn -- "$response" | "$jq_bin" -e '
    .data.order.order_id and
    .data.order.suno.title and
    .data.order.suno.style and
    .data.order.suno.lyrics and
    .data.claim_token
  ' >/dev/null || die "Deze order is niet klaar, al geclaimd of al verwerkt."

  order_id=$(print -rn -- "$response" | "$jq_bin" -r '.data.order.order_id')
  category=$(print -rn -- "$response" | "$jq_bin" -r '.data.order.category // "aanvraag"')
  recipient=$(print -rn -- "$response" | "$jq_bin" -r '.data.order.recipient_name // "ontvanger"')
  slug=$(slugify "$category-voor-$recipient")
  [[ -n "$slug" ]] || slug=aanvraag
  order_dir="$orders_dir/order-$order_id-$slug"

  /bin/mkdir -p "$order_dir/full" "$order_dir/previews"
  print -rn -- "$response" | "$jq_bin" '.data.order' > "$order_dir/order.json"
  print -rn -- "$response" | "$jq_bin" '{
    order_id:.data.order.order_id,
    claim_token:.data.claim_token,
    claimed_by:.data.claimed_by,
    claim_expires_at:.data.claim_expires_at
  }' > "$order_dir/claim.json"
  /bin/chmod 600 "$order_dir/claim.json"
  print -rn -- "$response" | "$jq_bin" 'del(.data.claim_token)' > "$order_dir/claim-response.json"
  /bin/cp "$order_dir/order.json" "$orders_dir/current.json"
  /usr/bin/printf '%s\n' "$order_dir" > "$current_order_file"

  if [[ ! -s "$order_dir/samples.json" ]]; then
    local base_title
    base_title=$("$jq_bin" -r '.suno.title' "$order_dir/order.json")
    "$jq_bin" -cn --arg title "$base_title" '[range(1;5) | {
      position:.,
      title:($title + " – versie " + (.|tostring)),
      suno_source_url:null,
      audio_path:null,
      preview_path:null
    }]' > "$order_dir/samples.json"
  fi

  set_step "$order_dir" claimed
  log_line "$order_dir" "order=$order_id upload_token=$upload_token geclaimd"
  print -r -- "$order_dir"
}

value() {
  local query=${1:?jq-query ontbreekt}
  local order_dir
  order_dir=$(current_order_dir)
  "$jq_bin" -er "$query" "$order_dir/order.json"
}

stage() {
  local order_dir
  order_dir=$(current_order_dir)
  "$jq_bin" -r '.step // "claimed"' "$order_dir/status.json"
}

sample_value() {
  local position=${1:?positie ontbreekt}
  local field=${2:?veld ontbreekt}
  local order_dir
  order_dir=$(current_order_dir)
  "$jq_bin" -r --argjson position "$position" --arg field "$field" \
    '.[] | select(.position == $position) | .[$field] // ""' "$order_dir/samples.json"
}

generate_cover() {
  local key_file=${1:?OpenAI-keybestand ontbreekt}
  local order_dir response_file request_file curl_config prompt image_base64
  order_dir=$(current_order_dir)
  [[ -s "$key_file" ]] || die "OpenAI-keybestand is leeg."
  [[ -s "$order_dir/cover.png" ]] && {
    print -r -- "$order_dir/cover.png"
    return
  }

  prompt=$("$jq_bin" -r '
    "Create a polished square album cover for a personalized Dutch song. " +
    "Occasion/category: " + (.category_title // .category // "personal song") + ". " +
    "Recipient or subject: " + (.recipient_name // "the recipient") + ". " +
    "Musical direction: " + (.suno.style // "warm contemporary pop") + ". " +
    "Use the emotional tone and concrete story details from this intake as visual inspiration: " +
    ((.intake // {}) | to_entries | map(.key + ": " + (.value | tostring)) | join("; ")) + 
    ". Attractive commercial music artwork, emotionally specific, coherent lighting, no typography, no letters, no logos, no watermark."
  ' "$order_dir/order.json")

  request_file="$order_dir/cover-request.json"
  response_file="$order_dir/cover-response.json"
  "$jq_bin" -cn \
    --arg model "gpt-image-2" \
    --arg prompt "$prompt" \
    '{model:$model,prompt:$prompt,n:1,size:"1024x1024",quality:"medium",output_format:"png"}' > "$request_file"

  curl_config=$(/usr/bin/mktemp -t vim-openai-curl)
  /bin/chmod 600 "$curl_config"
  trap '/bin/rm -f "$curl_config"' EXIT INT TERM
  /usr/bin/printf 'header = "Authorization: Bearer %s"\n' "$(/bin/cat "$key_file")" > "$curl_config"
  response=$("$curl_bin" -fsS --max-time 300 \
    --config "$curl_config" \
    -H 'Content-Type: application/json' \
    -X POST \
    --data-binary "@$request_file" \
    https://api.openai.com/v1/images/generations)
  /bin/rm -f "$curl_config"
  trap - EXIT INT TERM

  image_base64=$(print -rn -- "$response" | "$jq_bin" -er '.data[0].b64_json') || {
    print -rn -- "$response" | "$jq_bin" 'del(.data[]?.b64_json)' > "$response_file"
    die "OpenAI heeft geen coverafbeelding teruggegeven."
  }
  /usr/bin/printf '%s' "$image_base64" | /usr/bin/base64 -D > "$order_dir/cover.png"
  [[ -s "$order_dir/cover.png" ]] || die "De gegenereerde cover is leeg."
  print -rn -- "$response" | "$jq_bin" 'del(.data[]?.b64_json)' > "$response_file"
  set_step "$order_dir" cover_ready
  print -r -- "$order_dir/cover.png"
}

record_link() {
  local position=${1:?positie ontbreekt}
  local source_url=${2:?Suno-link ontbreekt}
  local order_dir temp_file
  [[ "$position" == <1-4> ]] || die "Ongeldige positie: $position"
  [[ "$source_url" == https://suno.com/* ]] || die "Ongeldige Suno-link: $source_url"
  order_dir=$(current_order_dir)
  temp_file=$(/usr/bin/mktemp -t vim-samples)
  "$jq_bin" --argjson position "$position" --arg url "$source_url" '
    map(if .position == $position then .suno_source_url = $url else . end)
  ' "$order_dir/samples.json" > "$temp_file"
  /bin/mv "$temp_file" "$order_dir/samples.json"
  /bin/cp "$order_dir/samples.json" "$order_dir/suno-links.json"
  log_line "$order_dir" "suno_link positie=$position url=$source_url"
}

mark_download() {
  local position=${1:?positie ontbreekt}
  local order_dir marker
  order_dir=$(current_order_dir)
  marker="$order_dir/.download-$position.marker"
  /usr/bin/touch "$marker"
  print -r -- "$marker"
}

capture_download() {
  local position=${1:?positie ontbreekt}
  local order_dir marker target candidate size_a size_b duration temp_file
  order_dir=$(current_order_dir)
  marker="$order_dir/.download-$position.marker"
  target="$order_dir/full/$position.mp3"
  [[ -f "$marker" ]] || die "Downloadmarker voor positie $position ontbreekt."

  if [[ -s "$target" ]]; then
    print -r -- "$target"
    return
  fi

  for _ in {1..120}; do
    candidate=$(/usr/bin/find "$downloads_dir" -maxdepth 1 -type f \
      \( -iname '*.mp3' -o -iname '*.mpeg' \) -newer "$marker" -print 2>/dev/null |
      /usr/bin/head -1)
    if [[ -n "$candidate" && -s "$candidate" ]]; then
      size_a=$(/usr/bin/stat -f '%z' "$candidate")
      /bin/sleep 2
      [[ -f "$candidate" ]] || continue
      size_b=$(/usr/bin/stat -f '%z' "$candidate")
      if [[ "$size_a" == "$size_b" && "$size_b" -gt 500000 ]]; then
        /bin/mv "$candidate" "$target"
        duration=$("$ffprobe_bin" -v error -show_entries format=duration \
          -of default=noprint_wrappers=1:nokey=1 "$target")
        (( ${duration%.*} >= 45 )) || die "Nummer $position is korter dan 45 seconden."
        temp_file=$(/usr/bin/mktemp -t vim-samples)
        "$jq_bin" --argjson position "$position" --arg path "$target" '
          map(if .position == $position then .audio_path = $path else . end)
        ' "$order_dir/samples.json" > "$temp_file"
        /bin/mv "$temp_file" "$order_dir/samples.json"
        log_line "$order_dir" "download positie=$position pad=$target duur=$duration"
        print -r -- "$target"
        return
      fi
    fi
    /bin/sleep 1
  done
  die "Geen complete nieuwe MP3 gevonden voor positie $position."
}

make_previews() {
  local order_dir position audio preview duration temp_file
  order_dir=$(current_order_dir)
  "$jq_bin" -e 'length == 4 and all(.suno_source_url and .audio_path)' "$order_dir/samples.json" >/dev/null ||
    die "Niet alle vier links en audiobestanden zijn opgeslagen."

  for position in 1 2 3 4; do
    audio=$("$jq_bin" -er --argjson position "$position" '.[] | select(.position == $position) | .audio_path' "$order_dir/samples.json")
    preview="$order_dir/previews/$position.mp3"
    "$ffmpeg_bin" -hide_banner -loglevel error -y \
      -ss 00:00:30 -i "$audio" -t 15 -vn -codec:a libmp3lame -b:a 192k "$preview"
    duration=$("$ffprobe_bin" -v error -show_entries format=duration \
      -of default=noprint_wrappers=1:nokey=1 "$preview")
    (( ${duration%.*} >= 14 && ${duration%.*} <= 16 )) || die "Preview $position is niet ongeveer 15 seconden."
    temp_file=$(/usr/bin/mktemp -t vim-samples)
    "$jq_bin" --argjson position "$position" --arg path "$preview" '
      map(if .position == $position then .preview_path = $path else . end)
    ' "$order_dir/samples.json" > "$temp_file"
    /bin/mv "$temp_file" "$order_dir/samples.json"
  done
  set_step "$order_dir" previews_ready
}

upload_samples() {
  local order_dir key order_id token cover position index title source_url preview response
  local -a args
  order_dir=$(current_order_dir)
  key=$(automation_key)
  order_id=$("$jq_bin" -er '.order_id' "$order_dir/claim.json")
  token=$("$jq_bin" -er '.claim_token' "$order_dir/claim.json")
  cover="$order_dir/cover.png"
  [[ -s "$cover" ]] || die "Gedeelde cover ontbreekt."
  "$jq_bin" -e 'length == 4 and all(.suno_source_url and .preview_path)' "$order_dir/samples.json" >/dev/null ||
    die "Samples zijn nog niet compleet."

  args=(-fsS --max-time 180 -X POST \
    -H "X-Automation-Key: $key" \
    -H "X-Claim-Token: $token" \
    -F "cover=@$cover;type=image/png")
  for position in 1 2 3 4; do
    index=$((position - 1))
    title=$("$jq_bin" -er --argjson position "$position" '.[] | select(.position == $position) | .title' "$order_dir/samples.json")
    source_url=$("$jq_bin" -er --argjson position "$position" '.[] | select(.position == $position) | .suno_source_url' "$order_dir/samples.json")
    preview=$("$jq_bin" -er --argjson position "$position" '.[] | select(.position == $position) | .preview_path' "$order_dir/samples.json")
    args+=(
      -F "samples[$index][position]=$position"
      -F "samples[$index][title]=$title"
      -F "samples[$index][suno_source_url]=$source_url"
      -F "samples[$index][preview]=@$preview;type=audio/mpeg"
    )
  done
  response=$("$curl_bin" "${args[@]}" "$api_base/automation/orders/$order_id/samples")
  print -rn -- "$response" | "$jq_bin" -e '.data.automation_status == "completed" and (.data.samples | length == 4)' >/dev/null ||
    die "API heeft de vier samples niet bevestigd."
  print -rn -- "$response" | "$jq_bin" . > "$order_dir/upload-response.json"
  set_step "$order_dir" completed
  log_line "$order_dir" "vier samples geupload; mailtaak aangemaakt"
}

doctor() {
  require_bin "$curl_bin"
  require_bin "$jq_bin"
  require_bin "$ffmpeg_bin"
  require_bin "$ffprobe_bin"
  [[ -d "$downloads_dir" ]] || die "Downloads-map ontbreekt: $downloads_dir"
  print -r -- "API, jq, curl, ffmpeg, ffprobe en Downloads zijn beschikbaar."
}

command=${1:-}
shift || true
case "$command" in
  doctor) doctor ;;
  configure-key) configure_key "$@" ;;
  claim-url) claim_url "$@" ;;
  current-order-dir) current_order_dir ;;
  value) value "$@" ;;
  stage) stage ;;
  set-step) set_step "$(current_order_dir)" "$@" ;;
  sample-value) sample_value "$@" ;;
  generate-cover) generate_cover "$@" ;;
  record-link) record_link "$@" ;;
  mark-download) mark_download "$@" ;;
  capture-download) capture_download "$@" ;;
  make-previews) make_previews ;;
  upload) upload_samples ;;
  *) die "Onbekend commando: $command" ;;
esac
