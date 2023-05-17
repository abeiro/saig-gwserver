<?php

$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR;
require_once($path . "conf.php"); // API KEY must be there

$finalName=__DIR__.DIRECTORY_SEPARATOR."soundcache/".md5($_FILES["file"]["tmp_name"]).".wav";

@copy($_FILES["file"]["tmp_name"] ,$finalName);


if ($STTFUNCTION=="azure") {
    
    require_once($path."stt/stt-azure.php");
    $text= stt($finalName);
    
} else if ($STTFUNCTION=="whisper") { 

    require_once($path."stt/stt-whisper.php");
    $text= stt($finalName);
    
}

echo $text;

?>

