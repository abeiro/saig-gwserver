<?php


$db = new SQLite3('mysqlitedb.db');

$db->exec("
DROP TABLE `eventlog`;");

$db->exec("
CREATE TABLE `eventlog` (
  `ts` text NOT NULL,
  `type` varchar(128) ,
  `data` text ,
  `sess` varchar(1024) ,
  `gamets` bigint NOT NULL,
  `localts` bigint NOT NULL
);");

$db->exec("
DROP TABLE `responselog`;");

$db->exec("
CREATE TABLE `responselog` (
  `localts` bigint NOT NULL,
  `sent` bigint NOT NULL,
  `actor` varchar(128) ,
  `text` text,
  `action` varchar(256),
  `tag` varchar(256)

);");

$db->exec("DROP TABLE `log`;");

$db->exec("
CREATE TABLE `log` (
  `localts` bigint NOT NULL,
  `prompt` text,
  `response` text,
  `url` text
);");


$db->exec("
CREATE TABLE `quests` (
  `id_quest` bigint NOT NULL,
  `name` text,
  `editor_id` text,
  `giver_actor_id` bigint,
  `reward` text,
  `target_id` text,
  `is_uniqe` bool,
  `mod` text,
  `stage` int,
  `current_target` text
);");



@mkdir(__DIR__ .DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR."soundcache");

?>
