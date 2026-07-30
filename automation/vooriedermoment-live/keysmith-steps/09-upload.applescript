use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"

try
	do shell script ((quoted form of runner) & " upload")
	do shell script ((quoted form of runner) & " compat-stop")
	do shell script ((quoted form of runner) & " log " & quoted form of "STAP 9 upload geslaagd; klantmail staat in backend-queue")
	display notification "Samples geüpload; klantmail staat klaar." with title "VIM — stap 9/10"
on error errMsg
	try
		do shell script ((quoted form of runner) & " log " & quoted form of ("FOUT stap 9 upload: " & errMsg))
		do shell script ((quoted form of runner) & " fail-current " & quoted form of errMsg)
		do shell script ((quoted form of runner) & " compat-stop")
	end try
	display dialog "Stap 9 (upload) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	error number -128
end try
