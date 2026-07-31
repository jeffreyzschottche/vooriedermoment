use scripting additions

property automationApiKey : "vooriedermoment-pw"
property apiBase : "https://api.vooriedermoment.nl/api/v1"
property ordersDir : "/Users/jeffreyzschottche/Desktop/vooriedermoment-live-aanvragen"
property workerId : "studio-mac"
property curlBin : "/usr/bin/curl"
property jqBin : "/usr/bin/jq"

on activeFirefoxURL()
	set previousClipboard to the clipboard
	tell application "Firefox" to activate
	delay 0.3
	tell application "System Events" to tell process "Firefox"
		keystroke "l" using command down
		delay 0.1
		keystroke "c" using command down
	end tell
	delay 0.2
	set pageURL to the clipboard as text
	set the clipboard to previousClipboard
	return pageURL
end activeFirefoxURL

on jsonValue(jsonText, jqQuery)
	set commandText to (my jqBin) & " -er " & (quoted form of jqQuery) & " <<< " & (quoted form of jsonText)
	set parsedValue to do shell script commandText
	return parsedValue
end jsonValue

on writeJson(jsonText, jqQuery, targetPath)
	set commandText to "/usr/bin/printf %s " & (quoted form of jsonText) & " | " & (my jqBin) & " " & (quoted form of jqQuery) & " > " & (quoted form of targetPath)
	do shell script commandText
end writeJson

on slugify(valueText)
	set slugValue to do shell script ("/usr/bin/printf %s " & quoted form of valueText & " | /usr/bin/iconv -f UTF-8 -t ASCII//TRANSLIT 2>/dev/null | /usr/bin/tr '[:upper:]' '[:lower:]' | /usr/bin/sed -E 's/[^a-z0-9]+/-/g; s/^-+|-+$//g; s/-+/-/g'")
	return slugValue
end slugify

on logMessage(messageText)
	do shell script ("/bin/mkdir -p " & quoted form of ordersDir)
	do shell script ("/usr/bin/printf '%s %s\\n' \"$(/bin/date -u '+%Y-%m-%dT%H:%M:%SZ')\" " & quoted form of messageText & " >> " & quoted form of (ordersDir & "/macro.log"))
end logMessage

try
	if automationApiKey is "" then error "automationApiKey is leeg."

	set pageURL to my activeFirefoxURL()
	set uploadToken to do shell script ("/usr/bin/printf %s " & quoted form of pageURL & " | /usr/bin/sed -nE 's|^https?://api\\.vooriedermoment\\.nl/admin/upload/([A-Za-z0-9]+)([/?#].*)?$|\\1|p'")
	if uploadToken is "" then error "Open eerst de juiste https://api.vooriedermoment.nl/admin/upload/... pagina in Firefox. Gevonden URL: " & pageURL

	set stateDir to ordersDir & "/.runner"
	do shell script ("/bin/mkdir -p " & quoted form of stateDir)
	do shell script ("/usr/bin/printf %s " & quoted form of automationApiKey & " > " & quoted form of (stateDir & "/automation-key"))
	do shell script ("/bin/chmod 600 " & quoted form of (stateDir & "/automation-key"))

	my logMessage("RUN START — upload-token " & uploadToken)
	set payload to do shell script (jqBin & " -cn --arg worker_id " & quoted form of workerId & " --arg admin_upload_token " & quoted form of uploadToken & " '{worker_id:$worker_id,admin_upload_token:$admin_upload_token}'")
	set responseJSON to do shell script (curlBin & " -fsS -X POST -H " & quoted form of ("X-Automation-Key: " & automationApiKey) & " -H 'Content-Type: application/json' --data " & quoted form of payload & " " & quoted form of (apiBase & "/automation/orders/claim"))

	set hasOrder to do shell script (jqBin & " -r 'if .data == null then \"false\" else \"true\" end' <<< " & quoted form of responseJSON)
	if hasOrder is not "true" then error "Deze order is niet klaar om te claimen, is al actief, of is al verwerkt."

	set orderId to my jsonValue(responseJSON, ".data.order.order_id")
	set categoryName to my jsonValue(responseJSON, ".data.order.category // \"aanvraag\"")
	set recipientName to my jsonValue(responseJSON, ".data.order.recipient_name // \"ontvanger\"")
	set folderSlug to my slugify(categoryName & "-voor-" & recipientName)
	if folderSlug is "" then set folderSlug to "aanvraag"
	set orderDir to ordersDir & "/order-" & orderId & "-" & folderSlug

	do shell script ("/bin/mkdir -p " & quoted form of (orderDir & "/full") & " " & quoted form of (orderDir & "/covers") & " " & quoted form of (orderDir & "/previews"))
	my writeJson(responseJSON, ".data.order", orderDir & "/order.json")
	my writeJson(responseJSON, "{order_id:.data.order.order_id,claim_token:.data.claim_token,claimed_by:.data.claimed_by,claim_expires_at:.data.claim_expires_at}", orderDir & "/claim.json")
	do shell script ("/bin/cp " & quoted form of (orderDir & "/order.json") & " " & quoted form of (ordersDir & "/current.json"))
	do shell script ("/usr/bin/printf '%s\\n' " & quoted form of orderDir & " > " & quoted form of (stateDir & "/current-order-dir.txt"))
	do shell script (jqBin & " -cn --arg step claimed --arg updated_at \"$(/bin/date -u '+%Y-%m-%dT%H:%M:%SZ')\" '{step:$step,updated_at:$updated_at}' > " & quoted form of (orderDir & "/status.json"))
	my logMessage("STAP 1 order " & orderId & " opgehaald uit " & pageURL)
	display notification "Order " & orderId & " opgehaald voor " & recipientName & "." with title "VIM — stap 1/10"
on error errMsg
	try
		my logMessage("FOUT stap 1 order ophalen: " & errMsg)
	end try
	display dialog "Stap 1 (geopende order ophalen) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	error number -128
end try
