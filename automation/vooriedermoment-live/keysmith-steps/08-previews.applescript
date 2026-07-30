use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"

try
	do shell script ((quoted form of runner) & " previews")
	do shell script ((quoted form of runner) & " log " & quoted form of "STAP 8 vier previews 00:50–01:05 gemaakt")
	display notification "Vier previews van 15 seconden gemaakt." with title "VIM — stap 8/10"
on error errMsg
	try
		do shell script ((quoted form of runner) & " log " & quoted form of ("FOUT stap 8 previews: " & errMsg))
		do shell script ((quoted form of runner) & " fail-current " & quoted form of errMsg)
		do shell script ((quoted form of runner) & " compat-stop")
	end try
	display dialog "Stap 8 (previews maken) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	error number -128
end try
