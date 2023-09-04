<?php
error_reporting(E_ALL);

define("MAXIMUM_SENTENCE_SIZE", 125);

date_default_timezone_set('Europe/Madrid');


$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR;
require_once($path . "conf.php");
require_once($path . "dynmodel.php");

if (DMgetCurrentModel()!="openai") {
	require($path . "stream.php");
	die();
} else {
	require($path . "stream_functions.php");
	die();
	
}

?>
