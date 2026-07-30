use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"
property automationApiKey : "vooriedermoment-pw"

try
	do shell script ((quoted form of runner) & " configure-key " & (quoted form of automationApiKey))
	do shell script ((quoted form of runner) & " log " & (quoted form of "RUN START"))
	set resultText to do shell script ((quoted form of runner) & " doctor")
	display notification resultText with title "VIM — stap 1/10"
on error errMsg
	try
		do shell script ((quoted form of runner) & " log " & (quoted form of ("FOUT stap 1 controle: " & errMsg)))
	end try
	display dialog "Stap 1 (controle) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	error number -128
end try
