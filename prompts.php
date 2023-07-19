<?php

$PROMPTS=array(
    "location"=>[
            "(Chat as $HERIKA_NAME)", // give way to
            "{$finalParsedData[3]} What do you know about this place?"  //prompt
        ],
    "book"=>[
        "(Complete the text replacing the hashtag #CHAT#) (note that despite her poor memory, $HERIKA_NAME is capable of remembering entire books) $HERIKA_NAME: is about #CHAT#",
        "{$GLOBALS["PLAYER_NAME"]}: $HERIKA_NAME, summarize this book shortly: "
    ],
    "combatend"=>[
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she comments about the last combat encounter)  $HERIKA_NAME: #CHAT#",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she laughs at {$GLOBALS["PLAYER_NAME"]}'s combat style) $HERIKA_NAME: #CHAT#",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she comments about  {$GLOBALS["PLAYER_NAME"]} weapons)  $HERIKA_NAME: #CHAT#",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she admires  {$GLOBALS["PLAYER_NAME"]}'s combat style)  $HERIKA_NAME: #CHAT#",
        "(Roleplay only as $HERIKA_NAME, she make a short poem about last kill)  $HERIKA_NAME:",
        "extra"=>["mood"=>"whispering","force_tokens_max"=>"50","dontuse"=>(time()%5==0)]   //20% chance

    ],
    "quest"=>[
        "(Chat as $HERIKA_NAME)",
        "$HERIKA_NAME, what should we do about this quest '{$questName}'?"
    ],

    "bleedout"=>[
        "(Complete the text replacing the hashtag #CHAT#)(Chat as $HERIKA_NAME, complain about almost being defeated)  $HERIKA_NAME: #CHAT#",
        ""
    ],

    "bored"=>[
        "",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment a joke about current location) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment about the current weather) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment about the time and date) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment about the last event) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment about a Skyrim Meme) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment about any of the Gods in Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment about the politics of Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment about a historical event from the Elder Scrolls Universe) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment about a book from the Elder Scrolls Universe) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment starting with: I once had to) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment starting with: Did you hear about what happened in) $HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment starting with: A wise Akaviri man once told me) $$HERIKA_NAME: #CHAT# ",
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME, she makes a casual comment about current relationship/friendship status with {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT# "
    ],

    "goodmorning"=>[
        "(Complete the text replacing the hashtag #CHAT#)(Roleplay only as $HERIKA_NAME commenting about {$GLOBALS["PLAYER_NAME"]}'s nap )  $HERIKA_NAME: #CHAT#",
        "(waking up after sleep). ahhhh  "
    ],

    "inputtext"=>[
        "(Complete the text replacing the hashtag #CHAT#, call functions if needed, put voice tone in parenthesys,valid voice tones:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Roleplay only as $HERIKA_NAME) $HERIKA_NAME:  #CHAT#" // Prompt is implicit

    ],
    "inputtext_s"=>[
        "(Complete the text replacing the hashtag #CHAT#,call functions if needed, Roleplay only as $HERIKA_NAME) $HERIKA_NAME:  #CHAT#",
        "extra"=>["mood"=>"whispering"]
    ],
    "afterfunc"=>[
        "(Complete the text replacing the hashtag #CHAT#, $HERIKA_NAME talks again to {$GLOBALS["PLAYER_NAME"]}, you only generate text as $HERIKA_NAME) $HERIKA_NAME: Well... #CHAT#",
        "extra"=>[]
    ],
    "lockpicked"=>[
        "(Complete the text replacing the hashtag #CHAT#, comment about item lockpicked) $HERIKA_NAME: #CHAT#",
        "({$GLOBALS["PLAYER_NAME"]} has unlocked {$finalParsedData[3]})"
    ],
     "afterattack"=>[
        "(Complete the text replacing the hashtag #CHAT#, just write a short intro catchphrase for combat) $HERIKA_NAME: #CHAT#"
    ],
    // Like inputtext, but without the functions calls part. It's likely to be used in papyrus scripts
    "chatnf"=>[ 
         "(Complete the text replacing the hashtag #CHAT#, put voice tone in parenthesys,valid voice tones:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Roleplay only as $HERIKA_NAME) $HERIKA_NAME:  #CHAT#" // Prompt is implicit
        
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
