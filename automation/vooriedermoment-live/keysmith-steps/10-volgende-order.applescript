use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"

do shell script ((quoted form of runner) & " log " & quoted form of "RUN KLAAR — open voor de volgende order eerst de admin-uploadpagina")
display notification "Klaar. Open voor de volgende order eerst de juiste admin-uploadpagina." with title "VIM — stap 10/10"
