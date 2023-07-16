<?php

$PROMPTS=array(
    "location"=>[
            "(Chat as $HERIKA_NAME)", // give way to
            "{$finalParsedData[3]} What do you know about this place?"  //prompt
        ],
    "book"=>[
        "$HERIKA_NAME: is about ...",
        "{$GLOBALS["PLAYER_NAME"]}: $HERIKA_NAME, summarize this book shortly: "
    ],
    "combatend"=>[
        "(Roleplay only as $HERIKA_NAME, she comments about the last combat encounter)  $HERIKA_NAME:",
        "(Roleplay only as $HERIKA_NAME, she laughs at {$GLOBALS["PLAYER_NAME"]} combat style)  $HERIKA_NAME:",
        "(Roleplay only as $HERIKA_NAME, she comments about  {$GLOBALS["PLAYER_NAME"]} weapons)  $HERIKA_NAME:",
        "(Roleplay only as $HERIKA_NAME, she admires  {$GLOBALS["PLAYER_NAME"]} combat style)  $HERIKA_NAME:",
        "(Roleplay only as $HERIKA_NAME, she make a short poem about last kill)  $HERIKA_NAME:",
        "extra"=>["mood"=>"whispering","force_tokens_max"=>"50","dontuse"=>false]

    ],
    "quest"=>[
        "(Chat as $HERIKA_NAME)",
        "$HERIKA_NAME, what should we do about this quest '{$questName}'?"
    ],

    "bleedout"=>[
        "(Chat as $HERIKA_NAME, complain about almost being defeated)",
        ""
    ],

    "bored"=>[
        "",
        "(Roleplay only as $HERIKA_NAME, casual comment about her background story or a joke about current location) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment about the current weather) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment about the time and date) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment about the last event) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment about a Skyrim Meme) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment about any of the Gods in Skyrim) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment about the politics of Skyrim) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment about a historical event from the Elder Scrolls Universe) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment about a book from the Elder Scrolls Universe) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment starting with: I once had to) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment starting with: Did you hear about what happened in) $HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment starting with: A wise Akaviri man once told me) $$HERIKA_NAME: ... ",
        "(Roleplay only as $HERIKA_NAME, casual comment about current relationship status with {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: ... "
    ],

    "goodmorning"=>[
        "(Roleplay only as $HERIKA_NAME commenting about {$GLOBALS["PLAYER_NAME"]}'s nap ) ",
        "(waking up after sleep). ahhhh  "
    ],

    "inputtext"=>[
        "(call function if needed,put voice tone in parenthesys,valid voice tones:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Roleplay only as $HERIKA_NAME) $HERIKA_NAME:" // Prompt is implicit

    ],
    "inputtext_s"=>[
        "(call function if needed, Roleplay only as $HERIKA_NAME) $HERIKA_NAME: ",
        "extra"=>["mood"=>"whispering"]
    ],

    "lockpicked"=>[
        "(Comment about item lockpicked) $HERIKA_NAME: ",
        "({$GLOBALS["PLAYER_NAME"]} has unlocked {$finalParsedData[3]})"
    ],
     "afterattack"=>[
        "(Just write a short intro catchphrase for combat) $HERIKA_NAME: "
    ],
    // Like inputtext, but without the functions calls part. It's likely to be used in papyrus scripts
    "chatnf"=>[ 
        "(put mood in parenthesys,valid moods:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") $HERIKA_NAME:" // Prompt is implicit
    ],
    "diary"=>[ 
        "(Use function to Write into diary) $HERIKA_NAME:" ,
        "Please, write in your diary a summary of {$GLOBALS["PLAYER_NAME"]} and $HERIKA_NAME's last dialogues and events. Be creative and create the topic by yourself."      
    ],

);


// You can override prompts here
if (file_exists(__DIR__.DIRECTORY_SEPARATOR."prompts_custom.php"))
    require_once(__DIR__.DIRECTORY_SEPARATOR."prompts_custom.php");
?>
