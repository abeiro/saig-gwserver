<?php

$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
require_once($path . "conf.php");
require_once($path . "dynmodel.php");
require_once($path . "vendor/autoload.php");
require_once($path . "lib/$DRIVER.class.php");
require_once($path . "lib/Misc.php");


// Preparing data to send

function requestGeneric($request, $preprompt = '', $queue = 'AASPGQuestDialogue2Topic1B1Topic', $lastDataAmount = 10, $tag = '')
{
    global $db;

    $PROMPT_HEAD = ($GLOBALS["PROMPT_HEAD"]) ? $GLOBALS["PROMPT_HEAD"] : "Let\'s roleplay in the Universe of Skyrim. I\'m {$GLOBALS["PLAYER_NAME"]} ";

    require_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "command_prompt.php"); //$COMMAND_PROMPT_SHORT

    //$PROMPT_RULES = $COMMAND_PROMPT_SHORT;

    $starTime = microtime(true);

    $historic = $db->lastDataFor("", $lastDataAmount * -1);
    $contextCurrentPlan[] = array('role' => 'user', 'content' => 'The Narrator: (' . $db->get_current_task() . ')');

    $head = array();
    $foot = array();

    $head[] = array('role' => 'user', 'content' => '(' . $PROMPT_HEAD . $GLOBALS["HERIKA_PERS"] . $PROMPT_RULES);
    $prompt[] = array('role' => 'user', 'content' => $request);
    $foot[] = array('role' => 'user', 'content' => $preprompt);
    //$foot[] = array('role' => 'user', 'content' => $GLOBALS["PLAYER_NAME"].':' . $preprompt);


    if (!$preprompt) {
        $parms = array_merge($head, $contextCurrentPlan, ($historic), $prompt);
    } else {
        $parms = array_merge($head, $contextCurrentPlan, ($historic), $foot, $prompt);
    }
    //// OPENAI CODE



    if ((!isset($GLOBALS["MODEL"]) || ($GLOBALS["MODEL"] == "openai"))) {

        $data = [
            'model' => (isset($GLOBALS["GPTMODEL"])) ? $GLOBALS["GPTMODEL"] : 'gpt-3.5-turbo-0613',
            'messages' => $parms,
            'max_tokens' => ((isset($GLOBALS["OPENAI_MAX_TOKENS"]) ? $GLOBALS["OPENAI_MAX_TOKENS"] : 48) + 0)
        ];

        $sentence = "";
        $errorFlag = false;
        $startTime = time();

        $headers = array(
            'Content-Type: application/json',
            "Authorization: Bearer {$GLOBALS["OPENAI_API_KEY"]}"
        );
        $jsonEncodedData = json_encode($data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $jsonEncodedData,
                'timeout' => ($GLOBALS["HTTP_TIMEOUT"]) ?: 30
            )
        );

        // call into tokenizer and tokenize request as part of OpenAI cost monitoring - save result in DB
        tokenizePrompt($jsonEncodedData);

        $url = $GLOBALS["OPENAI_URL"];

        $context = stream_context_create($options);
        error_reporting(E_ALL);
        $handle = fopen($url, 'r', false, $context);
        if (!$handle)
            die("Error " . print_r(error_get_last(), true));

        $buffer = "";

        while (!feof($handle)) {
            $buffer .= fread($handle, 1024);
        }
        $response = json_decode($buffer, true);
        $rawResponse = $response["choices"][0]["message"]["content"];
        $sentence = cleanReponse($rawResponse);


    } else if ($GLOBALS["MODEL"] == "koboldcpp") {
        $GLOBALS["DEBUG_DATA"] = []; //reset

        $context = "";

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
        $context.="\n### Response:";
        $GLOBALS["DEBUG_DATA"][]="\n### Response:";
        $MAX_TOKENS=((isset($GLOBALS["KOBOLDCPP_MAX_TOKENS"])?$GLOBALS["KOBOLDCPP_MAX_TOKENS"]:80)+0);
		$REP_PEN=((isset($GLOBALS["KOBOLDCPP_REP_PEN"])?$GLOBALS["KOBOLDCPP_REP_PEN"]:1.12)+0);
		$TOP_P=((isset($GLOBALS["KOBOLDCPP_TOP_P"])?$GLOBALS["KOBOLDCPP_TOP_P"]:0.9)+0);
        $TEMPERATURE=((isset($GLOBALS["KOBOLDCPP_TEMPERATURE"])?$GLOBALS["KOBOLDCPP_TEMPERATURE"]:0.9)+0);
        $postData = array(

            "prompt" => $context,
            "temperature" => $TEMPERATURE,
            "top_p" => $TOP_P,
            "max_length" => $MAX_TOKENS,
            "rep_pen" => $REP_PEN,
            "stop_sequence" => ["{$GLOBALS["PLAYER_NAME"]}:", "\\n{$GLOBALS["PLAYER_NAME"]} ", "The Narrator", "\n"]
        );

        $dataJson = json_encode($postData);

        $headers = array(
            'Content-Type: application/json',
            "Content-Length: " . strlen($dataJson)
        );

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $dataJson,
                'timeout' => ($GLOBALS["HTTP_TIMEOUT"]) ?: 30
            )
        );

        $host = parse_url($GLOBALS["KOBOLDCPP_URL"], PHP_URL_HOST);
        $port = parse_url($GLOBALS["KOBOLDCPP_URL"], PHP_URL_PORT);
        $path = '/api/v1/generate/';

        // Data to send in JSON format
       
        $context = stream_context_create($options);
        error_reporting(E_ALL);
        $handle = fopen($GLOBALS["KOBOLDCPP_URL"].'/api/v1/generate/', 'r', false, $context);

        if (!$handle)
            die("Error " . print_r(error_get_last(), true));

        $buffer = "";

        while (!feof($handle)) {
            $buffer .= fread($handle, 1024);
        }
        $response = json_decode($buffer, true);
   
        $rawResponse = $response["results"][0]["text"];
        $sentence = cleanReponse($rawResponse);

    }




    $GLOBALS["DEBUG_DATA"]["OPENAI_PARMS"] = $parms;
    if (!$errorFlag) {
        $GLOBALS["DEBUG_DATA"]["OPENAI_DATA"] = $response;
    }


    $GLOBALS["DEBUG_DATA"]["OPENAI_LAG"] = (microtime(true) - $starTime);


    //$modifiedSentence = preg_replace("/\.+/", ".", $sentence);  // Get ride of the double point issue
    $modifiedSentence = preg_replace("/(?<!\.)\.{2}(?!\.)/", ".", $sentence); // Get ride of the double point issue, leaving ...

    $sentence = $modifiedSentence;

    $responseTextUnmooded = preg_replace('/\((.*?)\)/', '', $sentence);

    return trim(preg_replace('/\s\s+/', ' ', $sentence));
    // Final result.
    if ($GLOBALS["DEBUG_MODE"]) {
        echo "# $sentence #\n";
    }
    // Action is the plugin queue which will store the response


}
