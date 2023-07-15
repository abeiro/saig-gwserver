<?php
error_reporting(E_ERROR);

define("MAXIMUM_SENTENCE_SIZE", 100);

date_default_timezone_set('Europe/Madrid');

$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR;
require_once($path . "conf.php");
require_once($path . "lib/$DRIVER.class.php");
require_once($path . "lib/Misc.php");
$db = new sql();

while (@ob_end_clean())
	;

ignore_user_abort(true);
set_time_limit(1200);

$startTime = time();

$talkedSoFar = array();
$alreadysent = array();

function findDotPosition($string)
{
	$dotPosition = strrpos($string, ".");

	if ($dotPosition !== false && strpos($string, ".", $dotPosition + 1) === false && substr($string, $dotPosition - 3, 3) !== "...") {
		return $dotPosition;
	}

	return false;
}



function split_sentences_stream($paragraph)
{
	$sentences = preg_split('/(?<=[.!?])\s+/', $paragraph, -1, PREG_SPLIT_NO_EMPTY);

	$splitSentences = [];
	$currentSentence = '';

	foreach ($sentences as $sentence) {
		$currentSentence .= ' ' . $sentence;
		if (strlen($currentSentence) > 120) {
			$splitSentences[] = trim($currentSentence);
			$currentSentence = '';
		} elseif (strlen($currentSentence) >= 60 && strlen($currentSentence) <= 120) {
			$splitSentences[] = trim($currentSentence);
			$currentSentence = '';
		}
	}

	if (!empty($currentSentence)) {
		$splitSentences[] = trim($currentSentence);
	}

	return $splitSentences;
}

function returnLines($lines)
{

	global $db, $startTime, $forceMood, $staticMood, $talkedSoFar, $FORCED_STOP, $TRANSFORMER_FUNCTION;
	foreach ($lines as $n => $sentence) {

		if ($FORCED_STOP)
			return;
		// Remove actions
		$pattern = '/<[^>]+>/';
		$output = preg_replace($pattern, '', $sentence);

		$sentence = preg_replace('/[[:^print:]]/', '', $output); // Remove non ASCII chracters
		$sentence=$output;
		$output = preg_replace('/\*([^*]+)\*/', '', $sentence); // Remove text bewteen * *

		$sentence = preg_replace('/"/', '', $output); // Remove "

		preg_match_all('/\((.*?)\)/', $sentence, $matches);

		$responseTextUnmooded = trim(preg_replace('/\((.*?)\)/', '', $sentence));

		if (stripos($responseTextUnmooded, "whispering:") !== false) { // Very very nasty, but solves lots of isses. We must keep log clean.
			$responseTextUnmooded = str_ireplace("whispering:", "", $responseTextUnmooded);
			$forceMood = "whispering";
		}


		$scoring = checkOAIComplains($responseTextUnmooded);

		if ($scoring >= 3) { // Catch OpenAI brekaing policies stuff
			$responseTextUnmooded = "I can't think clearly now..."; // Key phrase to indicate OpenAI triggered warning
			$FORCED_STOP = true;
		} else {
			if (isset($TRANSFORMER_FUNCTION)) {
				$responseTextUnmooded = $TRANSFORMER_FUNCTION($responseTextUnmooded);
			}

		}



		if (isset($forceMood)) {
			$mood = $forceMood;
		} else if (isset($matches[1][0]))
			$mood = $matches[1][0];
		else
			$mood = "default";

		if (isset($staticMood))
			$mood = $staticMood;
		else
			$staticMood = $mood;

		if (isset($GLOBALS["FORCE_MOOD"]))
			$mood = $GLOBALS["FORCE_MOOD"];

		$responseText = $responseTextUnmooded;

		if (strlen($responseText) < 2) // Avoid too short reponses
			return;


		if (strpos($responseText, "The Narrator:") !== false) { // Force not impersonating the narrator.
			return;
		}

		if ($responseText) {
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

			if ($GLOBALS["TTSFUNCTION"] == "11labs") {
				if ($GLOBALS["ELEVENLABS_API_KEY"]) {
					require_once("tts/tts-11labs.php");
					tts($responseTextUnmooded, $mood, $responseText);
				}
			}

			if ($GLOBALS["TTSFUNCTION"] == "gcp") {
				if ($GLOBALS["GCP_SA_FILEPATH"]) {
					require_once("tts/tts-gcp.php");
					tts($responseTextUnmooded, $mood, $responseText);
				}
			}
			if (trim($responseText))
				$talkedSoFar[] = $responseText;
		}

		$outBuffer = array(
			'localts' => time(),
			'sent' => 1,
			'text' => trim(preg_replace('/\s\s+/', ' ', $responseTextUnmooded)),
			'actor' => "Herika",
			'action' => "AASPGQuestDialogue2Topic1B1Topic",
			'tag' => (isset($tag) ? $tag : "")
		);
		$GLOBALS["DEBUG"]["BUFFER"][] = "{$outBuffer["actor"]}|{$outBuffer["action"]}|$responseTextUnmooded\r\n";
		echo "{$outBuffer["actor"]}|{$outBuffer["action"]}|$responseTextUnmooded\r\n";
		@ob_flush();
		@flush();

		$db->insert(
			'log',
			array(
				'localts' => time(),
				'prompt' => nl2br(SQLite3::escapeString(print_r($GLOBALS["DEBUG_DATA"], true))),
				'response' => (SQLite3::escapeString($responseTextUnmooded)),
				'url' => nl2br(SQLite3::escapeString(print_r(base64_decode(stripslashes($_GET["DATA"])), true) . " in " . (time() - $startTime) . " secs "))


			)
		);
	}

}

/*********** MAIN FLOW **************/

$starTime = microtime(true);

// PARSE GET RESPONSE
$finalData = base64_decode(stripslashes($_GET["DATA"]));
//$finalData = base64_decode("ZnVuY3JldHwxOTEyMDczNjIzMzQwMDB8NDQ5MzM3MTg0fGNvbW1hbmRASW5zcGVjdFN1cnJvdW5kaW5nc0BAQWRyaWFubmUgQXZlbmljY2ksQmVsZXRob3IsV2hpdGVydW4gR3VhcmQsQnJpbGwsUGx1Z2luZWVyLEhlcmlrYSxXaGl0ZXJ1biBHdWFyZCwNCg==");

$finalParsedData = explode("|", $finalData);
foreach ($finalParsedData as $i => $ele)
	$finalParsedData[$i] = trim(preg_replace('/\s\s+/', ' ', preg_replace('/\'/m', "''", $ele)));

$finalParsedData[0] = strtolower($finalParsedData[0]); // Who put 'diary' uppercase?

if ($finalParsedData[0] == "info") { // Output queues must be indepentent by type
	$db->insert(
		'eventlog',
		array(
			'ts' => $finalParsedData[1],
			'gamets' => $finalParsedData[2],
			'type' => $finalParsedData[0],
			'data' => SQLite3::escapeString($finalParsedData[3]),
			'sess' => 'pending',
			'localts' => time()
		)
	);
	die();
}

// PREPARE CONTEXT DATA
require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");

$PROMPT_HEAD = ($GLOBALS["PROMPT_HEAD"]) ? $GLOBALS["PROMPT_HEAD"] : "Let\'s roleplay in the Universe of Skyrim. I\'m {$GLOBALS["PLAYER_NAME"]} ";


//COMMAND Follow(NPC name): To Instruct Herika to start following the specified target, dont use if wanna engage. DONT FOLLOW {$GLOBALS["PLAYER_NAME"]}
require_once(__DIR__.DIRECTORY_SEPARATOR."command_prompt.php");
require_once(__DIR__.DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "functions.php");


/****** PROMPT OVERWRITE *******/
/*
if (sizeof($GLOBALS["AZURETTS_CONF"]["validMoods"])>0) {
	$PROMPTS["inputtext"]=[
				"(put mood in parenthesys,valid moods[" . 
				implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . 
				"])(follow rules,you can optionally call functions, complete {$GLOBALS["HERIKA_NAME"]}'s sentence) {$GLOBALS["HERIKA_NAME"]}: " // Prompt is implicit
	];
} else {
		$PROMPTS["inputtext"]=[
			"(follow rules,you can optionally call functions, complete {$GLOBALS["HERIKA_NAME"]}'s sentence) {$GLOBALS["HERIKA_NAME"]}: " // Prompt
		];
}
	
$PROMPTS["inputtext_s"]=[
			"(follow rules,you can optionally call functions, complete {$GLOBALS["HERIKA_NAME"]}'s sentence){$GLOBALS["HERIKA_NAME"]}: " // Prompt is implicit

		];
*/
$PROMPTS["funcret"] = $PROMPTS["inputtext"];

/* SUPER PROMPT CUSTOMIZATION */

if (isset($PROMPTS[$finalParsedData[0]]["extra"])) {
	if (isset($PROMPTS[$finalParsedData[0]]["extra"]["mood"]))
		$GLOBALS["FORCE_MOOD"] = $PROMPTS[$finalParsedData[0]]["extra"]["mood"];
	if (isset($PROMPTS[$finalParsedData[0]]["extra"]["force_tokens_max"]))
		$GLOBALS["OPENAI_MAX_TOKENS"] = $PROMPTS[$finalParsedData[0]]["extra"]["force_tokens_max"];
	if (isset($PROMPTS[$finalParsedData[0]]["extra"]["transformer"]))
		$GLOBALS["TRANSFORMER_FUNCTION"] = $PROMPTS[$finalParsedData[0]]["extra"]["transformer"];
	if (isset($PROMPTS[$finalParsedData[0]]["extra"]["dontuse"]))
		if (($PROMPTS[$finalParsedData[0]]["extra"]["dontuse"]))
			return "";


}

/* END OF SUPER PROMPT CUSTOMIZATION */

if (!isset($PROMPTS["afterattack"]))
	$PROMPTS["afterattack"] = "(Just write a short intro catchphrase for combat) {$GLOBALS["HERIKA_NAME"]}: ";



if ($finalParsedData[0] == "funcret") { // Take out the functions part
	$request = str_replace("call function if needed,", "continue chat as $HERIKA_NAME,", $PROMPTS["inputtext"][0]); 

	
} else if ($finalParsedData[0] == "chatnf_book") { // Takea out the functions part
	$request = $PROMPTS["book"][0];
	$books=$db->fetchAll("select title from books order by gamets desc");
	
	$finalParsedData[3]=$PROMPTS["book"][1]." ".$books[0]["title"];
	
	
} else
	$request = $PROMPTS[$finalParsedData[0]][0]; // Add support for arrays here

if (stripos($finalParsedData[3], "stop") !== false) {
	echo "Herika|command|StopAll@\r\n";
	@ob_flush();
	$alreadysent[md5("Herika|command|StopAll@\r\n")] = "Herika|command|StopAll@\r\n";
}


$commandSent = false;

if ($finalParsedData[0] == "inputtext_s") {
	$GLOBALS["FORCE_MOOD"] = "whispering";
}

if ($finalParsedData[0] == "funcret") { // Overwrite funrect with info from database when topic requested
	$returnFunction = explode("@", $finalParsedData[3]); // Function returns here
	if ($returnFunction[1] == "GetTopicInfo") {


	} else if ($returnFunction[1] == "ReadQuestJournal") {
		$returnFunction[3] = $db->questJournal($returnFunction[2]); // Overwrite funrect content with info from database
		$finalParsedData[3] .= $returnFunction[3];
	
		// Store info.
		$db->insert(
			'eventlog',
			array(
				'ts' => $finalParsedData[1],
				'gamets' => $finalParsedData[2],
				'type' => 'chat',
				'data' => SQLite3::escapeString("The Narrator. Herika reads in diary:".$returnFunction[3]),
				'sess' => 'pending',
				'localts' => time()
			)
		);
		
		
	} else if ($returnFunction[1] == "ReadDiary") {
		
		$returnFunction[3] = $db->diaryLog($returnFunction[2]); // Overwrite funrect content with info from database
		$finalParsedData[3] .= $returnFunction[3];
		
		$GLOBALS["CONTEXT_HISTORY"] = 5; 			// Because probably we will push a lot of data here
		
		if (strlen($returnFunction[3])<16)			// No data found
			$GLOBALS["OPENAI_MAX_TOKENS"]="100";	// She will invent.
		else
			$GLOBALS["OPENAI_MAX_TOKENS"]="200";	// Because probably we want a detailed reponse base on diary.
			
		
		
	} else if ($returnFunction[1] == "setCurrentTask") {
		
		$returnFunction[3] .= "ok"; // This is always ok
		$finalParsedData[3].="done";
		$db->insert(
			'currentmission',
			array(
				'ts' => $finalParsedData[1],
				'gamets' => $finalParsedData[2],
				'description' => SQLite3::escapeString($returnFunction[2]),
				'sess' => 'pending',
				'localts' => time()
			)
		);
		

	} 

	
} else if ($finalParsedData[0] == "diary") {
	$GLOBALS["CONTEXT_HISTORY"] = ($GLOBALS["CONTEXT_HISTORY"]<50)?50:$GLOBALS["CONTEXT_HISTORY"];		// Forced to obtain high history volume;
	$finalParsedData[3]=$GLOBALS["PROMPTS"]["diary"][1];

}


if (($finalParsedData[0] == "inputtext") || ($finalParsedData[0] == "inputtext_s") || (strpos($finalParsedData[0],"chatnf")!==false)) {
	$finalParsedData[3] = "(To {$GLOBALS["HERIKA_NAME"]}) " . $finalParsedData[3];
}


/// LOG INTO DB
$db->insert(
	'eventlog',
	array(
		'ts' => $finalParsedData[1],
		'gamets' => $finalParsedData[2],
		'type' => $finalParsedData[0],
		'data' => SQLite3::escapeString($finalParsedData[3]),
		'sess' => 'pending',
		'localts' => time()
	)
);

$preprompt = preg_replace("/^[^:]*:/", "", $finalParsedData[3]);
$lastNDataForContext = (isset($GLOBALS["CONTEXT_HISTORY"])) ? ($GLOBALS["CONTEXT_HISTORY"]) : "25";
$contextData = $db->lastDataFor("", $lastNDataForContext * -1); // Context (last dialogues, events,...)
$contextData2 = $db->lastInfoFor("", -2); // Infot about location and npcs in first position

$contextCurrentPlan[]=  array('role' => 'user', 'content' => 'The Narrator: ('.$db->get_current_task().')');

$contextDataFull = array_merge($contextData2, $contextCurrentPlan,$contextData);

$head = array();
$foot = array();

// SHould we use system prompt, or not?
$head[] = array('role' => 'system', 'content' => '(' . $PROMPT_HEAD . $GLOBALS["HERIKA_PERS"] . $COMMAND_PROMPT);


//$foot[] = array('role' => 'user', 'content' => $GLOBALS["PLAYER_NAME"].':' . $preprompt);

$url = 'https://api.openai.com/v1/chat/completions';

$forceAttackingText = false;

/**** CALL *******/

if ($finalParsedData[0] == "funcret") {
	$prompt[] = array('role' => 'assistant', 'content' => $request);

	$returnFunction = explode("@", $finalParsedData[3]); // Function returns here

	$useFunctionsAgain = false;
	if (isset($returnFunction[2])) {
		if ($returnFunction[1] == "GetTopicInfo") {
			$argName = "topic";
			// Lets overwrite this
			// Get info about $returnFunction[2]}
			$returnFunction[3] = "";

			//
		} else if ($returnFunction[1] == "LeadTheWayTo") {
			$argName = "location";

		} else if ($returnFunction[1] == "MoveTo") {
			if (strpos($finalParsedData[3], "LeadTheWayTo") !== false) // PatchHack. If Moving returning Shoud use TravelTo, enable functions again
				$useFunctionsAgain = true;

		} else if ($returnFunction[1] == "Attack") {
			//$useFunctionsAgain=true;
			$forceAttackingText = true;
			$argName = "target";

		} else if ($returnFunction[1] == "ReadQuestJournal") {
			//$useFunctionsAgain=true;
			$request="(use function setCurrentTask to update current quest if needed) $request";
			$argName = "id_quest";
			$useFunctionsAgain=true;

		} else if ($returnFunction[1] == "ReadDiary") {
			//$useFunctionsAgain=true;
			$argName = "topic";
			//$useFunctionsAgain=true;


		} else if ($returnFunction[1] == "GetTime") {
			//$useFunctionsAgain=true;
			$argName = "datestring";
			//$useFunctionsAgain=true;


		} else if ($returnFunction[1] == "get_current_mission") {		// Disabled, current task is always provided.
			//$useFunctionsAgain=true;
			$argName = "description";
			//$useFunctionsAgain=true;


		} else if ($returnFunction[1] == "setCurrentTask") {
			//$useFunctionsAgain=true;
			$argName = "description";
			//$useFunctionsAgain=true;


		} else if ($returnFunction[1] == "CheckInventory") {
			//$useFunctionsAgain=true;
			$argName = "target";
			//$useFunctionsAgain=true;


		} else {
			$argName = "target";

		}
		$functionCalled[] = array('role' => 'assistant', 'content' => null, 'function_call' => array("name" => $returnFunction[1], "arguments" => "{\"$argName\":\"{$returnFunction[2]}\"}"));

	} else
		$functionCalled[] = array('role' => 'assistant', 'content' => null, 'function_call' => ["name" => $returnFunction[1], "arguments" => "\"{}\""]);

	$returnFunctionArray[] = array('role' => 'function', 'name' => $returnFunction[1], 'content' => "{$returnFunction[3]}");

	if ($forceAttackingText)
		$returnFunctionArray[] = array('role' => 'assistant', 'content' => "{$PROMPTS["afterattack"][0]} {$GLOBALS["HERIKA_NAME"]}: ");
	else
		$returnFunctionArray[] = array('role' => 'assistant', 'content' => $request);


	$parms = array_merge($head, ($contextDataFull), $functionCalled, $returnFunctionArray);
	//$parms = array_merge($head, ($contextDataFull), $functionCalled, $returnFunctionArray, [end($contextDataFull)]);

	$data = array(
		'model' => 'gpt-3.5-turbo-0613',
		'messages' =>
		$parms
		,
		'stream' => true,
		'max_tokens' => ((isset($GLOBALS["OPENAI_MAX_TOKENS"]) ? $GLOBALS["OPENAI_MAX_TOKENS"] : 48) + 0),
		'temperature' => 1,
		'presence_penalty' => 1,
	);

	if ($useFunctionsAgain) {
		$data['functions'] = $GLOBALS["FUNCTIONS"];
		$data['function_call'] = "auto";
	}

} else if ( (strpos($finalParsedData[0],"chatnf")!==false)) {


	$prompt[] = array('role' => 'assistant', 'content' => $request);

	$parms = array_merge($head, ($contextDataFull), $prompt);
	$data = array(
		'model' => 'gpt-3.5-turbo-0613',
		'messages' =>
		$parms,
		'stream' => true,
		'max_tokens' => ((isset($GLOBALS["OPENAI_MAX_TOKENS"]) ? $GLOBALS["OPENAI_MAX_TOKENS"] : 48) + 0),
		'temperature' => 1,
		'presence_penalty' => 1
	);


} else if ($finalParsedData[0] == "diary") {


	$prompt[] = array('role' => 'assistant', 'content' => $request);

	$parms = array_merge($head, ($contextDataFull), $prompt);
	$data = array(
		'model' => 'gpt-3.5-turbo-0613',
		'messages' =>
		$parms,
		'stream' => false,
		'max_tokens' => ((isset($GLOBALS["OPENAI_MAX_TOKENS_MEMORY"]) ? $GLOBALS["OPENAI_MAX_TOKENS_MEMORY"] : 1024) + 0),
		'temperature' => 1,
		'presence_penalty' => 1,
		'functions' => $GLOBALS["FUNCTIONS_SPECIAL_CONTEXT"],
		'function_call' => ["name"=>"WriteIntoDiary"]	// Should be '{"name":\ "WriteIntoDiary"}'
	);


} else {

	$prompt[] = array('role' => 'assistant', 'content' => $request);
	$parms = array_merge($head, ($contextDataFull), $prompt);
	$data = array(
		'model' => 'gpt-3.5-turbo-0613',
		'messages' =>
		$parms
		,
		'stream' => true,
		'max_tokens' => ((isset($GLOBALS["OPENAI_MAX_TOKENS"]) ? $GLOBALS["OPENAI_MAX_TOKENS"] : 48) + 0),
		'temperature' => 1,
		'presence_penalty' => 1,
		'functions' => $GLOBALS["FUNCTIONS"],
		'function_call' => 'auto'
	);
}


$GLOBALS["DEBUG_DATA"][] = $data;

$headers = array(
	'Content-Type: application/json',
	"Authorization: Bearer {$GLOBALS["OPENAI_API_KEY"]}"
);

$options = array(
	'http' => array(
		'method' => 'POST',
		'header' => implode("\r\n", $headers),
		'content' => json_encode($data),
		'timeout' => ($GLOBALS["HTTP_TIMEOUT"]) ?: 30
	)
);



$context = stream_context_create($options);

///////DEBUG CODE
$fileLog = fopen("log.txt", 'a');
fwrite($fileLog, ">>" . $finalParsedData[3] . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE

fwrite($fileLog, ">>" . print_r($data["messages"], true) . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE


$fileLogSent = fopen("logSent.txt", 'a'); // Will LOG OpenAI calls
$stamp = "@STAMP ################# " . date('h:i:s', time());
fwrite($fileLogSent, $stamp . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE

/////
error_reporting(E_ALL);
$handle = fopen($url, 'r', false, $context);

if ($handle === false) {

	$db->insert(
		'log',
		array(
			'localts' => time(),
			'prompt' => nl2br(SQLite3::escapeString(print_r($GLOBALS["DEBUG_DATA"], true))),
			'response' => (SQLite3::escapeString(print_r(error_get_last(), true))),
			'url' => nl2br(SQLite3::escapeString(print_r(base64_decode(stripslashes($_GET["DATA"])), true) . " in " . (time() - $startTime) . " secs "))


		)
	);
	returnLines([$GLOBALS["ERROR_OPENAI"]]);
	@ob_end_flush();

	print_r(error_get_last(), true);

} else {

	if ($data["stream"] == false) {
		// Streammed mode disabled. For memory writing atm.
		$buffer = "";
		$totalBuffer = "";

		while (!feof($handle)) {
			$buffer.=fread($handle,1024);
		}
		$response=json_decode($buffer,true);
		$rawResponse = json_decode($response["choices"][0]["message"]["function_call"]["arguments"],true);

		fwrite($fileLogSent,$buffer . "eob".PHP_EOL); // Write the line to the file with a line break // DEBUG CODE
		fwrite($fileLogSent, print_r($rawResponse,true) . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE
		// Do something with the response;

		$db->insert(
			'diarylog',
			array(
				'ts' => $finalParsedData[1],
				'gamets' => $finalParsedData[2],
				'topic' => SQLite3::escapeString($rawResponse["topic"]),
				'content' => SQLite3::escapeString($rawResponse["content"]),
				'tags' => SQLite3::escapeString($rawResponse["tags"]),
				'people' => SQLite3::escapeString($rawResponse["people"]),
				'location' => SQLite3::escapeString($rawResponse["location"]),
				'sess' => 'pending',
				'localts' => time()
			)
		);
		
		$db->insert(
			'diarylogv2',
			array(
				'topic' => SQLite3::escapeString($rawResponse["topic"]),
				'content' => SQLite3::escapeString($rawResponse["content"]),
				'tags' => SQLite3::escapeString($rawResponse["tags"]),
				'people' => SQLite3::escapeString($rawResponse["people"]),
				'location' => SQLite3::escapeString($rawResponse["location"])
			)
		);
		
		returnLines([$RESPONSE_OK_NOTED]);
		@ob_flush();
		


	} else {
		// Streamed mode. Read and process the response line by line
		$buffer = "";
		$totalBuffer = "";
		$functionIsUsed = false;
		while (!feof($handle)) {
			$line = fgets($handle);
			//echo $line;
			//continue;
			fwrite($fileLogSent, $line . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE

			$data = json_decode(substr($line, 6), true);
			if (isset($data["choices"][0]["delta"]["content"])) {
				if (strlen(trim($data["choices"][0]["delta"]["content"])) > 0)
					$buffer .= $data["choices"][0]["delta"]["content"];
				$totalBuffer .= $data["choices"][0]["delta"]["content"];


			}

			// Catch function calling  here

			if (isset($data["choices"][0]["delta"]["function_call"])) {

				if (isset($data["choices"][0]["delta"]["function_call"]["name"])) {
					$functionName = $data["choices"][0]["delta"]["function_call"]["name"];
				}

				if (isset($data["choices"][0]["delta"]["function_call"]["arguments"])) {

					$parameterBuff .= $data["choices"][0]["delta"]["function_call"]["arguments"];

				}
			}

			if (isset($data["error"])) {
				$GLOBALS["DEBUG_DATA"][] = $data["error"];
				returnLines([$ERROR_OPENAI_REQLIMIT]);
				break;
			}


			if (isset($data["choices"][0]["finish_reason"]) && $data["choices"][0]["finish_reason"] == "function_call") {
				$parameterArr = json_decode($parameterBuff, true);
				$parameter = current($parameterArr); // Only support for one parameter

				if (!isset($alreadysent[md5("Herika|command|$functionName@$parameter\r\n")])) {
					echo "Herika|command|$functionName@$parameter\r\n";
					$db->insert(
						'eventlog',
						array(
							'ts' => $finalParsedData[1],
							'gamets' => $finalParsedData[2],
							'type' => "funccall",
							'data' => "Herika: {" . "$functionName($parameter)" . "}",
							'sess' => 'pending',
							'localts' => time()
						)
					);

				}

				$alreadysent[md5("Herika|command|$functionName@$parameter\r\n")] = "Herika|command|$functionName@$parameter\r\n";
				@ob_flush();
			}

			$buffer = strtr($buffer, array("\"" => ""));

			$pattern = "/\([^()]*\)/"; // Modified pattern to remove unmatched opening parentheses
			$buffer = preg_replace($pattern, "", $buffer);

			if (strlen($buffer) < MAXIMUM_SENTENCE_SIZE) // Avoid too short buffers
				continue;


			$position = findDotPosition($buffer);

			if ($position !== false) {
				$extractedData = substr($buffer, 0, $position + 1);
				$remainingData = substr($buffer, $position + 1);
				$sentences = split_sentences_stream(cleanReponse($extractedData));
				$GLOBALS["DEBUG_DATA"][] = (microtime(true) - $starTime) . " secs in openai stream";
				returnLines($sentences);
				//echo "$extractedData  # ".(microtime(true)-$starTime)."\t".strlen($finalData)."\t".PHP_EOL;  // Output
				$extractedData = "";
				$buffer = $remainingData;

			}
		}
		if (trim($buffer)) {

			$sentences = split_sentences_stream(cleanReponse(trim($buffer)));
			$GLOBALS["DEBUG_DATA"][] = (microtime(true) - $starTime) . " secs in openai stream";
			returnLines($sentences);

		}
		fclose($handle);
		fwrite($fileLog, $totalBuffer . " lines talked. " . sizeof($talkedSoFar) . " commands " . sizeof($alreadysent) . "\r\nParsed Commands:" . print_r($alreadysent, true) . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE
	}
}

if (sizeof($talkedSoFar) == 0) {
	if (sizeof($alreadysent) > 0) { // AI only issued commands, plugin will request a response in 10 seconds.

		$db->insert(
			'log',
			array(
				'localts' => time(),
				'prompt' => nl2br(SQLite3::escapeString(print_r($GLOBALS["DEBUG_DATA"], true))),
				'response' => SQLite3::escapeString(print_r($alreadysent, true)),
				'url' => nl2br(SQLite3::escapeString(print_r(base64_decode(stripslashes($_GET["DATA"])), true) . " in " . (time() - $startTime) . " secs "))


			)
		);
		// Should choose wich events she tends to call function without response.
		//returnLines(["Sure thing!"]);

	} else { // Fail request? or maybe an invalid command was issued

		//returnLines(array($randomSentence));
		$db->insert(
			'log',
			array(
				'localts' => time(),
				'prompt' => nl2br(SQLite3::escapeString(print_r($GLOBALS["DEBUG_DATA"], true))),
				'response' => SQLite3::escapeString(print_r($alreadysent, true)),
				'url' => nl2br(SQLite3::escapeString(print_r(base64_decode(stripslashes($_GET["DATA"])), true) . " in " . (time() - $startTime) . " secs "))


			)
		);

	}



}
echo 'X-CUSTOM-CLOSE';
$stamp = "@STAMP END ################# " . date('h:i:s', time());
fwrite($fileLogSent, print_r($GLOBALS["DEBUG"]["BUFFER"], true) . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE
fwrite($fileLogSent, $stamp . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE

//echo "\r\n<$totalBuffer>";
?>
