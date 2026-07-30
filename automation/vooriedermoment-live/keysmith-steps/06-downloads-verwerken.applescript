use scripting additions

property runner : "/Users/jeffreyzschottche/Sites/vooriedermoment/automation/vooriedermoment-live/runner.sh"
property jq_bin : "/usr/bin/jq"

on findRecentSunoFiles(fileExtension, maxAgeSeconds)
	-- Zoek recente bestanden met gegeven extensie in ~/Downloads
	-- Suno-bestanden hebben vaak "[titel] - [artiest].mp3" of "[titel].mp3" naamgeving
	set shellCmd to "find ~/Downloads -maxdepth 1 -name '*." & fileExtension & "' -type f -mmin -" & ((maxAgeSeconds / 60) as integer) & " -print0 | xargs -0 ls -t 2>/dev/null | head -4"
	try
		set resultText to do shell script shellCmd
		if resultText is "" then return {}
		return paragraphs of resultText
	on error
		return {}
	end try
end findRecentSunoFiles

on extractSunoUrl(filePath)
	-- Probeer Suno-URL uit xattr metadata te halen (sommige browsers slaan origin URL op)
	try
		set whereFrom to do shell script "xattr -p com.apple.metadata:kMDItemWhereFroms " & quoted form of filePath & " 2>/dev/null | xxd -r -p | plutil -convert json -o - - | " & jq_bin & " -r '.[0] // empty' 2>/dev/null"
		if whereFrom contains "suno.com" then return whereFrom
	end try
	-- Fallback: genereer placeholder URL (moet later handmatig ingevuld)
	return "https://suno.com/song/unknown"
end extractSunoUrl

on extractTitle(filePath)
	-- Haal titel uit bestandsnaam (zonder extensie en path)
	set shellCmd to "basename " & quoted form of filePath & " | sed 's/\\.[^.]*$//'"
	return do shell script shellCmd
end extractTitle

on buildSampleJson(position, titleText, audioPath, coverPath, sunoUrl)
	-- Gebruik jq voor correcte JSON-escaping
	set jqCmd to jq_bin & " -cn --argjson pos " & position & " --arg title " & quoted form of titleText & " --arg audio " & quoted form of audioPath & " --arg cover " & quoted form of coverPath & " --arg url " & quoted form of sunoUrl & " '{position:$pos,title:$title,audio_path:$audio,cover_path:$cover,suno_source_url:$url}'"
	return do shell script jqCmd
end buildSampleJson

try
	do shell script ((quoted form of runner) & " log " & quoted form of "STAP 6 downloads verwerken gestart")

	-- Haal huidige ordermap op
	set orderDir to do shell script (quoted form of runner) & " current"

	-- Zoek naar recente mp3-bestanden (binnen laatste 10 minuten = 600 seconden)
	set mp3Files to my findRecentSunoFiles("mp3", 600)

	if (count of mp3Files) < 4 then
		error "Slechts " & (count of mp3Files) & " mp3-bestanden gevonden in ~/Downloads (4 nodig)"
	end if

	-- Zoek naar recente jpg/png-bestanden voor covers
	set jpgFiles to my findRecentSunoFiles("jpg", 600)
	set pngFiles to my findRecentSunoFiles("png", 600)
	set coverFiles to jpgFiles & pngFiles

	if (count of coverFiles) < 4 then
		error "Slechts " & (count of coverFiles) & " coverbestanden gevonden in ~/Downloads (4 nodig)"
	end if

	-- Bouw samples als lijst van JSON-objecten
	set sampleObjects to {}

	repeat with i from 1 to 4
		set mp3Path to item i of mp3Files
		set coverPath to item i of coverFiles
		set titleText to my extractTitle(mp3Path)
		set sunoUrl to my extractSunoUrl(mp3Path)

		-- Bepaal cover-extensie
		set coverExt to "jpg"
		if coverPath ends with ".png" then set coverExt to "png"

		-- Kopieer en hernoem bestanden
		set targetAudio to orderDir & "/full/" & i & ".mp3"
		set targetCover to orderDir & "/covers/" & i & "." & coverExt

		do shell script "/bin/cp " & quoted form of mp3Path & " " & quoted form of targetAudio
		do shell script "/bin/cp " & quoted form of coverPath & " " & quoted form of targetCover

		-- Bouw JSON-object voor deze sample
		set sampleJson to my buildSampleJson(i, titleText, targetAudio, targetCover, sunoUrl)
		set end of sampleObjects to sampleJson
	end repeat

	-- Combineer alle samples tot een array en schrijf naar bestand
	set samplesFile to orderDir & "/samples.json"
	set joinedSamples to item 1 of sampleObjects
	repeat with i from 2 to 4
		set joinedSamples to joinedSamples & "," & item i of sampleObjects
	end repeat
	set finalJson to "[" & joinedSamples & "]"

	-- Schrijf en valideer JSON
	do shell script "echo " & quoted form of finalJson & " | " & jq_bin & " '.' > " & quoted form of samplesFile

	do shell script ((quoted form of runner) & " log " & quoted form of "STAP 6 vier bestanden verwerkt en samples.json gemaakt")
	display notification "Vier nummers en covers verwerkt." with title "VIM — stap 6/10"

on error errMsg
	try
		do shell script ((quoted form of runner) & " log " & quoted form of ("FOUT stap 6 downloads verwerken: " & errMsg))
		do shell script ((quoted form of runner) & " fail-current " & quoted form of errMsg)
		do shell script ((quoted form of runner) & " compat-stop")
	end try
	display dialog "Stap 6 (downloads verwerken) mislukt:" & return & errMsg buttons {"Stop"} default button "Stop"
	error number -128
end try
