<?php

//READ THE COMMENTS TO LEARN WHAT EACH VARIABLE DOES

//Enter API keys here
//DO NOT SHARE YOUR API KEYS WITH ANYONE!
$DRIVER="sql";
$OPENAI_API_KEY=""; //OpenAI API key here
$AZURE_API_KEY="";  //Azure API key here

//Player and Personality configuration. 
//If you are using a large personality description you will need to increase the API_MAX_TOKENS amount
//DONT USE "" (quotation marks) within the HERIKA_PERS variable otherwise it will break the conf.php file!
$DEBUG_MODE=false;
$PLAYER_NAME="Prisoner";
$HERIKA_PERS="You are Herika, a Breton female who likes jokes and sarcastic comments.";
$PROMPT_HEAD="Let\'s roleplay in the Universe of Skyrim. I\'m {$GLOBALS["PLAYER_NAME"]}. You dont describe things or actions, just chat as your character";

//Azure TTS Configuration
//More Azure TTS presets can be found in the Article section for the mod page
//This configuration will make Herika sound like how she does in the Dwemer Dynamics videos
$AZURETTS_CONF["fixedMood"]="";			// Azure TTS prosody and style. Empty to stay variable.
$AZURETTS_CONF["region"]="westeurope";			// Region, Fine tune to improve reponse time. https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/regions
$AZURETTS_CONF["voice"]="en-US-NancyNeural";	// Voice. Read https://learn.microsoft.com/es-es/azure/cognitive-services/speech-service/language-support?tabs=tts
$AZURETTS_CONF["volume"]="25";					// Default volume
$AZURETTS_CONF["rate"]="1.2";					// Default rate (speed)
$AZURETTS_CONF["countour"]= "(11%, +15%) (60%, -23%) (80%, -34%)";		//Algorithim to change pitch during speech. 
//Read this and scroll down to "Prosody" for more info on these configurations: https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/speech-synthesis-markup-voice		

// If using MIMIC3, comment out $AZURE_API_KEY and uncomment $MIMIC3
//$MIMIC3="http://127.0.0.1:59125";               // Don't set if using Azure.
$MIMIC3_CONF["voice"]="en_US/ljspeech_low";
$GLOBALS["MIMIC3_CONF"]["rate"]="1.25";
$GLOBALS["MIMIC3_CONF"]["volume"]="80";

//Allows you to toogle which providers you use for Text-to-Speech or Speech-to-Text
$STTFUNCTION="azure";								// Valid options are azure or whisper so far
$TTSFUNCTION="azure";								// Valid options are azure or mimic3 so far

//WIP configuration for changing default language for TTS
$TTSLANGUAGE_AZURE="en-US";							// en-US, es-ES formats
$TTSLANGUAGE_WHISPER="en";							// en, es formats

//Large Value = Longer responses from Herika (she can speak in Paragraphs). However this will increase API useage cost!
//Setting it to 100 is a good starting point for expirmenting with larger reponses.
$OPENAI_MAX_TOKENS="48";							// Limit size of reponses. 

?>
