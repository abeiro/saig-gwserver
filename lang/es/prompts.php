<?php

$PROMPTS=array(
    "location"=>[
            "(Chat as $HERIKA_NAME)", // give way to
            "{$finalParsedData[3]} What do you know about this place?"  //prompt
        ],
    "book"=>[
        " (ten en cuenta que a pesar de su mala memoria, $HERIKA_NAME es capaz de recordar libros enteros)",
        "{$GLOBALS["PLAYER_NAME"]}: $HERIKA_NAME, Resume brevemente este libro: "
    ],
    "combatend"=>[
        " (interpreta a $HERIKA_NAME, ella comenta sobre el último combate) ",
        " (interpreta a $HERIKA_NAME, ella se rie de estilo de combate de {$GLOBALS["PLAYER_NAME"]}) ",
        " (interpreta a $HERIKA_NAME, ella comenta las armas de {$GLOBALS["PLAYER_NAME"]})  ",
        " (interpreta a $HERIKA_NAME, ella admira el estilo de combate de {$GLOBALS["PLAYER_NAME"]})",
        " (interpreta a $HERIKA_NAME, hace un breve poema sobre el ultimo combate $HERIKA_NAME)",
        "extra"=>["mood"=>"whispering","force_tokens_max"=>"50","dontuse"=>(time()%5==0)]   //20% chance

    ],
    "quest"=>[
        "(Chat as $HERIKA_NAME)",
        "$HERIKA_NAME, what should we do about this quest '{$questName}'?"
    ],

    "bleedout"=>[
        " (interpreta a $HERIKA_NAME, ella se queja que casi es derrotada)",
        ""
    ],

    "bored"=>[
        "",
        " (interpreta a $HERIKA_NAME, ella hace un comentario a joke about current location)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario sobre el clima)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario sobre la hora y el dia)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario sobre los ultimos eventos)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario sobre un meme de Skyrim)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario sobre los dioses de Skyrim)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario sobre la politica de Skyrim)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario sobre un evento historico del universo de Elder Scrolls)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario sobre un libro del universo de Elder Scrolls)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario empezando with: I once had to)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario empezando con: Te has enterado de )",
        " (interpreta a $HERIKA_NAME, ella hace un comentario empezando con: Un sabio me dijo una vez)",
        " (interpreta a $HERIKA_NAME, ella hace un comentario acerca de su relacion de amistad con {$GLOBALS["PLAYER_NAME"]})"
    ],

    "goodmorning"=>[
        " ($HERIKA_NAME cuenta un chiste o anecdota sobre la siesta de {$GLOBALS["PLAYER_NAME"]})",
        "(bostezando despues de despertar). ahhhh  "
    ],

    "inputtext"=>[
        "(Pon el tono de voz entre parentesis,tonos validos:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ". Completa el dialogo como $HERIKA_NAME )" // Prompt is implicit

    ],
    "inputtext_s"=>[
        "(Completa el dialogo como $HERIKA_NAME.)", // Prompt is implicit
        "extra"=>["mood"=>"whispering"]
    ],
    "afterfunc"=>[
        "($HERIKA_NAME responde a {$GLOBALS["PLAYER_NAME"]}, pon el tono de voz entre parentesis,tonos validos:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . " ,solo completa el texto de $HERIKA_NAME)",
        "extra"=>[]
    ],
    "lockpicked"=>[
        "($HERIKA_NAME comenta el objetivo que se ha forzado) ",
        "The Narrator: ({$GLOBALS["PLAYER_NAME"]} ha forzado la cerradura de {$finalParsedData[3]})"
    ],
     "afterattack"=>[
        "($HERIKA_NAME grita una frase corta motivadora para el combate)"
    ],
    // Like inputtext, but without the functions calls part. It's likely to be used in papyrus scripts
    "chatnf"=>[ 
         "(pon el tono de voz entre parentesis,tonos validos:" . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ")  (Solo interpreta a $HERIKA_NAME)" // Prompt is implicit
        
    ],
    "diary"=>[ 
        "(Usa la function WriteIntoDiary para escribir en el diario)" ,
        "Por favor, escribe en tu diario un resumen de los ultimos dialogos y eventos de {$GLOBALS["PLAYER_NAME"]} y $HERIKA_NAME en tercera persona, se creativa y ponle un titulo adecuado"      
    ],

);


   
?>
