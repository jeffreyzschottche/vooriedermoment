use scripting additions

property macroTitle : "Voor Ieder Moment — live aanvragen"
property stepsDir : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/keysmith-steps/"

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

on macroRoot()
	tell application "System Events" to tell process "Keysmith"
		return UI element 1 of scroll area 1 of group 1 of group 1 of group 1 of window 1
	end tell
end macroRoot

on pasteInto(editorArea, scriptText)
	set the clipboard to scriptText
	tell application "System Events" to tell process "Keysmith"
		click editorArea
		keystroke "a" using command down
		keystroke "v" using command down
		delay 0.2
		if (length of (value of editorArea as text)) is 0 then error "Keysmith-editor bleef leeg."
	end tell
end pasteInto

on appendAppleScript(fileName)
	tell application "System Events" to tell process "Keysmith"
		click menu item "Run AppleScript" of menu 1 of menu item "Add Action" of menu 1 of menu bar item "Edit" of menu bar 1
		delay 0.15
	end tell
	set rootEl to macroRoot()
	tell application "System Events" to tell process "Keysmith"
		set editorArea to text area 1 of last group of rootEl
	end tell
	pasteInto(editorArea, readUTF8(stepsDir & fileName))
end appendAppleScript

tell application "Keysmith" to activate
delay 0.4

tell application "System Events" to tell process "Keysmith"
	try
		click menu item "Stop Recording" of menu 1 of menu bar item "File" of menu bar 1
		delay 0.4
	end try
	set wantedRow to missing value
	repeat with rowItem in rows of outline 1 of scroll area 1 of group 1 of window 1
		try
			if (value of static text 1 of UI element 1 of rowItem as text) is macroTitle then set wantedRow to rowItem
		end try
	end repeat
	if wantedRow is missing value then error "Macro niet gevonden: " & macroTitle
	click wantedRow
	delay 0.3
end tell

appendAppleScript("01-controle.applescript")
appendAppleScript("02-claim.applescript")
appendAppleScript("03-compat-server.applescript")
appendAppleScript("04-suno-start.applescript")
appendAppleScript("05-wachten-op-suno.applescript")
appendAppleScript("06-downloads-verwerken.applescript")
appendAppleScript("07-controle-downloads.applescript")
appendAppleScript("08-previews.applescript")
appendAppleScript("09-upload.applescript")
appendAppleScript("10-volgende-order.applescript")

tell application "System Events" to tell process "Keysmith"
	set rootEl to my macroRoot()
	return "10 AppleScript-acties opgebouwd; groepen=" & (count groups of rootEl)
end tell
