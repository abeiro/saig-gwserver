<?php


$DRIVER="sql";
$OPENAI_API_KEY=""; //OpenAI API key here
$AZURE_API_KEY="";  //Azure Free: https://azure.microsoft.com/en-us/try/cognitive-services/?api=speech-services

$DEBUG_MODE=false;
$PLAYER_NAME="Prisoner";
$HERIKA_PERS="You are Herika,a breton female who likes jokes and sarcastic comments";


// Azure TTS stuff
$AZURETTS_CONF["fixedMood"]="default";			// Azure TTS prosody and style. Empty to stay variable.
$AZURETTS_CONF["region"]="westeurope";			// Region, Fine tune to improve reponse time. https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/regions
$AZURETTS_CONF["voice"]="en-US-NancyNeural";	// Voice. Read https://learn.microsoft.com/es-es/azure/cognitive-services/speech-service/language-support?tabs=tts
$AZURETTS_CONF["volume"]="25";					// Default volume
$AZURETTS_CONF["rate"]="1.2";					// Default rate (speed)
$AZURETTS_CONF["countour"]=
	"(11%, +15%) (60%, -23%) (80%, -34%)";		//			


?>
