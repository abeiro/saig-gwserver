<?php

$PROMPTS=array(
    "location"=>[
        "(Chat comme $HERIKA_NAME)", // give way to
        "{$finalParsedData[3]} Que sais-tu de cet endroit ?"  //prompt
    ],
    "book"=>[
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (malgré sa mauvaise mémoire, $HERIKA_NAME se souvient de livres entiers) $HERIKA_NAME: parle de #CHAT#",
        "{$GLOBALS["PLAYER_NAME"]}: $HERIKA_NAME, Résumez brièvement ce livre : "
    ],
    "combatend"=>[
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle parle du dernier combat) $HERIKA_NAME: #CHAT#",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle se moque du style de combat de {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT#",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente les armes de {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT#",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle admire le style de combat de {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle fait un court poème sur le dernier combat) $HERIKA_NAME:",
        "extra"=>["mood"=>"murmure","force_tokens_max"=>"50","dontuse"=>(time()%5==0)]   //20% de chance

    ],
    "quest"=>[
        "(Chat comme $HERIKA_NAME)",
        "$HERIKA_NAME, que devrions-nous faire à propos de cette quête '{$questName}' ?"
    ],

    "bleedout"=>[
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle se plaint qu'elle a failli être vaincue) $HERIKA_NAME: #CHAT#",
        ""
    ],

    "bored"=>[
        "",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle fait une blague sur l'emplacement actuel) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente le temps) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente l'heure et le jour) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente les derniers événements) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle fait une blague sur un mème de Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente les dieux de Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente la politique de Skyrim) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente un événement historique de l'univers de Elder Scrolls) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente un livre de l'univers de Elder Scrolls) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente en commençant par : Un jour, j'ai dû) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente en commençant par : As-tu entendu parler de ) $HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente en commençant par : Un sage m'a dit un jour) $$HERIKA_NAME: #CHAT# ",
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle commente sa relation amicale avec {$GLOBALS["PLAYER_NAME"]}) $HERIKA_NAME: #CHAT# "
    ],

    "goodmorning"=>[
        "(Complétez le texte en remplaçant le hashtag #CHAT#) (interprétez $HERIKA_NAME, elle parle de la sieste de {$GLOBALS["PLAYER_NAME"]})  $HERIKA_NAME: #CHAT#",
        "(bâillement après s'être réveillée). ahhhh  "
    ],

    "inputtext"=>[
        "(Complétez le texte en remplaçant le hashtag #CHAT#, appelle une fonction, indique le ton de voix entre parenthèses, tons valides : " . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Interprète seulement $HERIKA_NAME) $HERIKA_NAME:  #CHAT#" // Prompt is implicit

    ],
    "inputtext_s"=>[
        "(Complétez le texte en remplaçant le hashtag #CHAT#, appelle une fonction, indique le ton de voix entre parenthèses, tons valides : " . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Interprète seulement $HERIKA_NAME) $HERIKA_NAME:  #CHAT#", // Prompt is implicit
        "extra"=>["mood"=>"whispering"]
    ],
    "afterfunc"=>[
        "(Complétez le texte en remplaçant le hashtag #CHAT#, $HERIKA_NAME continue à parler à {$GLOBALS["PLAYER_NAME"]}, complétez simplement le texte de $HERIKA_NAME) $HERIKA_NAME: bien... #CHAT#",
        "extra"=>[]
    ],
    "lockpicked"=>[
        "(Complétez le texte en remplaçant le hashtag #CHAT#, $HERIKA_NAME commente l'objet qui a été forcé) $HERIKA_NAME: #CHAT#",
        "({$GLOBALS["PLAYER_NAME"]} a forcé la serrure de {$finalParsedData[3]})"
    ],
    "afterattack"=>[
        "(Complétez le texte en remplaçant le hashtag #CHAT#, $HERIKA_NAME prononce une courte phrase motivante pour le combat) $HERIKA_NAME: #CHAT#"
    ],
// Comme inputtext, mais sans la partie des appels de fonctions. Elle est susceptible d'être utilisée dans des scripts Papyrus
    "chatnf"=>[
        "(Complétez le texte en remplaçant le hashtag #CHAT#, indiquez le ton de voix entre parenthèses, tons valides : " . implode(",", (@is_array($GLOBALS["AZURETTS_CONF"]["validMoods"])?$GLOBALS["AZURETTS_CONF"]["validMoods"]:array())) . ") (Interprète seulement $HERIKA_NAME) $HERIKA_NAME: #CHAT#" // Prompt is implicit
    ],
    "diary"=>[
        "(Utilisez la fonction WriteIntoDiary pour écrire dans le journal) $HERIKA_NAME:" ,
        "Veuillez écrire dans votre journal un résumé des derniers dialogues et événements de {$GLOBALS["PLAYER_NAME"]} et $HERIKA_NAME, soyez créatif et donnez-lui un titre approprié"
    ],

);


   
?>
