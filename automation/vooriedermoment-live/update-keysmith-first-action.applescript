use scripting additions

property macroTitle : "Voor Ieder Moment — live aanvragen"
property launcherPath : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/keysmith-launcher.applescript"

on readUTF8(pathValue)
	set fileRef to open for access POSIX file pathValue
	try
		set fileText to read fileRef as «class utf8»
		close access fileRef
		return fileText
	on error errMsg number errNumber
		try
			close access fileRef
		end try
		error errMsg number errNumber
	end try
end readUTF8

on replaceText(sourceText, searchText, replacementText)
	set savedDelimiters to AppleScript's text item delimiters
	set AppleScript's text item delimiters to searchText
	set sourceItems to text items of sourceText
	set AppleScript's text item delimiters to replacementText
	set replacedText to sourceItems as text
	set AppleScript's text item delimiters to savedDelimiters
	return replacedText
end replaceText

on quotedPropertyValue(scriptText, propertyName)
	repeat with scriptLine in paragraphs of scriptText
		set lineText to scriptLine as text
		if lineText contains ("property " & propertyName) then
			set savedDelimiters to AppleScript's text item delimiters
			set AppleScript's text item delimiters to quote
			set lineParts to text items of lineText
			set AppleScript's text item delimiters to savedDelimiters
			if (count of lineParts) ≥ 3 then return item 2 of lineParts
		end if
	end repeat
	error "Property ontbreekt in de bestaande eerste actie: " & propertyName
end quotedPropertyValue

on firstCodeText(rootElement)
	tell application "System Events" to tell process "Keysmith"
		return static text 1 of group 2 of rootElement
	end tell
end firstCodeText

tell application "Keysmith" to activate
delay 0.5

set previousClipboard to the clipboard
tell application "System Events" to tell process "Keysmith"
	set wantedRow to missing value
	repeat with rowItem in rows of outline 1 of scroll area 1 of group 1 of window 1
		try
			if (value of static text 1 of UI element 1 of rowItem as text) is macroTitle then set wantedRow to rowItem
		end try
	end repeat
	if wantedRow is missing value then error "Macro niet gevonden: " & macroTitle
	click wantedRow
	delay 1

	set actionScroll to scroll area 1 of group 1 of group 1 of group 1 of window 1
	repeat with scrollBarItem in scroll bars of actionScroll
		try
			if orientation of scrollBarItem is "AXVerticalOrientation" then set value of scrollBarItem to 0
		end try
	end repeat
	delay 0.4
	set rootElement to UI element 1 of actionScroll
	set codeText to my firstCodeText(rootElement)
	set the clipboard to "VIM_WAITING_FOR_EXISTING_SCRIPT"
	click codeText
	keystroke "a" using command down
	keystroke "c" using command down
end tell
delay 0.8

set existingScript to the clipboard as text
if existingScript is "VIM_WAITING_FOR_EXISTING_SCRIPT" then error "Bestaande eerste actie kon niet worden gekopieerd."
set automationKey to my quotedPropertyValue(existingScript, "automationApiKey")
set openAIKey to my quotedPropertyValue(existingScript, "chatGPTapiKey")
if automationKey is "" then error "Bestaande automationApiKey is leeg."
if openAIKey is "" or openAIKey starts with "PLAK_HIER" then error "Bestaande chatGPTapiKey is leeg."

set launcherText to my readUTF8(launcherPath)
set launcherText to my replaceText(launcherText, "property automationApiKey : \"vooriedermoment-pw\"", "property automationApiKey : \"" & automationKey & "\"")
set launcherText to my replaceText(launcherText, "property chatGPTapiKey : \"PLAK_HIER_JE_OPENAI_API_KEY\"", "property chatGPTapiKey : \"" & openAIKey & "\"")

set the clipboard to launcherText
tell application "System Events" to tell process "Keysmith"
	set actionScroll to scroll area 1 of group 1 of group 1 of group 1 of window 1
	set rootElement to UI element 1 of actionScroll
	set codeText to my firstCodeText(rootElement)
	click codeText
	keystroke "a" using command down
	keystroke "v" using command down
end tell
delay 0.8

set the clipboard to "VIM_VERIFY_SENTINEL"
tell application "System Events" to tell process "Keysmith"
	set actionScroll to scroll area 1 of group 1 of group 1 of group 1 of window 1
	set rootElement to UI element 1 of actionScroll
	set codeText to my firstCodeText(rootElement)
	click codeText
	keystroke "a" using command down
	keystroke "c" using command down
end tell
delay 0.4
set savedScript to the clipboard as text
set the clipboard to previousClipboard

if savedScript does not contain "workflow.applescript" then error "Nieuwe launcher is niet opgeslagen."
if savedScript contains "PLAK_HIER_JE_OPENAI_API_KEY" then error "OpenAI-key is niet behouden."
if savedScript does not contain "error number -128" then error "Stopbeveiliging voor oude vervolgstappen ontbreekt."

return "Eerste actie vervangen; lokale keys behouden; oude vervolgstappen worden geblokkeerd."
