use scripting additions

property automationApiKey : "vooriedermoment-pw"
property chatGPTapiKey : "PLAK_HIER_JE_OPENAI_API_KEY"
property workflowScript : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/workflow.applescript"

on writePrivateConfig(automationKey, openAIKey)
	set configPath to POSIX path of (path to temporary items) & "vim-keysmith-" & (random number from 100000 to 999999)
	set fileHandle to open for access POSIX file configPath with write permission
	try
		set eof fileHandle to 0
		write (automationKey & linefeed & openAIKey & linefeed) to fileHandle as «class utf8»
		close access fileHandle
	on error errorText number errorNumber
		try
			close access fileHandle
		end try
		error errorText number errorNumber
	end try
	do shell script "/bin/chmod 600 " & quoted form of configPath
	return configPath
end writePrivateConfig

try
	if automationApiKey is "" then error "automationApiKey is leeg."
	if chatGPTapiKey is "" or chatGPTapiKey starts with "PLAK_HIER" then error "Vul chatGPTapiKey bovenaan deze Keysmith-stap in."
	set configPath to my writePrivateConfig(automationApiKey, chatGPTapiKey)
	do shell script "/usr/bin/osascript " & quoted form of workflowScript & " " & quoted form of configPath
on error errMsg number errNum
	try
		if configPath is not missing value then do shell script "/bin/rm -f " & quoted form of configPath
	end try
	if errNum is -128 then error number -128
	display dialog "Voor Ieder Moment-run gestopt:" & return & errMsg buttons {"Stop"} default button "Stop"
	error number -128
end try

-- Deze launcher vervangt de oude tien acties; voorkom dat oude vervolgstappen draaien.
error number -128
