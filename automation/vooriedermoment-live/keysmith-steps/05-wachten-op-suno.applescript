use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"

try
	do shell script ((quoted form of runner) & " log " & quoted form of "STAP 5 wachten op Suno-generatie (180s)")
	display notification "Wachten tot vier nummers klaar zijn..." with title "VIM — stap 5/10"
	delay 180
	do shell script ((quoted form of runner) & " log " & quoted form of "STAP 5 wachttijd verstreken")
on error errMsg
	try
		do shell script ((quoted form of runner) & " log " & quoted form of ("FOUT stap 5 wachten: " & errMsg))
		do shell script ((quoted form of runner) & " fail-current " & quoted form of errMsg)
		do shell script ((quoted form of runner) & " compat-stop")
	end try
	display dialog "Stap 5 (wachten op Suno) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	error number -128
end try
