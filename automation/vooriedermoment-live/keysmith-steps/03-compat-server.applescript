use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"

try
	do shell script ((quoted form of runner) & " compat-start")
	do shell script ((quoted form of runner) & " log " & quoted form of "STAP 3 lokale VIM-bridge actief")
on error errMsg
	try
		do shell script ((quoted form of runner) & " log " & quoted form of ("FOUT stap 3 VIM-bridge: " & errMsg))
		do shell script ((quoted form of runner) & " fail-current " & quoted form of errMsg)
	end try
	display dialog "Stap 3 (lokale VIM-bridge) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	error number -128
end try
