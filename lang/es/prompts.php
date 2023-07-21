<?php

$PROMPTS=array(
    "location"=>[
            "(Chat as $HERIKA_NAME)", // give way to
            "{$finalParsedData[3]} What do you know about this place?"  //prompt
        ],
    "book"=>[
        "(Completa el texto reemplazando el hashtag #CHAT#) (ten en cuenta que a pesar de su mala memoria, $HERIKA_NAME es capaz de recordar libros enteros) $HERIKA_NAME: is about #CHAT#",
        "{$GLOBALS["PLAYER_NAME"]}: $HERIKA_NAME, Resume brevemente este libro: "
    ],
    "combatend"=>[
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella comenta sobre el último combate) $HERIKA_NAME: #CHAT#",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella se rie de estilo de combate de {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT#",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella comenta las armas de {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT#",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella admira el estilo de combate de {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, hace un breve poema sobre el ultimo combate $HERIKA_NAME) $HERIKA_NAME:",
        "extra"=>["mood"=>"whispering","force_tokens_max"=>"50","dontuse"=>(time()%5==0)]   //20% chance

    ],
    "quest"=>[
        "(Chat as $HERIKA_NAME)",
        "$HERIKA_NAME, what should we do about this quest '{$questName}'?"
    ],

    "bleedout"=>[
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella se queja que casi es derrotada) $HERIKA_NAME: #CHAT#",
        ""
    ],

    "bored"=>[
        "",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario a joke about current location) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario sobre el clima) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario sobre la hora y el dia) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario sobre los ultimos eventos) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario sobre un meme de Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario sobre los dioses de Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario sobre la politica de Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario sobre un evento historico del universo de Elder Scrolls) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario sobre un libro del universo de Elder Scrolls) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario empezando with: I once had to) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario empezando con: Te has enterado de ) $HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario empezando con: Un sabio me dijo una vez) $$HERIKA_NAME: #CHAT# ",
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella hace un comentario acerca de su relacion de amistad con {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT# "
    ],

    "goodmorning"=>[
        "(Completa el texto reemplazando el hashtag #CHAT#) (interpreta a $HERIKA_NAME, ella comenta sobre la siesta de {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "(bostezando despues de despertar). ahhhh  "
    ],

    "inputtext"=>[
        "(Completa el texto reemplazando el hashtag #CHAT#, llama a una funcion, pon el tono de voz entre parentesis,tonos validos:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Solo interpreta a $HERIKA_NAME) $HERIKA_NAME:  #CHAT#" // Prompt is implicit

    ],
    "inputtext_s"=>[
        "(Completa el texto reemplazando el hashtag #CHAT#, llama a una funcion, interpreta solo a $HERIKA_NAME) $HERIKA_NAME:  #CHAT#",
        "extra"=>["mood"=>"whispering"]
    ],
    "afterfunc"=>[
        "(Completa el texto reemplazando el hashtag #CHAT#, $HERIKA_NAME sigue hablado a {$GLOBALS["PLAYER_NAME"]}, solo completa el texto de $HERIKA_NAME) $HERIKA_NAME: bueno... #CHAT#",
        "extra"=>[]
    ],
    "lockpicked"=>[
        "(Completa el texto reemplazando el hashtag #CHAT#, $HERIKA_NAME comenta el objetvo que se ha forzado) $HERIKA_NAME: #CHAT#",
        "({$GLOBALS["PLAYER_NAME"]} ha forzado la cerradura de {$finalParsedData[3]})"
    ],
     "afterattack"=>[
        "(Completa el texto reemplazando el hashtag #CHAT#, $HERIKA_NAME suelta una frase corta motivadora para el combate) $HERIKA_NAME: #CHAT#"
    ],
    // Like inputtext, but without the functions calls part. It's likely to be used in papyrus scripts
    "chatnf"=>[ 
         "(Completa el texto reemplazando el hashtag #CHAT#,  pon el tono de voz entre parentesis,tonos validos:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ")  (Solo interpreta a $HERIKA_NAME) $HERIKA_NAME:  #CHAT#" // Prompt is implicit
        
    ],
    "diary"=>[ 
        "(Usa la function WriteIntoDiary para escribir en el diario) $HERIKA_NAME:" ,
        "Por favor, escribe en tu diario un resumen de los ultimos dialogos y eventos de {$GLOBALS["PLAYER_NAME"]} y $HERIKA_NAME, se creativa y ponle un titulo adecuado"      
    ],

);


   
?>
