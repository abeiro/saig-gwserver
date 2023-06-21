<?php

class sql
{

  static private $link = null;


  function __construct()
  {
    self::$link = new SQLite3(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."mysqlitedb.db");
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

    //$results = self::$link->query("select  A.*,ROWID FROM  responselog a  order by ROWID asc");
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
    $results = self::$link->query("select  case when type like 'info%' or type like 'funcret%' or type like 'location%' or data like '%background chat%' then 'The Narrator:' else '' end||a.data  as data FROM  eventlog a WHERE data like '%$actor%' 
    and type<>'combatend'  and type<>'book'  
    and type<>'bored' and type<>'init' and type<>'lockpicked' and type<>'infonpc' and type<>'infoloc' and type<>'info' and type<>'funcret' 
    and type<>'funccall'  order by gamets desc,ts desc LIMIT 0,50"); 
    $lastData="";
    while ($row = $results->fetchArray()) {
      if ($lastData!=md5($row["data"])) {
        if ((strpos($row["data"],"Herika:")!==false)||((strpos($row["data"],"{$GLOBALS["PLAYER_NAME"]}:")!==false))) {
          $pattern = "/\([^)]*Context location[^)]*\)/";    // Remove (Context location.. from Herikas lines.
          $replacement = "";
          $row["data"] = preg_replace($pattern, $replacement, $row["data"]);
          $lastDialogFull[] = array('role' => 'user', 'content' => $row["data"]);
          
        } else
          $lastDialogFull[] = array('role' => 'user', 'content' => $row["data"]);
        
      }
      $lastData=md5($row["data"]);
      
    }
    
    // Date issues
    
    foreach ($lastDialogFull as $n=>$line) {
     
      $pattern = '/(\w+), (\d{1,2}:\d{2} (?:AM|PM)), (\d{1,2})(?:st|nd|rd|th) of (\w+), (4E \d+)/';
      $replacement = 'Day:$1, Hour: $2, Day Number: $3, Month: $4, Age: $5';
      $result = preg_replace($pattern, $replacement, $line["content"]);
      $lastDialogFull[$n]["content"]=$result;
    }
      

    // Clean context locations for Herikas dialog.
    
    
    $lastDialogFullReversed=array_reverse($lastDialogFull);
    $lastDialog = array_slice($lastDialogFullReversed, $lastNelements);
    $last_location = null;

    // Remove Context Location part when repeated
    /*foreach ($lastDialog as $k => $message) {
      preg_match('/\(Context location: (.*)\)/', $message['content'], $matches);
      $current_location = isset($matches[1]) ? $matches[1] : null;
      if ($current_location === $last_location) {
        $message['content'] = preg_replace('/\(Context location: (.*)\)/', '', $message['content']);
      } else {
        $last_location = $current_location;
      }
      $lastDialog[$k]["content"] = $message['content'];
    }*/


    return $lastDialog;

  }

  function lastInfoFor($actor, $lastNelements = -2)
  {
    $lastDialogFull = array();
    $results = self::$link->query("select  case when type like 'info%' then 'The Narrator:' else '' end||a.data  as data  FROM  eventlog a 
    WHERE data like '%$actor%' and type in ('infoloc','infonpc')  order by gamets desc,ts desc LIMIT 0,50"); 
    $lastData="";
    while ($row = $results->fetchArray()) {
      if ($lastData!=md5($row["data"]))
        $lastDialogFull[] = array('role' => 'user', 'content' => $row["data"]);
      $lastData=md5($row["data"]);
    }

    $lastDialogFullReversed=array_reverse($lastDialogFull);
    $lastDialog = array_slice($lastDialogFullReversed, $lastNelements);
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

    // Date issues
   foreach ($lastDialog as $n=>$line) {
     
      $pattern = '/(\w+), (\d{1,2}:\d{2} (?:AM|PM)), (\d{1,2})(?:st|nd|rd|th) of (\w+), (4E \d+)/';
      $replacement = 'Day:$1, Hour: $2, Day Number: $3, Month: $4, Age: $5';
      $result = preg_replace($pattern, $replacement, $line["content"]);
      $lastDialog[$n]["content"]=$result;
    }
    
    return $lastDialog;

  }
  
  
  function lastRetFunc($actor, $lastNelements = -2)
  {
    $lastDialogFull = array();
    $results = self::$link->query("select  a.data  as data  FROM  eventlog a 
    WHERE data like '%$actor%' and type in ('funcret')  order by gamets desc,ts desc LIMIT 0,1"); 
    $lastData="";
    while ($row = $results->fetchArray()) {
      $pattern = "/\{(.*?)\(/";
      preg_match($pattern, $row["data"], $matches);
      $functionName = $matches[1];
      $lastDialogFull[] = array('role' => 'function', 'name'=>$functionName,'content' => $row["data"]);
      
    }

    $lastDialogFullReversed=array_reverse($lastDialogFull);
    $lastDialog = array_slice($lastDialogFullReversed, $lastNelements);
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
