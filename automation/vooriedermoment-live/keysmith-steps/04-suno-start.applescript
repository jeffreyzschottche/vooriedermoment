use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"
property vimMacro : "keysmith://run-macro/BB8095DE-6073-4538-8ABE-12E602A89612"

try
	do shell script ((quoted form of runner) & " log " & quoted form of "STAP 4 Firefox/Suno macro gestart")
	open location vimMacro
on error errMsg
	try
		do shell script ((quoted form of runner) & " log " & quoted form of ("FOUT stap 4 Suno starten: " & errMsg))
		do shell script ((quoted form of runner) & " fail-current " & quoted form of errMsg)
		do shell script ((quoted form of runner) & " compat-stop")
	end try
	display dialog "Stap 4 (Suno starten) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	error number -128
end try
