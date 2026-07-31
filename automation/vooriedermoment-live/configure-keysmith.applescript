use scripting additions

property updaterPath : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/update-keysmith-first-action.applescript"

-- Bewust geen acties toevoegen: vervang alleen de bestaande eerste actie en
-- behoud de twee lokale keys die daar al in staan.
set updateResult to do shell script "/usr/bin/osascript " & quoted form of updaterPath
display notification updateResult with title "VIM — Keysmith bijgewerkt"
return updateResult
