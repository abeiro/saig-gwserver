<?php

$PROMPTS=array(
    "location"=>[
            "(Chat as Herika)", // give way to
            "{$finalParsedData[3]} What do you know about this place?"  //prompt
        ],
    "book"=>[
        "Herika: It's about ",
        "Herika, summarize the book '{$finalParsedData[3]}' shortly"
    ],
    "combatend"=>[
        "(Chat as Herika, comment about last combat)"
    ],
    "quest"=>[
        "(Chat as Herika)",
        "Herika, what do should we do about this quest '{$questName}'?"
    ],

    "bleedout"=>[
        "(Chat as Herika, complain about almost being defeated)",
        ""
    ],

    "bored"=>[
        "(Herika makes joke about current localtion) Herika:",
        ""
    ],

    "goodmorning"=>[
        "(Chat as Herika)",
        "(waking up after sleep). ahhhh  "
    ],

    "inputtext"=>[
        "(put mood in parenthesys,valid moods:" . implode(",", $GLOBALS["AZURETTS_CONF"]["validMoods"]) . ") Herika:" // Prompt is implicit

    ],
    "inputtext_s"=>[
        "(whispering) Herika: "
    ],

    "lockpicked"=>[
        "(Comment about item lockpicked) Herika: ",
        "({$GLOBALS[$PLAYER_NAME]} has unlocked {$finalParsedData[3]})"
    ]

);


// You can override prompts here
require_once(__DIR__.DIRECTORY_SEPARATOR."prompts_custom.php");
?>