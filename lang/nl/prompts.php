<?php

$PROMPTS=array(
    "location"=>[
            "(Chat als $HERIKA_NAME)", // geef weg aan
            "{$finalParsedData[3]} Wat weet jij over deze plaats?"  //aanwijzing
        ],
    "book"=>[
        "(Vervolledig de tekst door hashtag #CHAT# te vervangen) (Ondanks haar slechte geheugen, is $HERIKA_NAME in staat om hele boeken te onthouden) $HERIKA_NAME: gaat over #CHAT#",
        "{$GLOBALS["PLAYER_NAME"]}: $HERIKA_NAME, vat dit boek kort samen: "
    ],
    "combatend"=>[
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze geeft commentaar op het laatste gevecht)  $HERIKA_NAME: #CHAT#",
		"(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze vertelt een korte anekdote over een gelijkaardig gevecht in haar verleden)  $HERIKA_NAME: #CHAT#",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze lacht om de gevechtsstijl van {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT#",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze geeft commentaar op de wapens van {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze bewondert de gevechtsstijl van {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
		"extra"=>["mood"=>"whispering","force_tokens_max"=>"50","dontuse"=>(time()%5!=0)]   //20% chance
    ],
    "quest"=>[
        "(Chat als $HERIKA_NAME)",
        "$HERIKA_NAME, wat moeten we doen met deze queeste '{$questName}'?"
    ],

    "bleedout"=>[
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Chat als $HERIKA_NAME, klagen over bijna verslagen worden)  $HERIKA_NAME: #CHAT#",
        ""
    ],

    "bored"=>[
        "",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een terloops commentaar op de huidige locatie) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een terloops commentaar over het weer) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking over de tijd en datum) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking over het laatste evenement) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking over een Skyrim meme) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking over een van de Goden in Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking over de politiek in Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking over een historische gebeurtenis uit het Elder Scrolls universum) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking over een boek uit het Elder Scrolls universum) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking die begint met: Ik moest eens) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking die begint met: Heb je gehoord over wat er gebeurde in) $HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking die begint met: Een wijze Akaviri man vertelde me eens) $$HERIKA_NAME: #CHAT# ",
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME, ze maakt een opmerking over de huidige relatie/vriendschapsstatus met {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT# "
    ],

    "goodmorning"=>[
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen)(Genereer chat afronding voor $HERIKA_NAME die commentaar geeft op de dutje van {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "(wakker worden na slaap). ahhhh  "
    ],

    "inputtext"=>[
        "(Vervolledig de tekst door hashtag #CHAT# te vervangen, roep functies op indien nodig, zet stemtoon tussen haakjes, geldige stemtonen:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Genereer chatvoltooiing voor $HERIKA_NAME) $HERIKA_NAME:  #CHAT#" // Aanwijzing is impliciet
    ],
    "inputtext_s"=>[
        "(Vervolledig de tekst door hashtag #CHAT# te vervangen, roep functies op indien nodig, Genereer chatvoltooiing voor $HERIKA_NAME) $HERIKA_NAME:  #CHAT#",
        "extra"=>["mood"=>"whispering"]
    ],
    "afterfunc"=>[
        "(Vervolledig de tekst door hashtag #CHAT# te vervangen, $HERIKA_NAME praat weer tegen {$GLOBALS["PLAYER_NAME"]}, je genereert alleen tekst als $HERIKA_NAME) $HERIKA_NAME: Nou... #CHAT#",
        "extra"=>[],
        "TakeASeat"=>"(Genereer chatvoltooiing voor $HERIKA_NAME) $HERIKA_NAME: (praat over zitplaats #CHAT#)",
        "GetDateTime"=>"(Genereer chatvoltooiing voor $HERIKA_NAME) $HERIKA_NAME: (antwoordt met de huidige datum en tijd in een korte zin)",
        "MoveTo"=>"(Genereer chatvoltooiing voor $HERIKA_NAME) $HERIKA_NAME: (ze maakt een opmerking over de bestemming van de beweging)"       
    ],
    "lockpicked"=>[
        "(Vervolledig de tekst door hashtag #CHAT# te vervangen, opmerking over item lockpicked) $HERIKA_NAME: #CHAT#",
        "({$GLOBALS["PLAYER_NAME"]} heeft {$finalParsedData[3]} ontgrendeld)"
    ],
    "afterattack"=>[
        "(Voltooi de tekst door de hashtag #CHAT# te vervangen, schrijf gewoon een korte catchphrase voor na het gevecht) $HERIKA_NAME: #CHAT#"
    ],

    // Like inputtext, but without the functions calls part. It's likely to be used in papyrus scripts
    "chatnf"=>[ 
         "(Vervolledig de tekst door hashtag #CHAT# te vervangen, zet stemtoon tussen haakjes, geldige stemtonen:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Genereer chatvoltooiing voor $HERIKA_NAME) $HERIKA_NAME:  #CHAT#" // Aanwijzing is impliciet
    ],
    "diary"=>[ 
        "(Gebruik functie om in dagboek te schrijven) $HERIKA_NAME:" ,
        "Schrijf alsjeblieft in je dagboek een samenvatting van de laatste dialogen en gebeurtenissen van {$GLOBALS["PLAYER_NAME"]} en $HERIKA_NAME. Wees creatief en creëer het onderwerp zelf."      
    ],
);


   
?>
