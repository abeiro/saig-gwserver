<?php

$db = new SQLite3(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'mysqlitedb.db');

$db->exec("delete from {$_GET["table"]} where rowid={$_GET["rowid"]}");

if ($_GET["table"]=="diarylog") {
    $db->exec("truncate table diarylogv2");
    $db->exec("insert into diarylogv2 select topic,content,tags,people,location from diarylog");
}
header("Location: ../index.php?table={$_GET["table"]}");

?>

