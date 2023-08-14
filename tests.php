<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Checking conf.php...";
if (!file_exists("conf.php")) {
    echo "not found<br>";
} else {
    echo "ok<br/>";
}


echo "Checking for database...";
if (!file_exists("mysqlitedb.db")) {
    echo "not found<br/>";
} else {
    echo "ok<br>";
}

echo "Checking for " . __DIR__ . DIRECTORY_SEPARATOR . "chat/generic.php...";
if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . "chat/generic.php")) {
    echo "not found<br/>";
} else {
    echo "ok<br/>";
}

echo "Trying to instantiate...";
$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR;
require_once($path . "conf.php");
require_once($path . "vendor/autoload.php");
require_once($path . "lib/sql.class.php");
require_once($path . "lib/Misc.php");
require_once($path . "chat/generic.php");


echo "ok<br/>";

echo "Trying to reach OpenAI...";



$headers = array(
    'Content-Type: application/json',
    "Authorization: Bearer {$GLOBALS["OPENAI_API_KEY"]}"
);

$options = array(
        'http' => array(
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => ($GLOBALS["HTTP_TIMEOUT"]) ?: 30
        )
    );


$url = 'https://api.openai.com/v1/models';

$context = stream_context_create($options);
error_reporting(E_ALL);
$handle = fopen($url, 'r', false, $context);
if (!$handle) {
    die("Error ".print_r(error_get_last(), true));
}

$buffer="";

while (!feof($handle)) {
    $buffer.=fread($handle, 1024);
}
$response=json_decode($buffer, true);
foreach ($response["data"] as $model) {
    if (strpos($model["id"], "gpt")!==false) {
        $models[]="<a href='https://www.google.com/search?q=".urlencode("model {$model["id"]} site:platform.openai.com")."'>{$model["id"]}</a>";
    }
}
echo "Success, available models: ";
echo implode(",", $models)."<br/>";


echo "Opening database...";
$db = new sql();
if (!$db) {
    echo "error<br/>";
} else {
    echo "ok<br/>";
}


echo "Trying to make a request...using {$GLOBALS["MODEL"]}<pre>";

$GLOBALS["DEBUG_MODE"] = true;
$response = requestGeneric("(Chat as $HERIKA_NAME)", "Are you there?", 'AASPGQuestDialogue2Topic1B1Topic', 1);
echo "</pre><p>Response: <h3><b>$response</b></p></h3>";

echo "Testing Azure Cache\n";
require_once($path . "lib/sharedmem.class.php"); // Caching token
$cache = new CacheManager();

print_r($cache);
echo "Current: ".$cache->get_cache()."<br/>";
if (!$cache->get_cache()) {
    $result = $cache->save_cache(time());
}
