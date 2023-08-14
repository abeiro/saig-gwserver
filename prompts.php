<?php

$PROMPTS=array(
    "location"=>[
            "(Chat as $HERIKA_NAME)", // give way to
            "{$finalParsedData[3]} What do you know about this place?"  //prompt
        ],
    "book"=>[
        "(Note that despite her poor memory, $HERIKA_NAME is capable of remembering entire books)",
        "{$GLOBALS["PLAYER_NAME"]}: $HERIKA_NAME, summarize this book shortly: "
    ],
    "combatend"=>[
        "(roleplay as $HERIKA_NAME, she comments about the last combat encounter",
        "(roleplay as $HERIKA_NAME, she laughs at {$GLOBALS["PLAYER_NAME"]}'s combat style)",
        "(roleplay as $HERIKA_NAME, she comments about  {$GLOBALS["PLAYER_NAME"]} weapons",
        "(roleplay as $HERIKA_NAME, she admires  {$GLOBALS["PLAYER_NAME"]}'s combat style",
        "extra"=>["mood"=>"whispering","force_tokens_max"=>"50","dontuse"=>(time()%5!=0)]   //20% chance

    ],
    "quest"=>[
        "(Chat as $HERIKA_NAME)",
        "$HERIKA_NAME, what should we do about this quest '{$questName}'?"
    ],

    "bleedout"=>[
        "(roleplay as $HERIKA_NAME, complain about almost being defeated",
        ""
    ],

    "bored"=>[
        "",
        "(roleplay as $HERIKA_NAME, she makes a casual comment a joke about current location)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment about the current weather)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment about the time and date)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment about the last event)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment about a Skyrim Meme)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment about any of the Gods in Skyrim)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment about the politics of Skyrim)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment about a historical event from the Elder Scrolls Universe)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment about a book from the Elder Scrolls Universe)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment starting with: I once had to)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment starting with: Did you hear about what happened in)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment starting with: A wise Akaviri man once told me)",
        "(roleplay as $HERIKA_NAME, she makes a casual comment about current relationship/friendship status with {$GLOBALS["PLAYER_NAME"]})"
    ],

    "goodmorning"=>[
        "(roleplay as $HERIKA_NAME commenting about {$GLOBALS["PLAYER_NAME"]}'s nap ",
        "(waking up after sleep). ahhhh  "
    ],

    "inputtext"=>[
        "(specify action for $HERIKA_NAME or complete $HERIKA_NAME's dialogue using this format '$HERIKA_NAME: (optional mood from this list [" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . "]) ...'" // Prompt is implicit

    ],
    "inputtext_s"=>[
        "(specify action for $HERIKA_NAME or complete $HERIKA_NAME's dialogue using this format: '$HERIKA_NAME: ...'", // Prompt is implicit
        "extra"=>["mood"=>"whispering"]
    ],
    "afterfunc"=>[
        "$HERIKA_NAME talks to {$GLOBALS["PLAYER_NAME"]}. Complete $HERIKA_NAME's dialogue using this format '$HERIKA_NAME: (optional mood from this list [" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . "]) ...'",
        "extra"=>[],
        "TakeASeat"=>"(roleplay as $HERIKA_NAME, she talks about sitting location)",
        "GetDateTime"=>"(roleplay as $HERIKA_NAME) she answers with the current date and time in short sentence)",
        "MoveTo"=>"(roleplay as $HERIKA_NAME) ,she makes a comment about movement destination)"       
    ],
    "lockpicked"=>[
        "($HERIKA_NAME comments about lockpicked item)",
        "({$GLOBALS["PLAYER_NAME"]} has unlocked {$finalParsedData[3]})"
    ],
     "afterattack"=>[
        "(roleplay as $HERIKA_NAME , she shouts a catchphrase for combat)"
    ],
    // Like inputtext, but without the functions calls part. It's likely to be used in papyrus scripts
    "chatnf"=>[ 
        "(Complete $HERIKA_NAME's dialogue using this format '$HERIKA_NAME: (optional mood from this list [" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . "]) ...'" // Prompt is implicit
        
    ],
    "diary"=>[ 
        "(Use function to Write into diary) $HERIKA_NAME:" ,
        "Please, write in your diary a summary of {$GLOBALS["PLAYER_NAME"]} and $HERIKA_NAME's last dialogues and events. Be creative and create the topic by yourself."      
    ],

);




if (isset($GLOBALS["CORE_LANG"]))
	if (file_exists(__DIR__.DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$GLOBALS["CORE_LANG"].DIRECTORY_SEPARATOR."prompts.php")) 
		require_once(__DIR__.DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$GLOBALS["CORE_LANG"].DIRECTORY_SEPARATOR."prompts.php");
  
// You can override prompts here
if (file_exists(__DIR__.DIRECTORY_SEPARATOR."prompts_custom.php"))
    require_once(__DIR__.DIRECTORY_SEPARATOR."prompts_custom.php");
?>
