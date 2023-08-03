<?php

$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
require_once($path . "conf.php");
require_once($path . "vendor/autoload.php");
require_once($path . "lib/$DRIVER.class.php");
require_once($path . "lib/Misc.php");


// Preparing data to send

function requestGeneric($request, $preprompt = '', $queue = 'AASPGQuestDialogue2Topic1B1Topic', $lastDataAmount=10, $tag='')
{
    global $db;
    $client = OpenAI::client($GLOBALS["OPENAI_API_KEY"]);

    $PROMPT_HEAD=($GLOBALS["PROMPT_HEAD"]) ? $GLOBALS["PROMPT_HEAD"] : "Let\'s roleplay in the Universe of Skyrim. I\'m {$GLOBALS["PLAYER_NAME"]} ";

    require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."command_prompt.php");//$COMMAND_PROMPT_SHORT

    $PROMPT_RULES= $COMMAND_PROMPT_SHORT;

    $starTime=microtime(true);

    $historic = $db->lastDataFor("", $lastDataAmount*-1);
    $contextCurrentPlan[]=  array('role' => 'user', 'content' => 'The Narrator: (' .$db->get_current_task().')');

    $head = array();
    $foot = array();

    $head[] = array('role' => 'user', 'content' => '('.$PROMPT_HEAD.$GLOBALS["HERIKA_PERS"].$PROMPT_RULES);
    $prompt[] = array('role' => 'user', 'content' => $request);
    $foot[] = array('role' => 'user', 'content' => $GLOBALS["PLAYER_NAME"].':' . $preprompt);


    if (!$preprompt) {
        $parms = array_merge($head, $contextCurrentPlan, ($historic), $prompt);
    } else {
        $parms = array_merge($head, $contextCurrentPlan, ($historic), $foot, $prompt);
    }
    //// OPENAI CODE
    $data = [
        'model' => (isset($GLOBALS["GPTMODEL"]))?$GLOBALS["GPTMODEL"]:'gpt-3.5-turbo-0613',
        'messages' => $parms,
        'max_tokens' => ((isset($GLOBALS["OPENAI_MAX_TOKENS"]) ? $GLOBALS["OPENAI_MAX_TOKENS"] : 48)+0)
    ];

    $sentence="";
    $errorFlag=false;
    $startTime=time();

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


    $url = 'https://api.openai.com/v1/chat/completions';

    $context = stream_context_create($options);
    error_reporting(E_ALL);
    $handle = fopen($url, 'r', false, $context);
    if (!$handle)
        die("Error ".print_r(error_get_last(),true));
    
    $buffer="";
    
    while (!feof($handle)) {
        $buffer.=fread($handle, 1024);
    }
    $response=json_decode($buffer, true);
    $rawResponse = $response["choices"][0]["message"]["content"];
    $sentence = cleanReponse($rawResponse);
    /*

        try {
            $response = $client->chat()->create($callParms);
               // PARSE RESPONSE
            if ($GLOBALS["DEBUG_MODE"]) {
                print_r($callParms);
                print_r($response->toArray());
            }
            // What we want
            $rawResponse = $response->toArray()["choices"][0]["message"]["content"];

            $sentence = cleanReponse($rawResponse);
        } catch (Exception $e) {
            $GLOBALS["DEBUG_DATA"]["OPENAI_RESPONSE"][]=$e->getMessage();
            $errorFlag=true;
        }
    */


    $GLOBALS["DEBUG_DATA"]["OPENAI_PARMS"]=$parms;
    if (!$errorFlag) {
        $GLOBALS["DEBUG_DATA"]["OPENAI_DATA"]=$response;
    }


    $GLOBALS["DEBUG_DATA"]["OPENAI_LAG"]=(microtime(true)-$starTime);


    //$modifiedSentence = preg_replace("/\.+/", ".", $sentence);  // Get ride of the double point issue
    $modifiedSentence = preg_replace("/(?<!\.)\.{2}(?!\.)/", ".", $sentence); // Get ride of the double point issue, leaving ...

    $sentence=$modifiedSentence;

    $responseTextUnmooded = preg_replace('/\((.*?)\)/', '', $sentence);

    return trim(preg_replace('/\s\s+/', ' ', $sentence));
    // Final result.
    if ($GLOBALS["DEBUG_MODE"]) {
        echo "# $sentence #\n";
    }
    // Action is the plugin queue which will store the response


}
