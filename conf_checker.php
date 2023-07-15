<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

@mkdir("tmp");
$fileName=__DIR__.DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.uniqid()."conf.check.php";

file_put_contents($fileName,$_POST["text"]);

echo '<!DOCTYPE html>
<html lang="en" >
<head>
<style>
body {
  background-color: white;
  color: black;
  font-size: small ; 
  font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
  width: 100%;
  display: inline-block;
}
</style>
</head>
<body>
';

echo "Checking syntax...<br/>";
try {
    require_once($fileName);
} catch (Exception $e) {
    echo $e->getMessage();
    echo "Errors";
    unlink($fileName);    
    die();
}

echo "Checking patterns...<br/>";
$input = file_get_contents($fileName);
$pattern = '/<\?php\s+(.*?)\?>/s';
preg_match($pattern, $input, $matches);

if (isset($matches[1])) {
    $matchedText = $matches[1];
} else {
    unlink($fileName);    
    die("Seems to be an unexpected general error. Check opening <strong>".htmlentities("<?php")."</strong> and <strong>".htmlentities("?>")."</strong> tags ");
}

echo "Checking needed vars...<br/>";

if (!$OPENAI_API_KEY) {
        echo "Fatal: \$OPENAI_API_KEY missing<br/>";
        die("");
}

$NEEDED_VARS=["PLAYER_NAME","HERIKA_NAME","HERIKA_PERS","PROMPT_HEAD"];
$errorFlag=false;

foreach ($NEEDED_VARS as $var) {
    if (!isset($GLOBALS[$var])) {
            echo "Needed var $var not found<br/>";
            $errorFlag=true;
    } else
        echo "$var found <br/>";  
}

if (!$TTSFUNCTION) {
    echo "Note: No TTS service configured \$TTSFUNCTION missing<br/>";
} else
    echo "Using  TTS service <strong>$TTSFUNCTION</strong> <br/>";
if (!$STTFUNCTION) {
        echo "Note: No STT service configured \$STTFUNCTION missing<br/>";
} else
    echo "Using  STT service <strong>$STTFUNCTION</strong> <br/>";

if ($TTSFUNCTION=="azure")
    if (!isset($AZURE_API_KEY)) {
        echo "Error: Azure is in use but \$AZURE_API_KEY not found <br/>";
        $errorFlag=true;
    }
    
if ($TTSFUNCTION=="mimic3")
    if (!isset($MIMIC3)) {
        echo "Error: MIMIC3 is in use but \$MIMIC3 (URL) not found <br/>";
        $errorFlag=true;
    }

if ($TTSFUNCTION=="gcp")
    if (!isset($GCP_CONF)) {
        echo "Error: Google Cloud Platform is in use but \$GCP_CONF not found <br/>";
        $errorFlag=true;
    }
    
if ($TTSFUNCTION=="11labs")
    if (!isset($ELEVENLABS_API_KEY)) {
        echo "Error: Elevenlabs is in use but \$ELEVENLABS_API_KEY  not found <br/>";
        $errorFlag=true;
    }

if ($STTFUNCTION=="azure")
    if (!isset($AZURE_API_KEY)) {
        echo "Error: Azure is in use for speech to text but \$AZURE_API_KEY not found <br/>";
        $errorFlag=true;
    }
    
if ($STTFUNCTION=="whisper")
    if (!isset($OPENAI_API_KEY)) {
        echo "Error: Whisper is in use but \$OPENAI_API_KEY not found <br/>";
        $errorFlag=true;
    }

if ($STTFUNCTION=="localwhisper")
    if (!isset($LOCALWHISPER["URL"])) {
        echo "Error: Local Whisper is in use but \$LOCALWHISPER[\"URL\"] not found <br/>";
        $errorFlag=true;
    }
    
    
if (!$errorFlag)
echo "<strong>Everything seems ok! You can safely save now</strong>\n";



?>
