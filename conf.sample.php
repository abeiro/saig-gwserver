<?php


$DRIVER="sql";
$OPENAI_API_KEY=""; //OpenAI API key here
$AZURE_API_KEY="";  //Azure Free: https://azure.microsoft.com/en-us/try/cognitive-services/?api=speech-services

$DEBUG_MODE=false;
$PLAYER_NAME="Prisoner";
$HERIKA_PERS="You are Herika,a breton female who likes jokes and sarcastic comments";

// Azure TTS Stuff. Copy desired block to conf.php (only one block)
// This conf is for variable mood
$AZURETTS_CONF["fixedMood"]="";					// Azure TTS prosody and style. Empty to stay variable.
$AZURETTS_CONF["region"]="westeurope";			// Region, Fine tune to improve reponse time. https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/regions
$AZURETTS_CONF["voice"]="en-US-JennyNeural";	// Voice. Read https://learn.microsoft.com/es-es/azure/cognitive-services/speech-service/language-support?tabs=tts
$AZURETTS_CONF["volume"]="20";					// Default volume
$AZURETTS_CONF["rate"]="1.25";					// Default rate (speed)
$AZURETTS_CONF["countour"]="";					

// This conf is for variable mood is mastered by rang97. 

$AZURETTS_CONF["fixedMood"]="default";			// Azure TTS prosody and style. Empty to stay variable.
$AZURETTS_CONF["region"]="westeurope";			// Region, Fine tune to improve reponse time. https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/regions
$AZURETTS_CONF["voice"]="en-US-NancyNeural";	// Voice. Read https://learn.microsoft.com/es-es/azure/cognitive-services/speech-service/language-support?tabs=tts
$AZURETTS_CONF["volume"]="25";					// Default volume
$AZURETTS_CONF["rate"]="1.2";					// Default rate (speed)
$AZURETTS_CONF["countour"]=
	"(11%, +15%) (60%, -23%) (80%, -34%)";		//				


?>
