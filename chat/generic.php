<?php
$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
require_once($path . "conf.php");
require_once($path . "vendor/autoload.php");
require_once($path . "lib/$DRIVER.class.php");
require_once($path . "lib/Misc.php");




// Preparing data to send

function requestGeneric($request, $preprompt = '', $queue = 'AASPGQuestDialogue2Topic1B1Topic',$lastDataAmount=10,$tag='')
{
    global $db;
    $client = OpenAI::client($GLOBALS["OPENAI_API_KEY"]);

    
    $historic = $db->lastDataFor("",$lastDataAmount*-1);
    $head = array();
    $foot = array();

    $head[] = array('role' => 'user', 'content' => '(Let\'s roleplay in the Universe of Skyrim. I\'m '.$GLOBALS["PLAYER_NAME"].'. '.$GLOBALS["HERIKA_PERS"]);
    $prompt[] = array('role' => 'assistant', 'content' => $request);
    $foot[] = array('role' => 'user', 'content' => $GLOBALS["PLAYER_NAME"].': ' . $preprompt);

    if (!$preprompt)
        $parms = array_merge($head, ($historic), $prompt);
    else
        $parms = array_merge($head, ($historic), $foot, $prompt);
    //// OPENAI CODE
    $callParms = [
        'model' => 'gpt-3.5-turbo',
        'messages' => $parms,
        'max_tokens' => ((isset($GLOBALS["OPENAI_MAX_TOKENS"])?$GLOBALS["OPENAI_MAX_TOKENS"]:48)+0)
    ];

    $sentence="";
    $errorFlag=false;
    $startTime=time();
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
        $sentence="Error ".$e->getMessage();
        $errorFlag=true;
    }
 
    
  
    //$modifiedSentence = preg_replace("/\.+/", ".", $sentence);  // Get ride of the double point issue
    $modifiedSentence = preg_replace("/(?<!\.)\.{2}(?!\.)/", ".", $sentence); // Get ride of the double point issue, leaving ...

    $sentence=$modifiedSentence;

    $responseTextUnmooded = preg_replace('/\((.*?)\)/', '', $sentence);
    // Final result.
    if ($GLOBALS["DEBUG_MODE"])
        echo "# $sentence #\n";
    // Action is the plugin queue which will store the response
    if ($sentence) {
        if (!$errorFlag)
            $db->insert(
                'responselog',
                array(
                    'localts' => time(),
                    'sent' => 0,
                    'text' => trim(preg_replace('/\s\s+/', ' ', SQLite3::escapeString($responseTextUnmooded))),
                    'actor' => "Herika",
                    'action' => $queue,
                    'tag'=>$tag
                )
            );
        $db->insert(
            'log',
            array(
                'localts' => time(),
                'prompt' => nl2br(SQLite3::escapeString(print_r($parms,true))),
                'response' => (SQLite3::escapeString(print_r($rawResponse,true).$responseTextUnmooded)),
                'url' => nl2br(SQLite3::escapeString(print_r( base64_decode(stripslashes($_GET["DATA"])),true)." in ".(time()-$startTime)." secs " ))
                  
               
            )
        );

        return trim(preg_replace('/\s\s+/', ' ', $sentence));
    } else {
        $db->insert(
            'log',
            array(
                'localts' => time(),
                'prompt' => nl2br(SQLite3::escapeString(print_r($parms,true))),
                'response' => (SQLite3::escapeString(print_r($rawResponse,true))),
                'url' => nl2br(SQLite3::escapeString(print_r( base64_decode(stripslashes($_GET["DATA"])),true)." in ".(time()-$startTime)." secs with ERROR STATE" ))
                  
               
            )
        );

    }

}

?>
