<?php
$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
require_once($path . "conf.php");
require_once($path . "vendor/autoload.php");
require_once($path . "lib/$DRIVER.class.php");
require_once($path . "lib/Misc.php");




// Preparing data to send

function requestGeneric($request, $preprompt = '', $queue = 'AASPGQuestDialogue2Topic1B1Topic',$lastDataAmount=10,$tag='')
{
    $db = new sql();
    $client = OpenAI::client($GLOBALS["OPENAI_API_KEY"]);

    
    $historic = $db->lastDataFor("",$lastDataAmount*-1);
    $head = array();
    $foot = array();

    $head[] = array('role' => 'user', 'content' => '(Let\'s roleplay in the Universe of Skyrim. I\'m '.$GLOBALS["PLAYER_NAME"].'. You are Herika,a breton female who likes jokes and sarcastic comments)');
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
        'max_tokens' => 48
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
 
    
  

    // Final result.
    if ($GLOBALS["DEBUG_MODE"])
        echo "# $sentence #\n";
    // Action is the queue plugin will store the response
    if ($sentence) {
        if (!$errorFlag)
            $db->insert(
                'responselog',
                array(
                    'localts' => time(),
                    'sent' => 0,
                    'text' => trim(preg_replace('/\s\s+/', ' ', SQLite3::escapeString($sentence))),
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
                'response' => nl2br(SQLite3::escapeString(print_r($rawResponse,true))),
                'url' => nl2br(SQLite3::escapeString(print_r( base64_decode(stripslashes($_GET["DATA"])),true)." in ".(time()-$startTime)." secs " ))
                  
               
            )
        );
    }

}

?>
