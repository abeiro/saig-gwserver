<?php

$PROMPTS=array(
    "location"=>[
            "(Rozmawiaj jako $HERIKA_NAME)", // give way to
            "{$finalParsedData[3]} Co wiesz o tym miejscu?"  //prompt

    ],
    "book"=>[
        "(Uzupełnij tekst zastępując hashtag #CHAT#) (zauważ, że pomimo słabej pamięci, $HERIKA_NAME jest w stanie zapamiętać całe książki) $HERIKA_NAME: jest o #CHAT#",
        "{$GLOBALS["PLAYER_NAME"]}: $HERIKA_NAME, krótko podsumuj tę książkę: "

    ],
    "combatend"=>[
        "(Uzupełnij tekst zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona komentuje ostatnie starcie)  $HERIKA_NAME: #CHAT#",
        "(Uzupełnij tekst zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona śmieje się ze stylu walki {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT#",
        "(Uzupełnij tekst zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona komentuje broń {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "(Uzupełnij tekst zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona podziwia styl walki {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "extra"=>["mood"=>"whispering","force_tokens_max"=>"50","dontuse"=>(time()%5!=0)]   //20% chance
    ],
    "quest"=>[
        "(Rozmawiaj jako $HERIKA_NAME)",
        "$HERIKA_NAME, co powinniśmy zrobić w związku z tą misją '{$questName}'?"
    ],

    "bleedout"=>[
        "(Uzupełnij tekst zastępując hashtag #CHAT#)(Rozmawiaj jako $HERIKA_NAME, narzekaj na to, że prawie zostałaś pokonana)  $HERIKA_NAME: #CHAT#",
        ""
    ],

    "bored"=>[
        "",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz, żart na temat obecnej lokalizacji) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz na temat obecnej pogody) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz na temat czasu i daty) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz na temat ostatniego wydarzenia) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz na temat mema ze Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz na temat któregoś z Bogów w Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz na temat polityki Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz na temat historycznego wydarzenia z uniwersum Elder Scrolls) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz na temat książki z uniwersum Elder Scrolls) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz zaczynając od: Kiedyś musiałam) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz zaczynając od: Słyszałeś co się wydarzyło w) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz zaczynając od: Pewien mądry człowiek z Akaviru kiedyś mi powiedział) $HERIKA_NAME: #CHAT# ",
        "(Uzupełnij tekst, zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME, ona robi swobodny komentarz na temat obecnego statusu związku/przyjaźni z {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT# "
    ],

    "goodmorning"=>[
        "(Uzupełnij tekst zastępując hashtag #CHAT#)(Graj tylko jako $HERIKA_NAME komentując drzemkę {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "(budząc się po spaniu). ahhhh  "
    ],

    "inputtext"=>[
        "(Uzupełnij tekst zastępując hashtag #CHAT#, wywołaj funkcje w razie potrzeby, umieść ton głosu w nawiasach (), dostępne tony (nie tłumacz ich):" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Graj tylko jako $HERIKA_NAME) $HERIKA_NAME:  #CHAT#" // Prompt is implicit
    ],
    "inputtext_s"=>[
        "(Uzupełnij tekst zastępując hashtag #CHAT#, wywołaj funkcje w razie potrzeby, graj tylko jako $HERIKA_NAME) $HERIKA_NAME:  #CHAT#",
        "extra"=>["mood"=>"whispering"]
    ],
    "afterfunc"=>[
        "(Uzupełnij tekst zastępując hashtag #CHAT#, $HERIKA_NAME znowu rozmawia z {$GLOBALS["PLAYER_NAME"]}, generujesz tylko tekst jako $HERIKA_NAME) $HERIKA_NAME: Cóż... #CHAT#",
        "extra"=>[],
    ],
    "lockpicked"=>[
        "(Uzupełnij tekst zastępując hashtag #CHAT#, skomentuj otwarte przedmioty) $HERIKA_NAME: #CHAT#",
        "({$GLOBALS["PLAYER_NAME"]} otworzył {$finalParsedData[3]})"
    ],
     "afterattack"=>[
        "(Uzupełnij tekst zastępując hashtag #CHAT#, wygłoś krótką kwestię po walce) $HERIKA_NAME: #CHAT#"
    ],

    // Like inputtext, but without the functions calls part. It's likely to be used in papyrus scripts
    "chatnf"=>[
         "(Uzupełnij tekst zastępując hashtag #CHAT#, umieść ton głosu w nawiasach (), dostępne tony (nie tłumacz ich):" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Graj tylko jako $HERIKA_NAME) $HERIKA_NAME:  #CHAT#" // Prompt is implicit
    ],
    "diary"=>[
        "(Użyj funkcji do zapisu do dziennika) $HERIKA_NAME:" ,
        "Proszę, napisz w swoim dzienniku podsumowanie ostatnich dialogów i wydarzeń {$GLOBALS["PLAYER_NAME"]} i $HERIKA_NAME. Bądź kreatywny i sam stwórz temat."
    ],

);


   
?>
