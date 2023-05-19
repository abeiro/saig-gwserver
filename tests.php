<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Checking conf.php...";
if (!file_exists("conf.php"))
    echo "not found<br>";
else
    echo "ok<br/>";


echo "Checking for database...";
if (!file_exists("mysqlitedb.db"))
    echo "not found<br/>";
else
    echo "ok<br>";

echo "Checking for " . __DIR__ . DIRECTORY_SEPARATOR . "chat/generic.php...";
if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . "chat/generic.php"))
    echo "not found<br/>";
else
    echo "ok<br/>";

echo "Trying to instantiate...";
$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR;
require_once($path . "conf.php");
require_once($path . "vendor/autoload.php");
require_once($path . "lib/sql.class.php");
require_once($path . "lib/Misc.php");
require_once($path . "chat/generic.php");


echo "ok<br/>";

echo "Trying to reach OpenAI...";
$client = OpenAI::client($GLOBALS["OPENAI_API_KEY"]);

$response = $client->models()->retrieve('gpt-3.5-turbo');
if ($response->id) {
    echo "Ok {$response->id} available<br/>";
} else
    echo "Not ok<br/>";


echo "Opening database...";
$db = new sql();
if (!$db)
    echo "error<br/>";
else
    echo "ok<br/>";


echo "Trying to make a request...<pre>";
/*
$GLOBALS["DEBUG_MODE"] = true;
$response = requestGeneric("(Chat as Herika)", "Are you there?", 'AASPGQuestDialogue2Topic1B1Topic', 1);
echo "</pre><p>Response: <h3><b>$response</b></p></h3>";
*/
echo "Testing Azure Cache\n";
require_once($path . "lib/sharedmem.class.php"); // Caching token
$cache = new CacheManager();

print_r($cache);
echo "Current: ".$cache->get_cache()."<br/>";
if (!$cache->get_cache())
    $result = $cache->save_cache(time());


?>
