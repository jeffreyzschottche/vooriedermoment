use scripting additions

property helper : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/workflow-helper.sh"
property sunoCreateURL : "https://suno.com/create"
property maxGroupsToVisit : 1400

on shellCommand(commandName, argumentList)
	set commandText to quoted form of helper & " " & quoted form of commandName
	repeat with argumentValue in argumentList
		set commandText to commandText & " " & quoted form of (argumentValue as text)
	end repeat
	set shellResult to do shell script commandText
	return shellResult
end shellCommand

on navigateFirefox(targetURL)
	set previousClipboard to the clipboard
	set the clipboard to targetURL
	tell application "Firefox" to activate
	delay 0.3
	tell application "System Events" to tell process "Firefox"
		keystroke "l" using command down
		keystroke "v" using command down
		key code 36
	end tell
	delay 0.5
	set the clipboard to previousClipboard
end navigateFirefox

on firefoxWebRoot()
	tell application "System Events" to tell process "Firefox"
		return UI element 1 of scroll area 1 of group 1 of group 1 of group 1 of front window
	end tell
end firefoxWebRoot

on textForElement(candidateItem)
	set candidateTexts to {}
	tell application "System Events"
		try
			set end of candidateTexts to name of candidateItem as text
		end try
		try
			set end of candidateTexts to description of candidateItem as text
		end try
		try
			set end of candidateTexts to value of candidateItem as text
		end try
	end tell
	return candidateTexts
end textForElement

on elementMatches(candidateItem, wantedRole, wantedText, containsText)
	tell application "System Events"
		try
			if wantedRole is not "" and (role of candidateItem as text) is not wantedRole then return false
			repeat with candidateText in my textForElement(candidateItem)
				if containsText then
					if (candidateText as text) contains wantedText then return true
				else
					if (candidateText as text) is wantedText then return true
				end if
			end repeat
		end try
	end tell
	return false
end elementMatches

on findElement(wantedRole, wantedText, containsText)
	tell application "System Events" to tell process "Firefox"
		set webRoot to my firefoxWebRoot()
		set pendingGroups to {webRoot}
		set visitedCount to 0
		repeat while (count of pendingGroups) > 0 and visitedCount < maxGroupsToVisit
			set candidateGroup to item 1 of pendingGroups
			if (count of pendingGroups) is 1 then
				set pendingGroups to {}
			else
				set pendingGroups to items 2 thru -1 of pendingGroups
			end if
			set visitedCount to visitedCount + 1

			repeat with candidateItem in UI elements of candidateGroup
				if my elementMatches(candidateItem, wantedRole, wantedText, containsText) then return candidateItem
			end repeat

			try
				set childGroups to groups of candidateGroup
				if (count of childGroups) > 0 then set pendingGroups to childGroups & pendingGroups
			end try
		end repeat
	end tell
	return missing value
end findElement

on waitForElement(wantedRole, wantedText, containsText, timeoutSeconds)
	repeat timeoutSeconds * 2 times
		set foundElement to my findElement(wantedRole, wantedText, containsText)
		if foundElement is not missing value then return foundElement
		delay 0.5
	end repeat
	error "Suno-element niet gevonden: " & wantedText
end waitForElement

on pressElement(targetElement)
	tell application "System Events" to tell process "Firefox"
		try
			perform action "AXScrollToVisible" of targetElement
		end try
		delay 0.15
		perform action "AXPress" of targetElement
	end tell
end pressElement

on fillElement(targetElement, fieldValue)
	set previousClipboard to the clipboard
	set the clipboard to fieldValue
	tell application "System Events" to tell process "Firefox"
		try
			perform action "AXScrollToVisible" of targetElement
		end try
		set focused of targetElement to true
		key code 0 using command down
		keystroke "v" using command down
	end tell
	delay 0.25
	set the clipboard to previousClipboard
end fillElement

on findFieldBelow(labelText, wantedRole, maxDistance)
	set labelElement to my findElement("AXButton", labelText, false)
	if labelElement is missing value then return missing value
	tell application "System Events" to tell process "Firefox"
		set {labelX, labelY} to position of labelElement
		set webRoot to my firefoxWebRoot()
		set pendingGroups to {webRoot}
		set visitedCount to 0
		set bestField to missing value
		set bestDistance to 100000
		repeat while (count of pendingGroups) > 0 and visitedCount < maxGroupsToVisit
			set candidateGroup to item 1 of pendingGroups
			if (count of pendingGroups) is 1 then
				set pendingGroups to {}
			else
				set pendingGroups to items 2 thru -1 of pendingGroups
			end if
			set visitedCount to visitedCount + 1
			repeat with candidateItem in UI elements of candidateGroup
				try
					if (role of candidateItem as text) is wantedRole then
						set {fieldX, fieldY} to position of candidateItem
						set fieldDistance to fieldY - labelY
						if fieldDistance ≥ 0 and fieldDistance ≤ maxDistance and fieldDistance < bestDistance and fieldX ≥ (labelX - 80) then
							set bestField to candidateItem
							set bestDistance to fieldDistance
						end if
					end if
				end try
			end repeat
			try
				set childGroups to groups of candidateGroup
				if (count of childGroups) > 0 then set pendingGroups to childGroups & pendingGroups
			end try
		end repeat
		return bestField
	end tell
end findFieldBelow

on countMatchingElements(wantedRole, wantedText, containsText)
	set matchCount to 0
	tell application "System Events" to tell process "Firefox"
		set webRoot to my firefoxWebRoot()
		set pendingGroups to {webRoot}
		set visitedCount to 0
		repeat while (count of pendingGroups) > 0 and visitedCount < maxGroupsToVisit
			set candidateGroup to item 1 of pendingGroups
			if (count of pendingGroups) is 1 then
				set pendingGroups to {}
			else
				set pendingGroups to items 2 thru -1 of pendingGroups
			end if
			set visitedCount to visitedCount + 1
			repeat with candidateItem in UI elements of candidateGroup
				if my elementMatches(candidateItem, wantedRole, wantedText, containsText) then set matchCount to matchCount + 1
			end repeat
			try
				set childGroups to groups of candidateGroup
				if (count of childGroups) > 0 then set pendingGroups to childGroups & pendingGroups
			end try
		end repeat
	end tell
	return matchCount
end countMatchingElements

on resultMenuButtonAt(resultIndex)
	set resultElements to {}
	set resultKeys to {}
	tell application "System Events" to tell process "Firefox"
		set webRoot to my firefoxWebRoot()
		set pendingGroups to {webRoot}
		set visitedCount to 0
		repeat while (count of pendingGroups) > 0 and visitedCount < maxGroupsToVisit
			set candidateGroup to item 1 of pendingGroups
			if (count of pendingGroups) is 1 then
				set pendingGroups to {}
			else
				set pendingGroups to items 2 thru -1 of pendingGroups
			end if
			set visitedCount to visitedCount + 1
			repeat with candidateItem in UI elements of candidateGroup
				if my elementMatches(candidateItem, "AXButton", "More options", false) then
					try
						set {itemX, itemY} to position of candidateItem
						set end of resultElements to candidateItem
						set end of resultKeys to (itemY * 10000 + itemX)
					end try
				end if
			end repeat
			try
				set childGroups to groups of candidateGroup
				if (count of childGroups) > 0 then set pendingGroups to childGroups & pendingGroups
			end try
		end repeat
	end tell

	if (count of resultElements) < resultIndex then error "Suno-resultaat " & resultIndex & " is niet gevonden."
	repeat with selectedNumber from 1 to resultIndex
		set smallestKey to 100000000
		set smallestIndex to 0
		repeat with candidateIndex from 1 to count of resultKeys
			set candidateKey to item candidateIndex of resultKeys
			if candidateKey is not missing value and candidateKey < smallestKey then
				set smallestKey to candidateKey
				set smallestIndex to candidateIndex
			end if
		end repeat
		if selectedNumber is resultIndex then return item smallestIndex of resultElements
		set item smallestIndex of resultKeys to missing value
	end repeat
	error "Suno-resultaat kon niet worden gesorteerd."
end resultMenuButtonAt

on ensureAdvancedMode()
	if my findElement("AXTextField", "Lyrics editor", false) is not missing value then return
	set advancedButton to my findElement("AXButton", "Advanced", false)
	if advancedButton is missing value then set advancedButton to my findElement("AXRadioButton", "Advanced", false)
	if advancedButton is missing value then error "Advanced-modus is niet gevonden."
	my pressElement(advancedButton)
	my waitForElement("AXTextField", "Lyrics editor", false, 20)
end ensureAdvancedMode

on fillSunoForm(lyricsText, stylesText, vocalGender, songTitle)
	my ensureAdvancedMode()

	set lyricsField to my findElement("AXTextField", "Lyrics editor", false)
	if lyricsField is missing value then
		my pressElement(my waitForElement("AXButton", "Lyrics", false, 10))
		set lyricsField to my waitForElement("AXTextField", "Lyrics editor", false, 10)
	end if
	my fillElement(lyricsField, lyricsText)

	set stylesField to my findFieldBelow("Styles", "AXTextArea", 260)
	if stylesField is missing value then
		my pressElement(my waitForElement("AXButton", "Styles", false, 10))
		delay 0.5
		set stylesField to my findFieldBelow("Styles", "AXTextArea", 260)
	end if
	if stylesField is missing value then error "Het Suno-veld Styles is niet gevonden."
	my fillElement(stylesField, stylesText)

	if vocalGender is "male" or vocalGender is "female" then
		set maleButton to my findElement("AXButton", "Male", false)
		set femaleButton to my findElement("AXButton", "Female", false)
		if maleButton is missing value and femaleButton is missing value then
			my pressElement(my waitForElement("AXButton", "More Options", false, 10))
			delay 0.4
		end if
		if vocalGender is "male" then
			my pressElement(my waitForElement("AXButton", "Male", false, 10))
		else
			my pressElement(my waitForElement("AXButton", "Female", false, 10))
		end if
	end if

	set titleField to my waitForElement("AXTextField", "Song Title (Optional)", false, 10)
	my fillElement(titleField, songTitle)
end fillSunoForm

on createFourSongs(songTitle)
	set confirmationText to "Suno is ingevuld voor ‘" & songTitle & "’." & return & return & "Na doorgaan klikt de macro twee keer op Create en gebruikt daarmee Suno-credits."
	display dialog confirmationText buttons {"Stop", "Maak 4 nummers"} default button "Maak 4 nummers" cancel button "Stop" with title "VIM — controle vóór Create"

	set createButton to my waitForElement("AXButton", "Create song", false, 15)
	my pressElement(createButton)
	delay 10
	set createButton to my waitForElement("AXButton", "Create song", false, 15)
	my pressElement(createButton)

	-- Suno noemt de vier nieuwe kaarten allemaal naar deze unieke titel.
	delay 150
	repeat 30 times
		-- Het titelveld zelf telt één keer mee; vier resultaatkaarten maken vijf.
		if my countMatchingElements("", songTitle, true) ≥ 5 then return
		delay 10
	end repeat
	error "Na maximaal 7,5 minuut zijn geen vier nieuwe Suno-resultaten met de verwachte titel gevonden."
end createFourSongs

on copySunoLink(resultIndex)
	set previousClipboard to the clipboard
	set the clipboard to "VIM_WAITING_FOR_LINK"
	my pressElement(my resultMenuButtonAt(resultIndex))
	delay 0.5
	my pressElement(my waitForElement("AXButton", "Share", false, 10))
	delay 0.5
	my pressElement(my waitForElement("AXButton", "Copy Link", false, 10))
	delay 0.7
	set sourceURL to the clipboard as text
	if sourceURL does not start with "https://suno.com/" then
		set the clipboard to previousClipboard
		error "Suno-link voor resultaat " & resultIndex & " is niet naar het klembord gekopieerd."
	end if
	set the clipboard to previousClipboard
	return sourceURL
end copySunoLink

on downloadSunoMp3(resultIndex)
	my shellCommand("mark-download", {resultIndex})
	my pressElement(my resultMenuButtonAt(resultIndex))
	delay 0.5
	my pressElement(my waitForElement("AXButton", "Download", false, 10))
	delay 0.5
	my pressElement(my waitForElement("AXButton", "MP3 Audio", false, 10))
	return my shellCommand("capture-download", {resultIndex})
end downloadSunoMp3

on writeSecretFile(secretValue)
	set secretPath to POSIX path of (path to temporary items) & "vim-secret-" & (random number from 100000 to 999999)
	set fileHandle to open for access POSIX file secretPath with write permission
	try
		set eof fileHandle to 0
		write secretValue to fileHandle as «class utf8»
		close access fileHandle
	on error errorText number errorNumber
		try
			close access fileHandle
		end try
		error errorText number errorNumber
	end try
	do shell script "/bin/chmod 600 " & quoted form of secretPath
	return secretPath
end writeSecretFile

on run argv
	if (count of argv) is not 1 then error "Beveiligd configuratiebestand ontbreekt."
	set configPath to item 1 of argv
	set automationApiKey to do shell script "/usr/bin/sed -n '1p' " & quoted form of configPath
	set chatGPTapiKey to do shell script "/usr/bin/sed -n '2p' " & quoted form of configPath
	do shell script "/bin/rm -f " & quoted form of configPath
	if automationApiKey is "" then error "automationApiKey is leeg."
	if chatGPTapiKey is "" then error "chatGPTapiKey is leeg."

	my shellCommand("doctor", {})
	my shellCommand("configure-key", {automationApiKey})
	set orderDir to my shellCommand("claim-next", {})
	set currentStage to my shellCommand("stage", {})
	if currentStage is "completed" then
		display dialog "Deze order is al volledig verwerkt. De opgeslagen Suno-links staan in:" & return & orderDir & "/samples.json" buttons {"OK"} default button "OK" with title "VIM — al klaar"
		return
	end if

	set openAIKeyFile to my writeSecretFile(chatGPTapiKey)
	try
		set coverPath to my shellCommand("generate-cover", {openAIKeyFile})
	on error errorText number errorNumber
		do shell script "/bin/rm -f " & quoted form of openAIKeyFile
		error errorText number errorNumber
	end try
	do shell script "/bin/rm -f " & quoted form of openAIKeyFile

	set lyricsText to my shellCommand("value", {".suno.lyrics"})
	set stylesText to my shellCommand("value", {".suno.style"})
	set vocalGender to my shellCommand("value", {".suno.vocal_gender // \"\""})
	set baseTitle to my shellCommand("value", {".suno.title"})
	set orderId to my shellCommand("value", {".order_id | tostring"})
	set uniqueSongTitle to baseTitle & " [VIM " & orderId & "]"

	my navigateFirefox(sunoCreateURL)
	delay 5
	my waitForElement("AXButton", "Create song", false, 30)
	my fillSunoForm(lyricsText, stylesText, vocalGender, uniqueSongTitle)
	if currentStage is "claimed" or currentStage is "cover_ready" then
		my shellCommand("set-step", {"form_ready"})
		set currentStage to "form_ready"
	end if
	display notification "Lyrics, stijl, titel en stem zijn ingevuld. Cover staat klaar." with title "VIM — stappen 1 t/m 6"

	if currentStage is "form_ready" then
		my createFourSongs(uniqueSongTitle)
		my shellCommand("set-step", {"songs_ready"})
		set currentStage to "songs_ready"
	end if

	repeat with resultIndex from 1 to 4
		set storedURL to my shellCommand("sample-value", {resultIndex, "suno_source_url"})
		if storedURL is "" then
			set sourceURL to my copySunoLink(resultIndex)
			my shellCommand("record-link", {resultIndex, sourceURL})
		end if
	end repeat
	my shellCommand("set-step", {"links_ready"})

	repeat with resultIndex from 1 to 4
		set storedAudioPath to my shellCommand("sample-value", {resultIndex, "audio_path"})
		if storedAudioPath is "" then my downloadSunoMp3(resultIndex)
	end repeat
	my shellCommand("set-step", {"downloads_ready"})

	my shellCommand("make-previews", {})
	my shellCommand("upload", {})
	display dialog "Order " & orderId & " is afgerond." & return & return & "Vier Suno-links zijn lokaal én in de API opgeslagen, previews 00:30–00:45 zijn geüpload en de klantmail is klaargezet." & return & return & "Ordermap: " & orderDir buttons {"OK"} default button "OK" with title "VIM — klaar"
end run
