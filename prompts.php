<?php

$PROMPTS=array(
    "location"=>[
            "(Chat as Herika)", // give way to
            "{$finalParsedData[3]} What do you know about this place?"  //prompt
        ],
    "book"=>[
        "Herika: {$finalParsedData[3]}  is about ",
        "Herika, summarize the book '{$finalParsedData[3]}' shortly"
    ],
    "combatend"=>[
        "(Chat as Herika, comment about the last combat encounter)"
    ],
    "quest"=>[
        "(Chat as Herika)",
        "Herika, what should we do about this quest '{$questName}'?"
    ],

    "bleedout"=>[
        "(Chat as Herika, complain about almost being defeated)",
        ""
    ],

    "bored"=>[
        "(Herika make a casual comment about her background story or a joke about current location) Herika: ... ",
        ""
    ],

    "goodmorning"=>[
        "(Chat as Herika)",
        "(waking up after sleep). ahhhh  "
    ],

    "inputtext"=>[
        "(put mood in parenthesys,valid moods:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") Herika:" // Prompt is implicit

    ],
    "inputtext_s"=>[
        "(whispering) Herika: "
    ],

    "lockpicked"=>[
        "(Comment about item lockpicked) Herika: ",
        "({$GLOBALS[$PLAYER_NAME]} has unlocked {$finalParsedData[3]})"
    ],
     "afterattack"=>[
        "(Just write a short intro catchphrase for combat) Herika: "
    ]

);


// You can override prompts here
if (file_exists(__DIR__.DIRECTORY_SEPARATOR."prompts_custom.php"))
    require_once(__DIR__.DIRECTORY_SEPARATOR."prompts_custom.php");
?>
