<?php

//READ THE COMMENTS TO LEARN WHAT EACH VARIABLE DOES
//DO NOT COMMENT OUT ANY VARIABLES, YOU MAY BREAK THE MOD.

//Enter API keys here
//DO NOT SHARE YOUR API KEYS WITH ANYONE!
$DRIVER="sql";
$OPENAI_API_KEY="";                   // OpenAI API key here
$AZURE_API_KEY="";                    // Azure API key here
$ELEVENLABS_API_KEY="";               // 11labs API key here
$GCP_SA_FILEPATH="tts/gcp_key.json";  // path to GCP SA key file here


// Player and Personality configuration. 
// If you are using a large personality description you will need to increase the API_MAX_TOKENS amount
// DON'T USE "" (quotation marks) within the HERIKA_PERS variable otherwise it will break the conf.php file!
$DEBUG_MODE=false;
$PLAYER_NAME="Prisoner";
$HERIKA_NAME="Herika";  //Work in progress configuration for changing Herika's character. Just ignore this and leave as is.
$HERIKA_PERS="You are Herika, a Breton female who likes jokes and sarcastic comments.";
$PROMPT_HEAD="Let\'s roleplay in the Universe of Skyrim. I\'m {$GLOBALS["PLAYER_NAME"]}. You don't describe things or actions, just chat as your character";

// Size of context data to send. More context, more tokens, and not so fish-memory.
$CONTEXT_HISTORY="25";

// Azure TTS Configuration
$AZURETTS_CONF["fixedMood"]="";			// Azure TTS prosody and style. Empty to stay variable.
$AZURETTS_CONF["region"]="westeurope";			// Region, Fine tune to improve response time. https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/regions
$AZURETTS_CONF["voice"]="en-US-NancyNeural";	// Voice. Read https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/language-support?tabs=tts
$AZURETTS_CONF["volume"]="25";					// Default volume.
$AZURETTS_CONF["rate"]="1.2";					// Default rate (speed).
$AZURETTS_CONF["countour"]= "(11%, +15%) (60%, -23%) (80%, -34%)";		//Algorithm to change pitch during speech. 
$AZURETTS_CONF["validMoods"]=array("whispering","default");	// New, limits moods allowed in TTS transcription. Not all voices support all moods.

// Read this and scroll down to "Prosody" for more info on these configurations: https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/speech-synthesis-markup-voice		

//  MIMIC3 configuration, navigate to the URL below to access the web interface.
$MIMIC3="http://127.0.0.1:59125";               
$MIMIC3_CONF["voice"]="en_US/ljspeech_low";
$MIMIC3_CONF["rate"]="1.25";
$MIMIC3_CONF["volume"]="80";

// To use a custom voice which you have created with Elevenlabs go here: https://api.elevenlabs.io/docs#/voices/Get_voices_v1_voices_get
// Click the "Try it out" button and in the "x-api-key" textbox enter in your API key. 
// Click Execute. You should see just below a list of all the available voices you can use, including any custom ones.
// Just copy and paste that "voice_id" into the "voice_id" variable below.
$ELEVEN_LABS["voice_id"]="EXAVITQu4vr4xnSDxMaL";	// https://api.elevenlabs.io/v1/voices for voice list.
$ELEVEN_LABS["optimize_streaming_latency"]="0";		// https://docs.elevenlabs.io/api-reference/text-to-speech for API parameters.
$ELEVEN_LABS["model_id"]="eleven_monolingual_v1";	// Check https://beta.elevenlabs.io/speech-synthesis for voice parameters.
$ELEVEN_LABS["stability"]="0.75";			// Check https://beta.elevenlabs.io/speech-synthesis for voice parameters.
$ELEVEN_LABS["similarity_boost"]="0.75";		// Check https://beta.elevenlabs.io/speech-synthesis for voice parameters.

// Google Cloud Platform TTS Config.
// Catalogue of voices: https://cloud.google.com/text-to-speech?hl=en#section-11 & https://cloud.google.com/text-to-speech/docs/voices
// Creating a service account key for GCP: https://github.com/abeiro/saig-gwserver/pull/2 . Or ask ChatGPT.
// SSML Configuration is discarded if voice is "en-US-Studio-O" or en-US-Studio-M.
$GCP_CONF = [
  'voice' => [
    'name' => 'en-GB-Neural2-C',
    'languageCode' => 'en-GB'
  ],
  'ssml' => [
    'rate' => '1.15',
    'pitch' => '+3.6st'
  ],
  'volume' => 1.0
];


$LOCALWHISPER["URL"]="";    // Used for local whisper installations.

//Allows you to toggle which providers you use for Text-to-Speech or Speech-to-Text.
//IF YOU DO NOT HEAR HERIKA MAKE SURE TO CHECK YOUR SYSTEM SOUNDS VOLUME.
$STTFUNCTION="whisper";								// Valid options are azure or whisper or localwhisper.
$TTSFUNCTION="mimic3";								// Valid options are azure or mimic3 or 11labs or gcp.

//Configuration for changing default language for TTS
$TTSLANGUAGE_AZURE="en-US";							// en-US, es-ES formats
$TTSLANGUAGE_WHISPER="en";							// en, es formats

//Large Value = Longer responses from Herika (she can speak in Paragraphs). However this will increase API usage cost!
//Setting it to 100 is a good starting point for experimenting with larger responses.
$OPENAI_MAX_TOKENS="48";							// Limit size of responses. 

$HTTP_TIMEOUT=30;                       // How long we will wait for Openai response.
$OPENAI_MAX_TOKENS_MEMORY="1024";       // Required to create memories.


// NEW CONF VARS FOR 0.99

//$CORE_LANG="es";                            // Control global language. Leave commented for default language (English).

$MEMORY_EMBEDDING=false;                     // Memory feature (needs OpenAI).
$CHROMADB_URL='http://172.16.1.128:8000';   // CHROMADB REST API URL. Change to the one provided by DwemerDistro.

// MODEL="openai";
$GPTMODEL="gpt-3.5-turbo-0613";                           // Changes GPT model to use. Options are gpt-4 or gpt-3.5-turbo-0613, more can be found here https://platform.openai.com/account/rate-limits
$OPENAI_URL="https://api.openai.com/v1/chat/completions"; // OpenAI endpoint.



// MODEL="koboldcpp";                       
$KOBOLDCPP_URL="http://172.16.1.128:5001";  // Endpoint URL. Change with your custom endpoint.

$MODELS=["openai","koboldcpp"];             // Models available;


$COST_MONITOR_ENABLED=false;                // Elbios token counter and cost calculator. Needs background daemon.
?>
