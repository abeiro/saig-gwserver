<?php
error_reporting(E_ERROR);

define("MAXIMUM_SENTENCE_SIZE", 125);

$MINIMUM_SENTENCE_SIZE=15;

$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR;
require_once($path . "conf.php");
require_once($path . "dynmodel.php");
require_once($path . "lib/$DRIVER.class.php");
require_once($path . "lib/Misc.php");
require_once($path . "lib/vectordb.php");
require_once($path . "lib/embeddings.php");
$db = new sql();

while (@ob_end_clean());

ignore_user_abort(true);
set_time_limit(1200);

$startTime=time();
$LAST_ROLE="user";
$ERROR_TRIGGERED=false;
$momentum=time();
$talkedSoFar=[];


function findLastSentenceEnd($string) {
    $endMarkers = array('.', '?', '!');
    $lastPositions = array();

	// find end of sentence marker and get its position
	// note: if '.' is its own token, then ellipsis ('...') can be misinterpreted as end of sentence
    foreach ($endMarkers as $marker) {
        $position = strrpos($string, $marker);
        if ($position !== false) {
            $lastPositions[] = $position;
        }
    }

    if (!empty($lastPositions)) {
        $lastPosition = max($lastPositions);

        // check for ('..') which could occur in the middle of a sentence
        if ($string[$lastPosition] == '.' && isset($string[$lastPosition - 1]) && $string[$lastPosition - 1] == '.') {
            return false;
        }

        return $lastPosition;
    }

    return false;
}

function split_sentences_stream($paragraph) {
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

function returnLines($lines) {
	
	global $db,$startTime,$forceMood,$staticMood,$talkedSoFar,$memories;
	foreach ($lines as $n=>$sentence) {

		$output = preg_replace('/\*([^*]+)\*/', '', $sentence); // Remove text bewteen * *

		$sentence = preg_replace('/"/', '', $output); // Remove "

		preg_match_all('/\((.*?)\)/', $sentence, $matches);
		$responseTextUnmooded = trim(preg_replace('/\((.*?)\)/', '', $sentence));
		
		if (isset($forceMood)) {
			$mood = $forceMood;
		} else
			$mood = $matches[1][0];

		if (isset($staticMood))
			$mood=$staticMood;
		else
			$staticMood=$mood;
		
		$responseText=$responseTextUnmooded;

		if (preg_match('/^[^a-zA-Z0-9]+$/', $responseText))	// Skip if only non alphanumeric
			return;

		if (is_array($talkedSoFar))							// Fast hack to avoid duplicate sentences
			if (in_array($responseTextUnmooded,$talkedSoFar))
				return;
				
		if (isset($GLOBALS["FORCE_MOOD"]))
			$mood = $GLOBALS["FORCE_MOOD"];
		
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
		
		$outBuffer=array(
						'localts' => time(),
						'sent' => 1,
						'text' => trim(preg_replace('/\s\s+/', ' ', $responseTextUnmooded)),
						'actor' => "Herika",
						'action' => "AASPGQuestDialogue2Topic1B1Topic",
						'tag'=>(isset($tag)?$tag:"")
					);
		
		
		echo "{$outBuffer["actor"]}|{$outBuffer["action"]}|$responseTextUnmooded\r\n";
		ob_flush();
		flush();
		
		if (php_sapi_name()=="cli") {
			if (!isset($GLOBALS["nts"]))
				$GLOBALS["nts"]=0;
			else
				$GLOBALS["nts"]++;
			$db->insert(
				'eventlog',
				array(
					'localts' => time()+$GLOBALS["nts"],
					'type' => "chat",
					'data' => (SQLite3::escapeString("{$outBuffer["actor"]}: $responseTextUnmooded")),
					'gamets' => 770416256,
					'ts'=>108826400925500
				)
			);
			
		}
		
		
		$db->insert(
				'log',
				array(
					'localts' => time(),
					'prompt' => nl2br(SQLite3::escapeString(print_r($GLOBALS["DEBUG_DATA"],true))),
					'response' => (SQLite3::escapeString($responseTextUnmooded)),
					'url' => nl2br(SQLite3::escapeString(print_r( base64_decode(stripslashes($_GET["DATA"])),true)." in ".(time()-$startTime)." secs " ))
					
				
				)
			);
	}
	
}

function logMemory($speaker,$listener,$message,$momentum,$gamets) {
    global $db;
	
	// Here, we could use the LLM to generate a summary. Will consume a lot of tokens.
	
    $db->insert(
	'memory',
		array(
				'localts' => time(),
				'speaker' => (SQLite3::escapeString($speaker)),
                'listener' => (SQLite3::escapeString($listener)),
				'message' => (SQLite3::escapeString($message)),
	  			'gamets' => $gamets,
				'session' => "pending",
                'momentum'=>$momentum
		)
	);
    if (isset($GLOBALS["MEMORY_EMBEDDING"]) && $GLOBALS["MEMORY_EMBEDDING"]) {
		$insertedSeq=$db->fetchAll("SELECT SEQ from sqlite_sequence WHERE name='memory'");
		$embeddings=getEmbeddingRemote($message);
		if (sizeof($embeddings)>0)
			storeMemory($embeddings,$message,$insertedSeq[0]["seq"]);	
	}
    
}


$starTime=microtime(true);

// PARSE GET RESPONSE
$finalData = base64_decode(stripslashes($_GET["DATA"]));
if (php_sapi_name()=="cli") {
	// You can run this script directly with php: stream.php "Player text" 
	
	$finalData = "inputtext|594939787246000|788840576|{$GLOBALS["PLAYER_NAME"]}: {$argv[1]}";
}


$finalParsedData = explode("|", $finalData);
foreach ($finalParsedData as $i => $ele)
		$finalParsedData[$i] = trim(preg_replace('/\s\s+/', ' ', preg_replace('/\'/m', "''", $ele)));



// PREPARE CONTEXT DATA
require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");

$PROMPT_HEAD=($GLOBALS["PROMPT_HEAD"])?$GLOBALS["PROMPT_HEAD"]:"Let\'s roleplay in the Universe of Skyrim. I\'m {$GLOBALS["PLAYER_NAME"]} ";

require_once(__DIR__.DIRECTORY_SEPARATOR."command_prompt.php");


// Add the DIALOGUE_TARGET
if (($finalParsedData[0] == "inputtext") || ($finalParsedData[0] == "inputtext_s") || (strpos($finalParsedData[0],"chatnf")!==false)) {
	$finalParsedData[3] = $finalParsedData[3]." $DIALOGUE_TARGET";
}

// Log my chat
 if ($finalParsedData[0] != "diary") {
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
 }

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

$request=$PROMPTS[$finalParsedData[0]][0];

if ($finalParsedData[0]=="inputtext_s") {
	$forceMood="whispering";
	

	
} else if ($finalParsedData[0]=="inputtext") {

	

	
} else if ($finalParsedData[0] == "chatnf_book") { // new read book event
	$request = $PROMPTS["book"][0];
	$books=$db->fetchAll("select title from books order by gamets desc");
	
	$finalParsedData[3]=$PROMPTS["book"][1]." ".$books[0]["title"];

}  else if ( (strpos($finalParsedData[0],"chatnf")!==false)) {
	
	$request = $PROMPTS[$finalParsedData[0]][0];

	
}  else if ($finalParsedData[0] == "diary") {
	
	$request = $PROMPTS["diary_noactions"][0];

	
} 

$request = str_replace("specify action for $HERIKA_NAME or","",$request);	// Make better

$preprompt=preg_replace("/^[^:]*:/", "", $finalParsedData[3]);
$lastNDataForContext=(isset($GLOBALS["CONTEXT_HISTORY"])) ? ($GLOBALS["CONTEXT_HISTORY"]) : "25";
$contextDataDialog = $db->lastDataNewFor("",$lastNDataForContext*-1);
$contextDataWorld = $db->lastInfoFor("", -2); // Infot about location and npcs in first position
$contextData=array_merge($contextDataWorld,$contextDataDialog);

$head = array();
$foot = array();
$MEMORIES="";

/* Memory offering */
if (isset($GLOBALS["MEMORY_EMBEDDING"]) && $GLOBALS["MEMORY_EMBEDDING"]) {
	if (($finalParsedData[0] == "inputtext") || ($finalParsedData[0] == "inputtext_s")) {
		$memories=array();
		
		// This is to remove the "Player:" aprt.
		$textToEmbed=str_replace($DIALOGUE_TARGET,"",$finalParsedData[3]);
		$pattern = '/\([^)]+\)/';
		$textToEmbedFinal = preg_replace($pattern, '', $textToEmbed);
		$textToEmbedFinal=str_replace("{$GLOBALS["PLAYER_NAME"]}:","",$textToEmbedFinal);

		$embeddings=getEmbeddingRemote($textToEmbedFinal);
		if (sizeof($embeddings)>0) {
			$memories=queryMemory($embeddings);
			$GLOBALS["DEBUG_DATA"]["memories"]=$memories["content"];
			if (is_array($memories["content"])) {
				consoleLog("Related memory injected");
				
				//$memories["content"][0]["search_term"]=$textToEmbedFinal;
				$MEMORIES="\n".$GLOBALS["MEMORY_OFFERING"].json_encode($memories["content"]);

			}
		}
	}
}
/**/

$head[] = array('role' => 'system', 'content' => '('.$PROMPT_HEAD.$GLOBALS["HERIKA_PERS"].$MEMORIES);
$prompt[] = array('role' => $LAST_ROLE, 'content' => $request);
$foot[] = array('role' => 'user', 'content' => $GLOBALS["PLAYER_NAME"].':' . $preprompt);

if (!$preprompt)
	$parms = array_merge($head, ($contextData), $prompt);
else
	//$parms = array_merge($head, ($contextData), $foot, $prompt);
	$parms = array_merge($head, ($contextData),  $prompt);




//// DIRECT OPENAI REST API

if ( (!isset($GLOBALS["MODEL"]) || ($GLOBALS["MODEL"]=="openai"))) {
	consoleLog("OpenAI type call");
	$url = $GLOBALS["OPENAI_URL"];
	
	if ($finalParsedData[0] == "diary") 
		$MAX_TOKENS=((isset($GLOBALS["OPENAI_MAX_TOKENS_MEMORY"]) ? $GLOBALS["OPENAI_MAX_TOKENS_MEMORY"] : 1024) + 0);
	else
		$MAX_TOKENS=((isset($GLOBALS["OPENAI_MAX_TOKENS"])?$GLOBALS["OPENAI_MAX_TOKENS"]:48)+0);
	
	
	$data = array(
		'model' => (isset($GLOBALS["GPTMODEL"]))?$GLOBALS["GPTMODEL"]:'gpt-3.5-turbo-0613',
		'messages' => 
			$parms
		,
		'stream' => true,
		'max_tokens'=>$MAX_TOKENS
		
	);

 
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
	//error_reporting(E_ALL);
	$context = stream_context_create($options);
	$handle = fopen($url, 'r', false, $context);
	$GLOBALS["DEBUG_DATA"][]=$parms;


} else if ($GLOBALS["MODEL"]=="koboldcpp")  {
	$GLOBALS["DEBUG_DATA"]=[];//reset
	consoleLog("koboldcpp type call");

	$url=$GLOBALS["KOBOLDCPP_URL"].'/api/extra/generate/stream/';
	$context="";


	foreach ($parms as $s_role=>$s_msg) {	// Have to mangle context format

		if (empty(trim($s_msg["content"])))
			continue;
		else {
			// This should be customizable per model
			/*
			if ($s_msg["role"]=="user")
				$normalizedContext[]="### Instruction: ".$s_msg["content"];
			else if ($s_msg["role"]=="assistant")
				$normalizedContext[]="### Response: ".$s_msg["content"];
			else if ($s_msg["role"]=="system")
				$normalizedContext[]=$s_msg["content"];
			*/
			$normalizedContext[]=$s_msg["content"];
		}
	}	

	foreach ($normalizedContext as $n=>$s_msg) {
		if ($n==(sizeof($normalizedContext)-1)) {
			$context.="### Instruction: ".$s_msg.". Write a single reply only.";
			$GLOBALS["DEBUG_DATA"][]="### Instruction: ".$s_msg."";

		} else {
			$s_msg_p = preg_replace('/^(The Narrator:)(.*)/m', '[Author\'s notes: $2 ]', $s_msg);
			$context.="$s_msg_p\n";
			$GLOBALS["DEBUG_DATA"][]=$s_msg_p;
		}
		
	}
	//$context.="\n{$GLOBALS["HERIKA_NAME"]}:";
	$context.="\n### Response:";
	$GLOBALS["DEBUG_DATA"][]="\n### Response:";
	
	if ($finalParsedData[0] == "diary") {
		$TEMPERATURE=((isset($GLOBALS["KOBOLDCPP_TEMPERATURE"])?$GLOBALS["KOBOLDCPP_TEMPERATURE"]:0.9)+0);
		$REP_PEN=((isset($GLOBALS["KOBOLDCPP_REP_PEN"])?$GLOBALS["KOBOLDCPP_REP_PEN"]:1.12)+0);
		$TOP_P=((isset($GLOBALS["KOBOLDCPP_TOP_P"])?$GLOBALS["KOBOLDCPP_TOP_P"]:0.9)+0);
		$MAX_TOKENS=((isset($GLOBALS["KOBOLDCPP_MAX_TOKENS_MEMORY"]) ? $GLOBALS["KOBOLDCPP_MAX_TOKENS_MEMORY"] : 1024) + 0);
		$stop_sequence=["{$GLOBALS["PLAYER_NAME"]}:","\n{$GLOBALS["PLAYER_NAME"]} ","Author\'s notes","\n"];
		$postData = array(
		
			"prompt"=>$context,
			"temperature"=> $TEMPERATURE,
			"top_p"=> 0.9,
			"max_length"=>$MAX_TOKENS,
			"rep_pen"=>$REP_PEN,
			"stop_sequence"=>$stop_sequence
		);
		
	}
	else {
		$TEMPERATURE=((isset($GLOBALS["KOBOLDCPP_TEMPERATURE"])?$GLOBALS["KOBOLDCPP_TEMPERATURE"]:0.9)+0);
		$REP_PEN=((isset($GLOBALS["KOBOLDCPP_REP_PEN"])?$GLOBALS["KOBOLDCPP_REP_PEN"]:1.12)+0);
		$TOP_P=((isset($GLOBALS["KOBOLDCPP_TOP_P"])?$GLOBALS["KOBOLDCPP_TOP_P"]:0.9)+0);
		$MAX_TOKENS=((isset($GLOBALS["KOBOLDCPP_MAX_TOKENS"])?$GLOBALS["KOBOLDCPP_MAX_TOKENS"]:48)+0);
		$stop_sequence=["{$GLOBALS["PLAYER_NAME"]}:","\n{$GLOBALS["PLAYER_NAME"]} ","Author\'s notes","\n"];
		$postData = array(
		
			"prompt"=>$context,
			"temperature"=> $TEMPERATURE,
			"top_p"=> $TOP_P,
			"max_length"=>$MAX_TOKENS,
			"rep_pen"=>$REP_PEN,
			"stop_sequence"=>$stop_sequence
		);
	}
	
	$headers = array(
		'Content-Type: application/json'
	);

	$options = array(
		'http' => array(
			'method' => 'POST',
			'header' => implode("\r\n", $headers),
			'content' => json_encode($postData)
		)
	);
	error_reporting(E_ALL);
	$context = stream_context_create($options);
	
	
	$host = parse_url($GLOBALS["KOBOLDCPP_URL"], PHP_URL_HOST);
	$port = parse_url($GLOBALS["KOBOLDCPP_URL"], PHP_URL_PORT);
	$path = '/api/extra/generate/stream/';

	// Data to send in JSON format
	$dataJson = json_encode($postData);

	$request = "POST $path HTTP/1.1\r\n";
	$request .= "Host: $host\r\n";
	$request .= "Content-Type: application/json\r\n";
	$request .= "Content-Length: " . strlen($dataJson) . "\r\n";
	$request .= "Connection: close\r\n\r\n";
	$request .= $dataJson;

	// Open a TCP connection
	$handle = fsockopen('tcp://' . $host, $port, $errno, $errstr, 30);

	// Send the HTTP request
	if ($handle !== false) {
		fwrite($handle, $request);
	}
	
	// Initialize variables for response
	$responseHeaders = '';
	$responseBody = '';
	//$handle = fopen($url, 'r', false, $context);
}
///////DEBUG CODE
//$fileLog = fopen("log.txt", 'a');
/////

if ($handle === false) {
	
	$db->insert(
				'log',
				array(
					'localts' => time(),
					'prompt' => nl2br(SQLite3::escapeString(print_r($GLOBALS["DEBUG_DATA"],true))),
					'response' => (SQLite3::escapeString(print_r(error_get_last(),true))),
					'url' => nl2br(SQLite3::escapeString(print_r( base64_decode(stripslashes($_GET["DATA"])),true)." in ".(time()-$startTime)." secs " ))
					
				
				)
			);
} else {
    // Read and process the response line by line
    $buffer="";
    $totalBuffer="";
	$breakFlag=false;
	$lineCounter=0;
	$fullContent="";
	$totalProcessedData="";
	$numOutputTokens = 0;

    while (true) {
		
		if ($breakFlag)
			break;
		
		if ( (!isset($GLOBALS["MODEL"]) || ($GLOBALS["MODEL"]=="openai"))) {
			$line = fgets($handle);
			

			file_put_contents("debugStream.log",$line,FILE_APPEND);

			$data=json_decode(substr($line,6),true);
			if (isset($data["choices"][0]["delta"]["content"])) {
				if (strlen(trim($data["choices"][0]["delta"]["content"]))>0) {
					$buffer.=$data["choices"][0]["delta"]["content"];
					$numOutputTokens += 1;

				}
			$totalBuffer.=$data["choices"][0]["delta"]["content"];
			}

		} else if ($GLOBALS["MODEL"]=="koboldcpp")  {
			error_reporting(E_ERROR);
			if (feof($handle)) {
				$breakFlag=true;
				continue;
			}

			$line = fgets($handle);

			file_put_contents("debugStream.log",$line,FILE_APPEND);

			if (strpos($line, 'data: {') !== 0) {
				continue;
			}
			$data=json_decode(substr($line,6),true);
			if (isset($data["token"])) {
				if (strlen(trim($data["token"]))>0) {
					$buffer.=$data["token"];
				}
				$totalBuffer.=$data["token"];
			}
		}

		$buffer=strtr($buffer,array("\""=>""));
	   
		if ( (!isset($GLOBALS["MODEL"]) || ($GLOBALS["MODEL"]=="openai"))) {
			if (feof($handle))
				$breakFlag=true;
		} else if ($GLOBALS["MODEL"]=="koboldcpp")  {
			if (feof($handle))
				$breakFlag=true;
				
		}
		
		if (strlen($buffer)<$MINIMUM_SENTENCE_SIZE)	// Avoid too short buffers
			continue;
		
		$position = findLastSentenceEnd($buffer);
		
        if ($position !== false) {
            $extractedData = substr($buffer, 0, $position + 1);
            $remainingData = substr($buffer, $position + 1);
            $sentences=split_sentences_stream(cleanReponse($extractedData));
			$GLOBALS["DEBUG_DATA"][]=(microtime(true) - $starTime)." secs in openai stream";
			if ($finalParsedData[0] != "diary")
				returnLines($sentences);
			else {
				$talkedSoFar[md5(implode(" ",$sentences))]=implode(" ",$sentences);
			}
            //echo "$extractedData  # ".(microtime(true)-$starTime)."\t".strlen($finalData)."\t".PHP_EOL;  // Output
			$totalProcessedData.=$extractedData;
            $extractedData="";
            $buffer=$remainingData;
            
        }
		
    }
    if (trim($buffer)) {
		 $sentences=split_sentences_stream(cleanReponse(trim($buffer)));
		 $GLOBALS["DEBUG_DATA"][]=(microtime(true) - $starTime)." secs in openai stream";
		 if ($finalParsedData[0] != "diary")
			returnLines($sentences);
		 else {
			 $talkedSoFar[md5(implode(" ",$sentences))]=implode(" ",$sentences);
		 }
		 $totalBuffer.=trim($buffer);
		 $totalProcessedData.=trim($buffer);
	}

	tokenizeResponse($numOutputTokens);

    fclose($handle);
	//fwrite($fileLog, $totalBuffer . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE
}

if (!$ERROR_TRIGGERED) {
		if ($finalParsedData[0] == "diary") {
			$topic=$db->lastKnowDate();
			$location=$db->lastKnownLocation();
			$db->insert(
			'diarylog',
				array(
					'ts' => $finalParsedData[1],
					'gamets' => $finalParsedData[2],
					'topic' => "$topic",
					'content' => SQLite3::escapeString(implode(" ",$talkedSoFar)),
					'tags' => "Pending",
					'people' => "Pending",
					'location' => "$location",
					'sess' => 'pending',
					'localts' => time()
				)
			);
			returnLines([$RESPONSE_OK_NOTED]);
		} else {
			$lastPlayerLine=$db->fetchAll("SELECT data from eventlog where type in ('inputtext','inputtext_s') order by gamets desc limit 0,1");
			logMemory($GLOBALS["HERIKA_NAME"],$GLOBALS["PLAYER_NAME"],"{$lastPlayerLine[0]["data"]} \n\r {$GLOBALS["HERIKA_NAME"]}:".implode(" ",$talkedSoFar),$momentum,$finalParsedData[2]);
		}
	}

file_put_contents("log_stream.txt",$totalBuffer,FILE_APPEND);
	

echo 'X-CUSTOM-CLOSE';
//echo "\r\n<$totalBuffer>";
?>
