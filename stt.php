<?php

//file_put_contents(__DIR__.DIRECTORY_SEPARATOR."soundcache/test.wav",base64_decode($_POST["wavData"]));

$finalName=__DIR__.DIRECTORY_SEPARATOR."soundcache/".md5($_FILES["file"]["tmp_name"]).".wav";

@copy($_FILES["file"]["tmp_name"] ,$finalName);


$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR ;
require_once($path . "conf.php");
require_once($path . "vendor/autoload.php");
require_once($path . "lib/$DRIVER.class.php");
require_once($path . "lib/Misc.php");


$client = OpenAI::client($GLOBALS["OPENAI_API_KEY"]);
 
$response = $client->audio()->transcribe([
    'model' => 'whisper-1',
    'file' => fopen($finalName, 'r'),
    'response_format' => 'verbose_json',
]);

echo $response["text"];

?>

