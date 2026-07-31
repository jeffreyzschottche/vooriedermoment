use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"

try
	set orderDir to do shell script ((quoted form of runner) & " current")
	set orderID to do shell script ("/usr/bin/jq -er '.order_id' " & quoted form of (orderDir & "/order.json"))
	do shell script ((quoted form of runner) & " log " & quoted form of ("STAP 2 order " & orderID & " lokaal gecontroleerd"))
	display notification "Order " & orderID & " staat lokaal klaar." with title "VIM — stap 2/10"
on error errMsg number errNumber
	if errNumber is not -128 then
		try
			do shell script ((quoted form of runner) & " log " & quoted form of ("FOUT stap 2 claim: " & errMsg))
		end try
		display dialog "Stap 2 (lokale order controleren) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	end if
	error number -128
end try
