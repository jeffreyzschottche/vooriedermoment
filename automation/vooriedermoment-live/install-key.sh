#!/bin/zsh
set -euo pipefail

service='vooriedermoment-automation'
account='studio-mac'

printf 'AUTOMATION_API_KEY (invoer blijft verborgen): '
IFS= read -rs automation_key
printf '\n'

if [[ -z "$automation_key" ]]; then
  printf 'Geen key ingevoerd.\n' >&2
  exit 1
fi

/usr/bin/security add-generic-password \
  -U \
  -s "$service" \
  -a "$account" \
  -w "$automation_key" >/dev/null

unset automation_key
printf 'Automation-key veilig opgeslagen in macOS Sleutelhanger.\n'
