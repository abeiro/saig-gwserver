<?php
$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
require_once($path . "conf.php"); // API KEY must be there

function stt($file)
{
    
    

$url = $GLOBALS["LOCALWHISPER"]["URL"];

$fileData = file_get_contents($file);

// Configuración del contexto
$contextOptions = array(
    'http' => array(
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => $fileData
    )
);

$context = stream_context_create($contextOptions);

// Realizar la solicitud
$response = file_get_contents($url, false, $context);

// Manejar la respuesta
if ($response === false) {
    // Error handling
} else {
    // Procesar la respuesta
    
}
$reponseParsed=json_decode($response);

    
return $reponseParsed->DisplayText;

    
}


