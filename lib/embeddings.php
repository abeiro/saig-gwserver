<?php


function getEmbeddingLocal($text) {

    $url = 'http://172.16.1.128:8080/predictions/my_model/';
	
    $data = [
		"input"=> "$text"
		
    ];

    $headers = array(
		'Content-Type: application/json'
	);

    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($data),
            'timeout' => ($GLOBALS["HTTP_TIMEOUT"]) ?: 30
        )
    );

    $context = stream_context_create($options);
	$handle = fopen($url, 'r', false, $context);
	
	$buffer="";
	$c=0;
	while (!feof($handle)) {
		$line = fgetc($handle);
		$buffer.=$line;
		if ($line=="]")
			$c++;
		
		if ($c>1) 
			break;
		
		
	}

	$responseParsed=json_decode($buffer,true);
	
	$embedData=$responseParsed[0];
	
   
	return $embedData;
		
}

function getEmbeddingRemote($text) {

    //// OPENAI CODE
    $data = [
		"model"=> "text-embedding-ada-002",
		"input"=>$text
    ];

    

    $headers = array(
		'Content-Type: application/json',
		"Authorization: Bearer {$GLOBALS["OPENAI_API_KEY"]}"
	);

    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($data),
            'timeout' => ($GLOBALS["HTTP_TIMEOUT"]) ?: 30
        )
    );


    $url = 'https://api.openai.com/v1/embeddings';

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
	//print_r($response);
	$responseParsed=json_decode($response,true);
	
	//print_r($responseParsed);
	$embedData=$responseParsed["data"][0]["embedding"];
	
	//echo "Size of embedding array".sizeof($embedData);
    
	return $embedData;
		
}
?>
