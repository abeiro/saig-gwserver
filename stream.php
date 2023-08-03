<?php
error_reporting(E_ERROR);

define("MAXIMUM_SENTENCE_SIZE", 125);

$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR;
require_once($path . "conf.php");
require_once($path . "lib/$DRIVER.class.php");
require_once($path . "lib/Misc.php");
$db = new sql();

while (@ob_end_clean());

ignore_user_abort(true);
set_time_limit(1200);

$startTime=time();


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
	
	global $db,$startTime,$forceMood,$staticMood;
	foreach ($lines as $n=>$sentence) {

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
} 

$preprompt=preg_replace("/^[^:]*:/", "", $finalParsedData[3]);
$lastNDataForContext=(isset($GLOBALS["CONTEXT_HISTORY"])) ? ($GLOBALS["CONTEXT_HISTORY"]) : "25";
$contextData = $db->lastDataFor("",$lastNDataForContext*-1);
$head = array();
$foot = array();

$head[] = array('role' => 'user', 'content' => '('.$PROMPT_HEAD.$GLOBALS["HERIKA_PERS"]);
$prompt[] = array('role' => 'user', 'content' => $request);
$foot[] = array('role' => 'user', 'content' => $GLOBALS["PLAYER_NAME"].':' . $preprompt);

if (!$preprompt)
	$parms = array_merge($head, ($contextData), $prompt);
else
	$parms = array_merge($head, ($contextData), $foot, $prompt);

$GLOBALS["DEBUG_DATA"][]=$parms;

//// DIRECT OPENAI REST API
	
$url = 'https://api.openai.com/v1/chat/completions';
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
    while (!feof($handle)) {
        $line = fgets($handle);
	    
		
		
        $data=json_decode(substr($line,6),true);
        if (isset($data["choices"][0]["delta"]["content"])) {
            if (strlen(trim($data["choices"][0]["delta"]["content"]))>0)
                $buffer.=$data["choices"][0]["delta"]["content"];
        $totalBuffer.=$data["choices"][0]["delta"]["content"];
		}
       
       $buffer=strtr($buffer,array("\""=>""));
	   
		if (strlen($buffer)<MAXIMUM_SENTENCE_SIZE)	// Avoid too short buffers
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
		
	}
    fclose($handle);
	//fwrite($fileLog, $totalBuffer . PHP_EOL); // Write the line to the file with a line break // DEBUG CODE
}


echo 'X-CUSTOM-CLOSE';
//echo "\r\n<$totalBuffer>";
?>
