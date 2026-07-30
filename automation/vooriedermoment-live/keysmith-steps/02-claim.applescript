use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"

try
	set claimJSON to do shell script ((quoted form of runner) & " claim")
	set queueEmpty to do shell script ("/bin/echo " & quoted form of claimJSON & " | /usr/bin/jq -r '.empty'")
	if queueEmpty is "true" then
		do shell script ((quoted form of runner) & " log " & quoted form of "RUN KLAAR — wachtrij leeg")
		display notification "Alle aanvragen zijn behandeld." with title "VIM — klaar"
		error number -128
	end if
	set orderID to do shell script ("/bin/echo " & quoted form of claimJSON & " | /usr/bin/jq -r '.order_id'")
	do shell script ((quoted form of runner) & " log " & quoted form of ("STAP 2 order " & orderID & " geclaimd"))
	display notification "Order " & orderID & " geclaimd." with title "VIM — stap 2/10"
on error errMsg number errNumber
	if errNumber is not -128 then
		try
			do shell script ((quoted form of runner) & " log " & quoted form of ("FOUT stap 2 claim: " & errMsg))
		end try
		display dialog "Stap 2 (order claimen) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	end if
	error number -128
end try
