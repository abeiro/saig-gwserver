<?php

// READ THE COMMENTS TO LEARN WHAT EACH VARIABLE DOES
// DO NOT COMMENT OUT ANY VARIABLES, YOU MAY BREAK THE MOD.

// Enter API keys here
// DO NOT SHARE YOUR API KEYS WITH ANYONE!
$DRIVER="sql";
$OPENAI_API_KEY="";                   // OpenAI API key here
$AZURE_API_KEY="";                    // Azure API key here
$ELEVENLABS_API_KEY="";               // 11labs API key here
$GCP_SA_FILEPATH="tts/gcp_key.json";  // path to GCP SA key file here


// Personality Configuration
// If you are using a large personality description you will need to increase the API_MAX_TOKENS amount
// DON'T USE "" (quotation marks) within the HERIKA_PERS variable otherwise it will break the conf.php file!
$DEBUG_MODE=false;
$PLAYER_NAME="Prisoner";
$HERIKA_NAME="Herika";  //Work in progress configuration for changing Herika's character. Just ignore this and leave as is.
$HERIKA_PERS="You are Herika, a Breton female who likes jokes and sarcastic comments.";
$PROMPT_HEAD="Let\'s roleplay in the Universe of Skyrim. I\'m {$GLOBALS["PLAYER_NAME"]}. You don't describe things or actions, just chat as your character. Only perform actions and functions if your character would find it necessary or must have to, even if it contradicts {$GLOBALS["PLAYER_NAME"]}'s requests.";

// Short term memory size. How many of the most recent events which will be sent in each prompt.
$CONTEXT_HISTORY="25";

// Azure TTS Configuration
// Read this and scroll down to "Prosody" for more info on these configurations: https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/speech-synthesis-markup-voice		
$AZURETTS_CONF["fixedMood"]="";			// Azure TTS prosody and style. Empty to stay variable.
$AZURETTS_CONF["region"]="westeurope";			// Region, Fine tune to improve response time. https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/regions
$AZURETTS_CONF["voice"]="en-US-NancyNeural";	// Voice. Read https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/language-support?tabs=tts
$AZURETTS_CONF["volume"]="25";					// Default volume
$AZURETTS_CONF["rate"]="1.2";					// Default rate (speed)
$AZURETTS_CONF["countour"]= "(11%, +15%) (60%, -23%) (80%, -34%)";		//Algorithm to change pitch during speech. 
$AZURETTS_CONF["validMoods"]=array("whispering","default");	// Limits moods allowed in TTS transcription. Not all voices support all moods.


// MIMIC3 configuration
// If using DwemerDistro check the console for what URL to use.
$MIMIC3="http://127.0.0.1:59125";               
$MIMIC3_CONF["voice"]="en_US/ljspeech_low";
$MIMIC3_CONF["rate"]="1.25";
$MIMIC3_CONF["volume"]="80";

// To use a custom voice which you have created with Elevenlabs go here: https://api.elevenlabs.io/docs#/voices/Get_voices_v1_voices_get
// Click the "Try it out" button and in the "x-api-key" textbox enter in your API key. 
// Click Execute. You should see just below a list of all the available voices you can use, including any custom ones.
// Just copy and paste that "voice_id" into the "voice_id" variable below.
$ELEVEN_LABS["voice_id"]="EXAVITQu4vr4xnSDxMaL";	// https://api.elevenlabs.io/v1/voices for voice list
$ELEVEN_LABS["optimize_streaming_latency"]="0";		// https://docs.elevenlabs.io/api-reference/text-to-speech for API parameters
$ELEVEN_LABS["model_id"]="eleven_monolingual_v1";	// Check https://beta.elevenlabs.io/speech-synthesis for voice parameters
$ELEVEN_LABS["stability"]="0.75";			// Check https://beta.elevenlabs.io/speech-synthesis for voice parameters
$ELEVEN_LABS["similarity_boost"]="0.75";		// Check https://beta.elevenlabs.io/speech-synthesis for voice parameters

// Google Cloud Platform TTS Config
// Catalogue of voices: https://cloud.google.com/text-to-speech?hl=en#section-11 & https://cloud.google.com/text-to-speech/docs/voices
// Creating a service account key for GCP: https://github.com/abeiro/saig-gwserver/pull/2 
// SSML Configuration is discarded if voice is "en-US-Studio-O" or en-US-Studio-M
$GCP_CONF = [
  'voice' => [
    'name' => 'en-GB-Neural2-C',
    'languageCode' => 'en-GB'
  ],
  'ssml' => [
    'rate' => '1.15',
    'pitch' => '+3.6st'
  ]
];


$LOCALWHISPER["URL"]="";    // Used for LocalWhisper installations

//Allows you to toggle which providers you use for Text-to-Speech or Speech-to-Text
//IF YOU DO NOT HEAR HERIKA MAKE SURE TO CHECK YOUR SYSTEM SOUNDS VOLUME!
$STTFUNCTION="whisper";								// Valid options are azure or whisper or localwhisper
$TTSFUNCTION="mimic3";								// Valid options are azure or mimic3 or 11labs or gcp 

//Configuration for changing default language for TTS
$TTSLANGUAGE_AZURE="en-US";							// en-US, es-ES formats
$TTSLANGUAGE_WHISPER="en";							// en, es formats

//Large value = longer responses from Herika. However this will increase API usage cost!
//100 is a good starting point for experimenting with larger responses.
$OPENAI_MAX_TOKENS="48";							// Limit size of responses. 

$OPENAI_MAX_TOKENS_MEMORY="1024";             // Length of Diary entries. More = longer entry but higher cost.
$HTTP_TIMEOUT=30;                             // How long we will wait for LLM response


// NEW CONF VARS FOR 0.99

//$CORE_LANG="es";                            // Control global lang. Leave commented for default language.

$MEMORY_EMBEDDING=false;                      // Memory feature (needs OpenAI atm)
$CHROMADB_URL='http://172.16.1.128:8000';     // CHROMADB REST API URL. Change to the one provided by DwemerDistro.
$MEMORY_TIME_DELAY='10';                      // How many minutes to wait before allowing a memory to be pulled. Prevents short term memory overlapping($CONTEXT_HISTORY).
$MEMORY_CONTEXT_SIZE='1';                     // How many longterm memories will be injected into the prompt. Higher amount means a more acurate response but higher token count/cost. 

// MODEL="openai";
$GPTMODEL="gpt-3.5-turbo-0613";                           // Changes GPT model to use. Options are gpt-4 or gpt-3.5-turbo-0613, more can be found here https://platform.openai.com/account/rate-limits
$OPENAI_URL="https://api.openai.com/v1/chat/completions"; // OpenAI endpoint

// MODEL="koboldcpp";                         // Koboldcpp model
$KOBOLDCPP_URL="http://172.16.1.128:5001";  // Endpoint URL. Change with your custom endpoint.

$KOBOLDCPP_MAX_TOKENS="80";                 // Limit size of responses. 
$KOBOLDCPP_MAX_TOKENS_MEMORY="256";         // Length of Diary entries. Do not make it any smaller then default. More = longer entry but higher time. 
                                            // Note that this is the length of the response. 


$MODELS=["openai","koboldcpp"];             // Models available;


$COST_MONITOR_ENABLED=false;                // Elbios token counter and cost calculator. Requires a background service running which may slowdown the DwemerDistro.
?>
