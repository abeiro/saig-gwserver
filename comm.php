<?php
error_reporting(E_ERROR);
require_once("lib/sql.class.php");
$db = new sql();


while (@ob_end_clean())
	;
ignore_user_abort(true);
set_time_limit(1200);

ob_start();
/* Send reponse */
// Dequeue message and send +		action	"AASPGDialogueHerika1WhatTopic"	std::string

$responseDataMl = $db->dequeue();
foreach ($responseDataMl as $responseData)
	echo "{$responseData["actor"]}|{$responseData["action"]}|{$responseData["text"]}\r\n";

// Fake Close conection asap

header('Content-Encoding: none');
header('Content-Length: ' . ob_get_length());
header('Connection: close');

ob_end_flush();
ob_flush();
flush();

// Log here (we can be slower)


function parseResponse($responseText, $forceMood = "")
{

	global $db;
	preg_match_all('/\((.*?)\)/', $responseText, $matches);
	$responseTextUnmooded = preg_replace('/\((.*?)\)/', '', $responseText);
	if ($forceMood) {
		$mood = $forceMood;
	} else
		$mood = $matches[1][0];

	$responseText=$responseTextUnmooded;

	if ($GLOBALS["TTSFUNCTION"] == "azure") {
		if ($GLOBALS["AZURE_API_KEY"]) {
			require_once("tts/tts-azure.php");
			tts($responseTextUnmooded, $mood, $responseText);
		}
	}

	if ($GLOBALS["TTSFUNCTION"] == "mimic3") {
		if ($GLOBALS["MIMIC3"]) {
			require_once("tts/tts-mimic3.php");
			ttsMimic($responseTextUnmooded, $mood, $responseText);
		}
	}

	$responseDataMl = $db->dequeue();
	foreach ($responseDataMl as $responseData)
		echo "{$responseData["actor"]}|{$responseData["action"]}|$responseText\r\n";


}

try {
	$finalData = base64_decode(stripslashes($_GET["DATA"]));
	$finalParsedData = explode("|", $finalData);
	foreach ($finalParsedData as $i => $ele)
		$finalParsedData[$i] = trim(preg_replace('/\s\s+/', ' ', preg_replace('/\'/m', "''", $ele)));


	if ($finalParsedData[0] == "init") { // Reset reponses if init sent (Think about this)
		$db->delete("eventlog", "gamets>{$finalParsedData[2]}  ");
		//die(print_r($finalParsedData,true));
		$db->update("responselog", "sent=0", "sent=1 and (action='AASPGDialogueHerika2Branch1Topic')");
		$db->insert(
			'eventlog',
			array(
				'ts' => $finalParsedData[1],
				'gamets' => $finalParsedData[2],
				'type' => $finalParsedData[0],
				'data' => $finalParsedData[3],
				'sess' => 'pending',
				'localts' => time()
			)
		);
	} else if ($finalParsedData[0] == "request") { // Just requested response
		// Do nothing
	} else // It's an event. Store it
		$db->insert(
			'eventlog',
			array(
				'ts' => $finalParsedData[1],
				'gamets' => $finalParsedData[2],
				'type' => $finalParsedData[0],
				'data' => $finalParsedData[3],
				'sess' => 'pending',
				'localts' => time()
			)
		);

} catch (Exception $e) {
	syslog(LOG_WARNING, $e->getMessage());
}


// Queue for more responses. Be carefull here. This will efective send data to AI Chat.
// AASPGQuestDialogue2Topic1B1Topic will enqueue on ASAP TopicInfo
// AASPGDialogueHerika3Branch1Topic will enqueue on What do you know about this place? TopicInfo
// AASPGQuestDialogue2Topic1B1Topic will enqueue on What we should do now? TopicInfo


if ($finalParsedData[0] == "combatend") {
	require_once("chat/generic.php");
	$GLOBALS["DEBUG_MODE"] = false;
	require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");
	$responseText = requestGeneric($PROMPTS["combatend"][0]);
	parseResponse($responseText);

} else if ($finalParsedData[0] == "location") { // Locations might be cached	
	require_once("chat/generic.php");
	$GLOBALS["DEBUG_MODE"] = false;
	$alreadyGenerated = $db->fetchAll(("select * from responselog where  sent=1 and tag='{$finalParsedData[3]}'"));
	if (sizeof($alreadyGenerated) > 0) {
		$db->update("responselog", "sent=0", "sent=1 and tag='{$finalParsedData[3]}'");
		die();
	}
	//requestGeneric("(Chat as Herika)","What do you think about last events?","AASPGDialogueHerika1WhatTopic");
	//requestGeneric("(Chat as Herika)","What should we do?","AASPGDialogueHerika2Branch1Topic");
	require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");
	requestGeneric($PROMPTS["location"][0], $PROMPTS["location"][1], "AASPGDialogueHerika3Branch1Topic", 2, $finalParsedData[3]);

} else if ($finalParsedData[0] == "book") { // Books should be cached
	require_once("chat/generic.php");
	$GLOBALS["DEBUG_MODE"] = false;
	if (stripos($finalParsedData[3], 'note') !== false) // Avoid notes
		return;
	require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");
	$responseText = requestGeneric($PROMPTS["book"][0], $PROMPTS["book"][1], 'AASPGQuestDialogue2Topic1B1Topic', 1);
	parseResponse($responseText);


} else if ($finalParsedData[0] == "quest") {
	require_once("chat/generic.php");

	preg_match('/"(.*?)"/', $finalParsedData[3], $matches);

	$questName = $matches[1];

	$GLOBALS["DEBUG_MODE"] = false;
	require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");
	requestGeneric($PROMPTS["quest"][0], $PROMPTS["quest"][1], 'AASPGDialogueHerika2Branch1Topic', 5);

} else if ($finalParsedData[0] == "bleedout") {
	require_once("chat/generic.php");
	$GLOBALS["DEBUG_MODE"] = false;
	require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");
	$responseText = requestGeneric($PROMPTS["bleedout"][0], $PROMPTS["bleedout"][1], 'AASPGQuestDialogue2Topic1B1Topic', 10);
	parseResponse($responseText);

} else if ($finalParsedData[0] == "bored") {
	require_once("chat/generic.php");
	$GLOBALS["DEBUG_MODE"] = false;
	require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");
	$responseText = requestGeneric($PROMPTS["bored"][0], $PROMPTS["bored"][1], 'AASPGQuestDialogue2Topic1B1Topic', 10);
	parseResponse($responseText);



} else if ($finalParsedData[0] == "goodmorning") {
	require_once("chat/generic.php");
	$GLOBALS["DEBUG_MODE"] = false;
	require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");
	$responseText=requestGeneric($PROMPTS["goodmorning"][0], $PROMPTS["goodmorning"][1], 'AASPGQuestDialogue2Topic1B1Topic', 5);
	parseResponse($responseText);
	
} else if ($finalParsedData[0] == "inputtext") { // Highest priority, must return qeuee data
	require_once("chat/generic.php");
	$GLOBALS["DEBUG_MODE"] = false;

	$newString = preg_replace("/^[^:]*:/", "", $finalParsedData[3]); // Work here
	require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");

	$responseText = requestGeneric($PROMPTS["inputtext"][0], $newString, 'AASPGQuestDialogue2Topic1B1Topic', 10);

	parseResponse($responseText);


} else if ($finalParsedData[0] == "inputtext_s") { // Highest priority, must return qeuee data
	require_once("chat/generic.php");
	$GLOBALS["DEBUG_MODE"] = false;

	$newString = preg_replace("/^[^:]*:/", "", $finalParsedData[3]); // Work here
	require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");
	$responseText = requestGeneric($PROMPTS["inputtext_s"][0], $newString, 'AASPGQuestDialogue2Topic1B1Topic', 10);
	parseResponse($responseText, "whispering");


} else if ($finalParsedData[0] == "lockpicked") {
	require_once("chat/generic.php");

	$GLOBALS["DEBUG_MODE"] = false;
	require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");
	$responseText=requestGeneric($PROMPTS["lockpicked"][0], $PROMPTS["lockpicked"][1], 'AASPGQuestDialogue2Topic1B1Topic', 5);
	parseResponse($responseText, "whispering");
}

?>