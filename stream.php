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

function findDotPosition($string) {
    $dotPosition = strrpos($string, ".");
    
    if ($dotPosition !== false && strpos($string, ".", $dotPosition + 1) === false && substr($string, $dotPosition - 3, 3) !== "...") {
        return $dotPosition;
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
	
	global $db,$startTime,$forceMood,$staticMood,$talkedSoFar;
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
		storeMemory($embeddings,$message,$insertedSeq[0]["seq"]);	
	}
    
}


$starTime=microtime(true);

// PARSE GET RESPONSE
$finalData = base64_decode(stripslashes($_GET["DATA"]));
$finalParsedData = explode("|", $finalData);
foreach ($finalParsedData as $i => $ele)
		$finalParsedData[$i] = trim(preg_replace('/\s\s+/', ' ', preg_replace('/\'/m', "''", $ele)));

// Log my chat
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


// PREPARE CONTEXT DATA
require_once(__DIR__ . DIRECTORY_SEPARATOR . "prompts.php");

$PROMPT_HEAD=($GLOBALS["PROMPT_HEAD"])?$GLOBALS["PROMPT_HEAD"]:"Let\'s roleplay in the Universe of Skyrim. I\'m {$GLOBALS["PLAYER_NAME"]} ";

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

	
} else if ($finalParsedData[0] == "chatnf_book") { // new read book event
	$request = $PROMPTS["book"][0];
	$books=$db->fetchAll("select title from books order by gamets desc");
	
	$finalParsedData[3]=$PROMPTS["book"][1]." ".$books[0]["title"];

}  else if ( (strpos($finalParsedData[0],"chatnf")!==false)) {

	$request = $PROMPTS[$finalParsedData[0]][0];


}

$request = str_replace("specify action for $HERIKA_NAME or","",$request);	// Make better

$preprompt=preg_replace("/^[^:]*:/", "", $finalParsedData[3]);
$lastNDataForContext=(isset($GLOBALS["CONTEXT_HISTORY"])) ? ($GLOBALS["CONTEXT_HISTORY"]) : "25";
$contextData = $db->lastDataFor("",$lastNDataForContext*-1);
$head = array();
$foot = array();


/* Memory offering */
if (isset($GLOBALS["MEMORY_EMBEDDING"]) && $GLOBALS["MEMORY_EMBEDDING"]) {
	if (($finalParsedData[0] == "inputtext") || ($finalParsedData[0] == "inputtext_s")) {
		$memory=array();
		
		$textToEmbed=str_replace($DIALOGUE_TARGET,"",$finalParsedData[3]);
		$pattern = '/\([^)]+\)/';
		$textToEmbedFinal = preg_replace($pattern, '', $textToEmbed);
		$textToEmbedFinal=str_replace("{$GLOBALS["PLAYER_NAME"]}:","",$textToEmbedFinal);

		$embeddings=getEmbeddingRemote($textToEmbedFinal);
		$memories=queryMemory($embeddings);
		if ($memories["content"][0]) {
			//$memories["content"][0]["search_term"]=$textToEmbedFinal;
			$contextData[]=['role' => 'user', 'content' => "The Narrator: Past related memories of {$GLOBALS["HERIKA_NAME"]}'s :".json_encode($memories["content"]) ];
		}
	}
}
/**/

$head[] = array('role' => 'system', 'content' => '('.$PROMPT_HEAD.$GLOBALS["HERIKA_PERS"]);
$prompt[] = array('role' => $LAST_ROLE, 'content' => $request);
$foot[] = array('role' => 'user', 'content' => $GLOBALS["PLAYER_NAME"].':' . $preprompt);

if (!$preprompt)
	$parms = array_merge($head, ($contextData), $prompt);
else
	//$parms = array_merge($head, ($contextData), $foot, $prompt);
	$parms = array_merge($head, ($contextData),  $prompt);


//// DIRECT OPENAI REST API

if ( (!isset($GLOBALS["MODEL"]) || ($GLOBALS["MODEL"]=="openai"))) {
	$url = $GLOBALS["OPENAI_URL"];
	$data = array(
		'model' => (isset($GLOBALS["GPTMODEL"]))?$GLOBALS["GPTMODEL"]:'gpt-3.5-turbo-0613',
		'messages' => 
			$parms
		,
		'stream' => true,
		'max_tokens'=>((isset($GLOBALS["OPENAI_MAX_TOKENS"])?$GLOBALS["OPENAI_MAX_TOKENS"]:48)+0)
		
	);


	$headers = array(
		'Content-Type: application/json',
		"Authorization: Bearer {$GLOBALS["OPENAI_API_KEY"]}"
	);

	$options = array(
		'http' => array(
			'method' => 'POST',
			'header' => implode("\r\n", $headers),
			'content' => json_encode($data)
		)
	);
	error_reporting(E_ALL);
	$context = stream_context_create($options);
	$handle = fopen($url, 'r', false, $context);
	$GLOBALS["DEBUG_DATA"][]=$parms;


} else if ( (isset($GLOBALS["MODEL"]) || ($GLOBALS["MODEL"]=="koboldcpp")))  {
	$GLOBALS["DEBUG_DATA"]=[];//reset

	$url=$GLOBALS["KOBOLDCPP_URL"].'/api/v1/generate/';
	$context="";

	foreach ($parms as $s_role=>$s_msg) {	// Have to mangle context format

		if (empty(trim($s_msg["content"])))
			continue;
		else
			$normalizedContext[]=$s_msg["content"];
	}	

	foreach ($normalizedContext as $n=>$s_msg) {
		if ($n==(sizeof($normalizedContext)-1)) {
			$context.="[Author's notes: ".$s_msg."]";
			$GLOBALS["DEBUG_DATA"][]="[Author's notes: ".$s_msg."]";

		} else {
			$s_msg_p = preg_replace('/^(?=.*The Narrator).*$/s', '[Author\'s notes: $0 ]', $s_msg);
			$context.="$s_msg_p\n";
			$GLOBALS["DEBUG_DATA"][]=$s_msg_p;
		}
		
	}
	$context.="\n{$GLOBALS["HERIKA_NAME"]}:";
	//$GLOBALS["DEBUG_DATA"]=explode("\n",$context);
	$postData = array(
		
		"prompt"=>$context,
		"temperature"=> 0.9,
		"top_p"=> 0.9,
		"max_context_length"=>1024,
		"max_length"=>80,
		"rep_pen"=>1.1,
		"stop_sequence"=>["{$GLOBALS["PLAYER_NAME"]}:","\\n{$GLOBALS["PLAYER_NAME"]} ","The Narrator","\n"]
	);
		
	


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
	$handle = fopen($url, 'r', false, $context);
	
	
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

    while (true) {
		
		if ($breakFlag)
			break;
		
		if ( (!isset($GLOBALS["MODEL"]) || ($GLOBALS["MODEL"]=="openai"))) {
			$line = fgets($handle);
			

			file_put_contents("debugStream.log",$line,FILE_APPEND);

			$data=json_decode(substr($line,6),true);
			if (isset($data["choices"][0]["delta"]["content"])) {
				if (strlen(trim($data["choices"][0]["delta"]["content"]))>0)
					$buffer.=$data["choices"][0]["delta"]["content"];
			$totalBuffer.=$data["choices"][0]["delta"]["content"];
			}

		} else if ( (isset($GLOBALS["MODEL"]) || ($GLOBALS["MODEL"]=="koboldcpp")))  {
			
			$headers = array(
				'Content-Type: application/json',
				'Content-Length: 0',
			);

			$options = array(
				'http' => array(
					'method' => 'POST',
					'header' => implode("\r\n", $headers),
				)
			);
			
			error_reporting(E_ERROR);
			$context = stream_context_create($options);	
			$fullContent = file_get_contents("{$GLOBALS["KOBOLDCPP_URL"]}/api/extra/generate/check",false,$context);

			if (feof($handle)) {
				$breakFlag=true;
				continue;
			} else
				fread($handle, 1024);	// Flush input buffer at primary call.
			
			
			
			$data=json_decode($fullContent,true);
		
			$buffer=$data["results"][0]["text"];

			//consoleLog("$buffer vs $oldBuffer");


			if (!empty($totalProcessedData)) {
					$buffer=str_replace($totalProcessedData,"",$buffer);
				
			}
			if (strrpos($buffer,".")<$MINIMUM_SENTENCE_SIZE) {
				continue;
			}

			//$oldBuffer.=$buffer;
			$totalBuffer.=$buffer;


			//$GLOBALS["DEBUG_DATA"]["response"][]=$data;	

		}

		$buffer=strtr($buffer,array("\""=>""));
	   
		if ( (!isset($GLOBALS["MODEL"]) || ($GLOBALS["MODEL"]=="openai"))) {
			if (feof($handle))
				$breakFlag=true;
		} else if ( (isset($GLOBALS["MODEL"]) || ($GLOBALS["MODEL"]=="koboldcpp")))  {

				
		}
		
		if (strlen($buffer)<$MINIMUM_SENTENCE_SIZE)	// Avoid too short buffers
			continue;
		
		$position = findDotPosition($buffer);
		
        if ($position !== false) {
            $extractedData = substr($buffer, 0, $position + 1);
            $remainingData = substr($buffer, $position + 1);
            $sentences=split_sentences_stream(cleanReponse($extractedData));
			$GLOBALS["DEBUG_DATA"][]=(microtime(true) - $starTime)." secs in openai stream";
            returnLines($sentences);
            //echo "$extractedData  # ".(microtime(true)-$starTime)."\t".strlen($finalData)."\t".PHP_EOL;  // Output
            $extractedData="";
            $buffer=$remainingData;
            
        }
		
    }
    if (trim($buffer)) {
		 $sentences=split_sentences_stream(cleanReponse(trim($buffer)));
		 $GLOBALS["DEBUG_DATA"][]=(microtime(true) - $starTime)." secs in openai stream";
         returnLines($sentences);
		 $totalBuffer.=trim($buffer);
		
	}
    fclose($handle);
	//fwrite($fileLog, $totalBuffer . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE
}

if (!$ERROR_TRIGGERED) {
		$lastPlayerLine=$db->fetchAll("SELECT data from eventlog where type in ('inputtext','inputtext_s') order by gamets desc limit 0,1");
		logMemory($GLOBALS["HERIKA_NAME"],$GLOBALS["PLAYER_NAME"],"{$lastPlayerLine[0]["data"]} \n\r {$GLOBALS["HERIKA_NAME"]}:".implode(" ",$talkedSoFar),$momentum,$finalParsedData[2]);
	}

file_put_contents("log_stream.txt",$totalBuffer,FILE_APPEND);
	

echo 'X-CUSTOM-CLOSE';
//echo "\r\n<$totalBuffer>";
?>
