<?php

$PROMPTS=array(
    "location"=>[
            "(Chat as $HERIKA_NAME)", // give way to
            "{$finalParsedData[3]} What do you know about this place?"  //prompt
        ],
    "book"=>[
        "$HERIKA_NAME: {$finalParsedData[3]}  is about ",
        "$HERIKA_NAME, summarize the book '{$finalParsedData[3]}' shortly"
    ],
    "combatend"=>[
        "(Chat as $HERIKA_NAME, comment about the last combat encounter)"
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
        "($HERIKA_NAME make a casual comment about her background story or a joke about current location) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment about the current weather) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment about the time and date) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment about the last event) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment about a Skyrim Meme) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment about any of the Gods in Skyrim) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment about the politics of Skyrim) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment about a historical event from the Elder Scrolls Universe) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment about a book from the Elder Scrolls Universe) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment starting with: I once had to) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment starting with: Did you hear about what happened in) $HERIKA_NAME: ... ",
        "($HERIKA_NAME make a casual comment starting with: A wise Akaviri man once told me) $$HERIKA_NAME_NAME: ... "
    ],

    "goodmorning"=>[
        "(Chat as $HERIKA_NAME)",
        "(waking up after sleep). ahhhh  "
    ],

    "inputtext"=>[
        "(put mood in parenthesys,valid moods:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") $HERIKA_NAME:" // Prompt is implicit

    ],
    "inputtext_s"=>[
        "(whispering) $HERIKA_NAME: "
    ],

    "lockpicked"=>[
        "(Comment about item lockpicked) $HERIKA_NAME: ",
        "({$GLOBALS[$PLAYER_NAME]} has unlocked {$finalParsedData[3]})"
    ],
     "afterattack"=>[
        "(Just write a short intro catchphrase for combat) $HERIKA_NAME: "
    ],
    // Like inputtext, but without the functions calls part. It's likely to be used in papyrus scripts
    "chatnf"=>[ 
        "(put mood in parenthesys,valid moods:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") $HERIKA_NAME:" // Prompt is implicit
    ],

);


// You can override prompts here
if (file_exists(__DIR__.DIRECTORY_SEPARATOR."prompts_custom.php"))
    require_once(__DIR__.DIRECTORY_SEPARATOR."prompts_custom.php");
?>
