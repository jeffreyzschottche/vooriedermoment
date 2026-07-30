use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"
property thisMacro : "keysmith://run-macro/1CDF4E2F-3D98-48CE-9E96-87E40B0B40F2"

do shell script ((quoted form of runner) & " log " & quoted form of "STAP 10 volgende order opvragen")
delay 1
open location thisMacro
