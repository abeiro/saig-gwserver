<?php


// Should move this to conf.

$VECTORDB_URL= 'http://172.16.1.128:8000';
$VECTORDB_URL_COLLECTION_NAME="herika_memories";
$VECTORDB_URL_COLLECTION="";

function getCollectionUID() {
	
	global $VECTORDB_URL,$VECTORDB_URL_COLLECTION_NAME,$VECTORDB_URL_COLLECTION;
	
	$responseData=@file_get_contents("$VECTORDB_URL/api/v1/collections/herika_memories");
	if ($responseData===false) {
		$headers = array(
			'Content-Type: application/json',
		);

		$requestData = array(
			'name' => $VECTORDB_URL_COLLECTION_NAME,
		);
		
		$jsonData = json_encode($requestData);
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => implode("\r\n", $headers),
				'content' => $jsonData,
			)
		));	


		$response = file_get_contents("$VECTORDB_URL/api/v1/collections", false, $context);
		$responseData=@file_get_contents("$VECTORDB_URL/api/v1/collections/herika_memories");

	}

	$jsonDataRes = json_decode($responseData,true);


	$VECTORDB_URL_COLLECTION=$jsonDataRes["id"];
	
	return $VECTORDB_URL_COLLECTION;

}

function getElement($id) {
	
	global $VECTORDB_URL,$VECTORDB_URL_COLLECTION_NAME,$VECTORDB_URL_COLLECTION;
	
	$VECTORDB_URL_COLLECTION=getCollectionUID();

	$requestData = array(
		'ids'=>[$id]
	);

	// Convert the request data to JSON
	$jsonData = json_encode($requestData);

	//echo "$jsonData";
	// Set the HTTP headers
	$headers = array(
		'Content-Type: application/json',
	);

	// Create a stream context for the HTTP request
	$context = stream_context_create(array(
		'http' => array(
			'method' => 'POST',
			'header' => implode("\r\n", $headers),
			'content' => $jsonData,
		)
	));

	// Perform the HTTP POST request
	$response = file_get_contents($VECTORDB_URL."/api/v1/collections/$VECTORDB_URL_COLLECTION/get", false, $context);

	// Check if the response is successful
	if ($response === false) {
		// Handle error
		die("Error: Unable to fetch response.");
	}

	// Decode the JSON response
	$responseData = json_decode($response, true);

	// Handle the response data as needed
	// var_dump($responseData);
	return $responseData["documents"][0];

}


function deleteElement($id) {
	
	global $VECTORDB_URL,$VECTORDB_URL_COLLECTION_NAME,$VECTORDB_URL_COLLECTION;
	
	$VECTORDB_URL_COLLECTION=getCollectionUID();

	$requestData = array(
		'ids'=>[$id]
	);

	// Convert the request data to JSON
	$jsonData = json_encode($requestData);

	//echo "$jsonData";
	// Set the HTTP headers
	$headers = array(
		'Content-Type: application/json',
	);

	// Create a stream context for the HTTP request
	$context = stream_context_create(array(
		'http' => array(
			'method' => 'POST',
			'header' => implode("\r\n", $headers),
			'content' => $jsonData,
		)
	));

	// Perform the HTTP POST request
	$response = file_get_contents($VECTORDB_URL."/api/v1/collections/$VECTORDB_URL_COLLECTION/delete", false, $context);

	// Check if the response is successful
	if ($response === false) {
		// Handle error
		die("Error: Unable to fetch response.");
	}

	// Decode the JSON response
	$responseData = json_decode($response, true);

	// Handle the response data as needed
	// var_dump($responseData);
	return $responseData["documents"][0];

}

function storeMemory($embeddings,$text,$id) {
	
	global $VECTORDB_URL,$VECTORDB_URL_COLLECTION;
	
	$VECTORDB_URL_COLLECTION=getCollectionUID();
	
	$requestData = array(
		'documents' => [$text],
		'metadatas' => [["category"=>"background story"]],
		'embeddings'=>[$embeddings],
		'ids'=>[$id]
	);

	// Convert the request data to JSON
	$jsonData = json_encode($requestData);

	//echo "$jsonData";
	// Set the HTTP headers
	$headers = array(
		'Content-Type: application/json',
	);

	// Create a stream context for the HTTP request
	$context = stream_context_create(array(
		'http' => array(
			'method' => 'POST',
			'header' => implode("\r\n", $headers),
			'content' => $jsonData,
		)
	));

	// Perform the HTTP POST request
	$response = file_get_contents($VECTORDB_URL."/api/v1/collections/$VECTORDB_URL_COLLECTION/add", false, $context);

	// Check if the response is successful
	if ($response === false) {
		// Handle error
		die("Error: Unable to fetch response.");
	}

	// Decode the JSON response
	$responseData = json_decode($response, true);

	// Handle the response data as needed
	// var_dump($responseData);

}



function queryMemory($embeddings) {
	global $VECTORDB_URL,$VECTORDB_URL_COLLECTION;
	
	$VECTORDB_URL_COLLECTION=getCollectionUID();
	
	$requestData = array(
		'query_embeddings' => [$embeddings],
		'n_results'=>2
	);

	// Convert the request data to JSON
	$jsonData = json_encode($requestData);

	//echo "$jsonData";
	// Set the HTTP headers
	$headers = array(
		'Content-Type: application/json',
	);

	// Create a stream context for the HTTP request
	$context = stream_context_create(array(
		'http' => array(
			'method' => 'POST',
			'header' => implode("\r\n", $headers),
			'content' => $jsonData,
		)
	));

	// Perform the HTTP POST request
	$response = file_get_contents($VECTORDB_URL."/api/v1/collections/$VECTORDB_URL_COLLECTION/query", false, $context);

	// Check if the response is successful
	if ($response === false) {
		// Handle error
		die("Error: Unable to fetch response.");
	}

	// Decode the JSON response
	$responseData = json_decode($response, true);

	// Handle the response data as needed
	//var_dump($responseData);
	$link = new SQLite3(__DIR__."/../mysqlitedb.db");
	
	
	foreach ($responseData["ids"][0] as $n=>$id) {
		$results = $link->query("select message as content,uid,localts,momentum from memory where uid=$id order by uid asc");	
			while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
				if (($responseData["distances"][0][$n]+0)>0.35)
					continue;
				
				$dbResults[]=[
						"memory_id"=>$row["uid"],
						"briefing"=>$row["content"],
						"timestamp"=>$row["localts"],
						"distance"=>$responseData["distances"][0][$n]
				];
				break;
			}	

	}
	return ["item"=>"{$GLOBALS["HERIKA_NAME"]}'s memories","content"=>$dbResults];
}



?>
