<?php
$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
require_once($path . "conf.php"); // API KEY must be there

require_once($path . "vendor/autoload.php");
require_once($path . "lib/$DRIVER.class.php");
require_once($path . "lib/Misc.php");


function stt($file) {
  $client = OpenAI::client($GLOBALS["OPENAI_API_KEY"]);
  $lang=($GLOBALS["TTSLANGUAGE_WHISPER"])?$GLOBALS["TTSLANGUAGE_WHISPER"]:"en";

  $response = $client->audio()->transcribe([
      'model' => 'whisper-1',
      'file' => fopen($file, 'r'),
      'response_format' => 'verbose_json',
      'language'=>$GLOBALS["TTSLANGUAGE_WHISPER"],
      'prompt'=>"{$GLOBALS["HERIKA_NAME"]}, the Dragonborns are taking the Nords to Whiterun! Check that bard\'s lute! Fus Ro Dah!"
  ]);

  return $response["text"];

}

?>
