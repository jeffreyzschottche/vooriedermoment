use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"

try
	do shell script ((quoted form of runner) & " validate")
	do shell script ((quoted form of runner) & " log " & quoted form of "STAP 7 vier downloads en covers gecontroleerd")
	display notification "Vier nummers en covers gevonden." with title "VIM — stap 7/10"
on error errMsg
	try
		do shell script ((quoted form of runner) & " log " & quoted form of ("FOUT stap 7 downloads: " & errMsg))
		do shell script ((quoted form of runner) & " fail-current " & quoted form of errMsg)
		do shell script ((quoted form of runner) & " compat-stop")
	end try
	display dialog "Stap 7 (downloads controleren) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	error number -128
end try
