<?php

$PROMPTS=array(
    "location"=>[
        "(Chat als $HERIKA_NAME)", // give way to
        "{$finalParsedData[3]} Was weißt du über diesen Ort?"  //prompt
    ],
    "book"=>[
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (beachte, dass $HERIKA_NAME trotz ihres schlechten Gedächtnisses ganze Bücher behalten kann) $HERIKA_NAME: handelt von #CHAT#",
        "{$GLOBALS["PLAYER_NAME"]}: $HERIKA_NAME, Fasse dieses Buch kurz zusammen: "
    ],
    "combatend"=>[
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie kommentiert den letzten Kampf) $HERIKA_NAME: #CHAT#",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie lacht über den Kampfstil von {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT#",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie kommentiert die Waffen von {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT#",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie bewundert den Kampfstil von {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie verfasst ein kurzes Gedicht über den letzten Kampf) $HERIKA_NAME:",
        "extra"=>["mood"=>"flüsternd","force_tokens_max"=>"50","dontuse"=>(time()%5==0)]   //20% chance

    ],
    "quest"=>[
        "(Chat als $HERIKA_NAME)",
        "$HERIKA_NAME, was sollen wir mit dieser Quest '{$questName}' tun?"
    ],

    "bleedout"=>[
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie beschwert sich, dass sie fast besiegt wurde) $HERIKA_NAME: #CHAT#",
        ""
    ],

    "bored"=>[
        "",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie macht einen Kommentar oder Witz über den aktuellen Ort) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie kommentiert das Wetter) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie macht einen Kommentar über die Uhrzeit und den Tag) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie macht einen Kommentar über die letzten Ereignisse) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie macht einen Kommentar über ein Meme aus Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie macht einen Kommentar über die Götter von Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie macht einen Kommentar über die Politik in Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie macht einen Kommentar über ein historisches Ereignis im Elder Scrolls-Universum) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie macht einen Kommentar über ein Buch aus dem Elder Scrolls-Universum) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie beginnt mit: Ich musste einmal) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie beginnt mit: Hast du gehört,) $HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie beginnt mit: Ein Weiser sagte mir einmal) $$HERIKA_NAME: #CHAT# ",
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie macht einen Kommentar über ihre Freundschaft mit {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT# "
    ],

    "goodmorning"=>[
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt) (stelle dir vor, du bist $HERIKA_NAME, und sie kommentiert den Mittagsschlaf von {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "(gähnt nach dem Aufwachen) ahhhh  "
    ],

    "inputtext"=>[
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt und rufe eine Funktion auf. Gib den Tonfall in Klammern an" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Interpretiere nur $HERIKA_NAME) $HERIKA_NAME: #CHAT#" // Prompt ist implizit

    ],
    "inputtext_s"=>[
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt und rufe eine Funktion auf. Interpretiere nur $HERIKA_NAME) $HERIKA_NAME:  #CHAT#",
        "extra"=>["mood"=>"whispering"]
    ],
    "afterfunc"=>[
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt. $HERIKA_NAME spricht weiterhin mit {$GLOBALS["PLAYER_NAME"]}. Vervollständige nur den Text von $HERIKA_NAME) $HERIKA_NAME: gut... #CHAT#",
        "extra"=>[]
    ],
    "lockpicked"=>[
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt. $HERIKA_NAME kommentiert das Objekt, das geöffnet wurde) $HERIKA_NAME: #CHAT#",
        "({$GLOBALS["PLAYER_NAME"]} hat das Schloss von {$finalParsedData[3]} geknackt)"
    ],
    "afterattack"=>[
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt. $HERIKA_NAME gibt einen kurzen motivierenden Satz für den Kampf ab) $HERIKA_NAME: #CHAT#"
    ],
// Wie "inputtext", aber ohne den Teil mit den Funktionsaufrufen. Es wird wahrscheinlich in Papyrus-Skripten verwendet
    "chatnf"=>[
        "(Vervollständige den Text, indem du den Hashtag #CHAT# ersetzt. Gib den Tonfall in Klammern an sowie gültige Töne: " . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ")  (Interpretiere nur $HERIKA_NAME) $HERIKA_NAME:  #CHAT#" // Prompt ist implizit

    ],
    "diary"=>[
        "(Verwende die Funktion WriteIntoDiary, um ins Tagebuch zu schreiben) $HERIKA_NAME:" ,
        "Bitte schreibe in dein Tagebuch eine Zusammenfassung der letzten Dialoge und Ereignisse von {$GLOBALS["PLAYER_NAME"]} und $HERIKA_NAME. Sei kreativ und gib ihm einen passenden Titel."
    ],

);


   
?>
