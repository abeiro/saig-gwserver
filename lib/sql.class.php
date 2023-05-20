<?php

class sql
{

  static private $link = null;


  function __construct()
  {
    self::$link = new SQLite3('mysqlitedb.db');
    self::$link->busyTimeOut(5000);

  }

  function __destruct()
  {
    self::$link->close();
  }


  function insert($table, $data)
  {
  
    self::$link->exec("INSERT INTO $table (" . implode(",", array_keys($data)) . ") VALUES ('" . implode("','", $data) . "')");


  }

  function query($query)
  {
  
    return self::$link->query($query);


  }
  
  function delete($table, $where = " false ")
  {
    self::$link->exec("DELETE FROM  $table WHERE $where");
  }

  function update($table, $set,$where = " false ")
  {
    self::$link->exec("UPDATE  $table set $set WHERE $where");
  }

  function fetchAll($q)
  {

    $results = self::$link->query("$q");
    $finalData=array();
    while($row = $results->fetchArray(SQLITE3_ASSOC))
      $finalData[]=$row;
    
    return $finalData;

  }


  function dequeue()
  {

    $results = self::$link->query("select  A.*,ROWID FROM  responselog a WHERE sent=0 order by ROWID asc");
    $finalData=array();
    while($row = $results->fetchArray())
      $finalData[]=$row;
    if (sizeof($finalData)>0)
      self::$link->query("update responselog set sent=1 where sent=0 and 1=1");

    return $finalData;

  }

  function lastDataFor($actor, $lastNelements = -10)
  {
    $lastDialogFull = array();
    $results = self::$link->query("select  distinct a.data  FROM  eventlog a WHERE data like '%$actor%' and type<>'combatend'  and type<>'book' and type<>'location'  
    and type<>'bored' and type<>'init' and type<>'lockpicked'  order by gamets asc,ts asc");
    while ($row = $results->fetchArray())
      $lastDialogFull[] = array('role' => 'user', 'content' => $row["data"]);


    $lastDialog = array_slice($lastDialogFull, $lastNelements);
    $last_location = null;

    // Remove Context Location part when repeated
    foreach ($lastDialog as $k => $message) {
      preg_match('/\(Context location: (.*)\)/', $message['content'], $matches);
      $current_location = isset($matches[1]) ? $matches[1] : null;
      if ($current_location === $last_location) {
        $message['content'] = preg_replace('/\(Context location: (.*)\)/', '', $message['content']);
      } else {
        $last_location = $current_location;
      }
      $lastDialog[$k]["content"] = $message['content'];
    }


    return $lastDialog;

  }

}

?>
