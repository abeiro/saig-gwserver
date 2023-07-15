<?php

class sql
{

  static private $link = null;


  function __construct()
  {
    self::$link = new SQLite3(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mysqlitedb.db");
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

  function update($table, $set, $where = " false ")
  {
    self::$link->exec("UPDATE  $table set $set WHERE $where");
  }

  function execQuery($sqlquery)
  {
    self::$link->exec($sqlquery);
  }
  
  
  function fetchAll($q)
  {

    $results = self::$link->query("$q");
    $finalData = array();
    while ($row = $results->fetchArray(SQLITE3_ASSOC))
      $finalData[] = $row;

    return $finalData;

  }


  function dequeue()
  {

    //$results = self::$link->query("select  A.*,ROWID FROM  responselog a  order by ROWID asc");
    $results = self::$link->query("select  A.*,ROWID FROM  responselog a WHERE sent=0 order by ROWID asc");
    $finalData = array();
    while ($row = $results->fetchArray())
      $finalData[] = $row;
    if (sizeof($finalData) > 0)
      self::$link->query("update responselog set sent=1 where sent=0 and 1=1");

    return $finalData;

  }

  function lastDataFor($actor, $lastNelements = -10)
  {
    $lastDialogFull = array();
    $results = self::$link->query("select  
    case 
      when type like 'info%' or type like 'death%' or  type like 'funcret%' or type like 'location%' or data like '%background chat%' then 'The Narrator:'
      when type='book' then 'The Narrator: ({$GLOBALS["PLAYER_NAME"]} took the book ' 
      else '' 
    end||a.data  as data 
    FROM  eventlog a WHERE data like '%$actor%' 
    and type<>'combatend'  
    and type<>'bored' and type<>'init' and type<>'lockpicked' and type<>'infonpc' and type<>'infoloc' and type<>'info' and type<>'funcret' 
    and type<>'funccall'  order by gamets desc,ts desc LIMIT 0,50");
    $lastData = "";


    while ($row = $results->fetchArray()) {

      if ($lastData!=md5($row["data"])) {
        if ((strpos($row["data"],"{$GLOBALS["HERIKA_NAME"]}:")!==false)||((strpos($row["data"],"{$GLOBALS["PLAYER_NAME"]}:")!==false))) {
          $pattern = "/\([^)]*Context location[^)]*\)/";    // Remove (Context location.. from Herikas lines.
          $replacement = "";
          $row["data"] = preg_replace($pattern, $replacement, $row["data"]);
          $lastDialogFull[] = array('role' => 'user', 'content' => $row["data"]);

        } else
          $lastDialogFull[] = array('role' => 'user', 'content' => $row["data"]);

      }
      $lastData = md5($row["data"]);

    }

    // Date issues

    foreach ($lastDialogFull as $n => $line) {

      $pattern = '/(\w+), (\d{1,2}:\d{2} (?:AM|PM)), (\d{1,2})(?:st|nd|rd|th) of ([A-Za-z\ ]+), 4E (\d+)/';
      $replacement = 'Day name: $1, Hour: $2, Day Number: $3, Month: $4, 4th Era, Year: $5';
      $result = preg_replace($pattern, $replacement, $line["content"]);
      $lastDialogFull[$n]["content"] = $result;
    }


    // Clean context locations for Herikas dialog.


    $lastDialogFullReversed = array_reverse($lastDialogFull);
    $lastDialog = array_slice($lastDialogFullReversed, $lastNelements);
    $last_location = null;


    return $lastDialog;

  }

  function lastInfoFor($actor, $lastNelements = -2)
  {
    $lastDialogFull = array();
    $results = self::$link->query("select  case when type like 'info%' then 'The Narrator:' else '' end||a.data  as data  FROM  eventlog a 
    WHERE data like '%$actor%' and type in ('infoloc','infonpc')  order by gamets desc,ts desc LIMIT 0,50");
    $lastData = "";
    while ($row = $results->fetchArray()) {
      if ($lastData != md5($row["data"]))
        $lastDialogFull[] = array('role' => 'user', 'content' => $row["data"]);
      $lastData = md5($row["data"]);
    }

    $lastDialogFullReversed = array_reverse($lastDialogFull);
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


    foreach ($lastDialog as $n => $line) {

      $pattern = '/(\w+), (\d{1,2}:\d{2} (?:AM|PM)), (\d{1,2})(?:st|nd|rd|th) of ([A-Za-z\ ]+), 4E (\d+)/';
      $replacement = 'Day name: $1, Hour: $2, Day Number: $3, Month: $4, 4th Era, Year: $5';
      $result = preg_replace($pattern, $replacement, $line["content"]);
      $lastDialogFull[$n]["content"] = $result;
    }

    return $lastDialog;

  }

  function questJournal($quest)
  {
    global $db;
    if (empty($quest)) {
      /*$lastDialogFull = array();
      $results = self::$link->query("SElECT  distinct name,id_quest,briefing,giver_actor_id  
            FROM quests where coalesce(status,'pending')<>'completed' and stage<200 order by id_quest");
      if (!$results)
        return "no result";
      $data=[];
      while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
      }
      if (sizeof($data)==0)
        $data[] = "no active quests";
      
      return json_encode($data);
      */
      $results = $db->fetchAll("SElECT name,id_quest,briefing,'pending' as status FROM quests");
      $finalRow=[];
      foreach ($results as $row) {
          if (isset($finalRow[$row["id_quest"]]))
              continue;
          else
              $finalRow[$row["id_quest"]]=$row;
      }
      
      if (sizeof($finalRow)==0)
          $data[] = "no active quests";
      else
          $data=array_values($finalRow);
      
      $extraData=$db->get_current_task();
      
      $data[]=["side note"=>"$extraData"];
      
      return json_encode($data);
      
    } else {
      $lastDialogFull = array();
      $results = self::$link->query("SElECT  name,id_quest,briefing,data
      FROM quests where lower(id_quest)=lower('$quest') or lower(name)=lower('$quest') ");
      $lastOne = -1;
      $data = array();
      if (!$results) {
        $data["error"] = "quest not found, make sure you use id_quest";
        return json_encode($data);

      }
      while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $lastOne++;
        $data[] = $row;
      }
      if ($lastOne >= 0)
        $data[$lastOne]["stage_completed"] = "no";

      if (sizeof($data) == 0) {
        $data["error"] = "quest not found, make sure you use id_quest";

      }

      return json_encode($data);

    }
  }
  
  function speechJournal($topic)
  {

    
      $lastDialogFull = [];
      $results = self::$link->query("SElECT  speaker,speech,location,listener,topic as quest FROM speech
      where (speaker like '%$topic%' or  listener like '%$topic%' or location like '%$topic%' or  topic like '%$topic%') order by rowid desc");
      if (!$results)
        return  json_encode([]);

      $data=[];

      while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
      }

      if (sizeof($data)==0)
      return  json_encode([]);
      else if  (sizeof($data)<25)
       $dataReversed = array_reverse($data);
      else {
        $smalldata = array_slice($data, 25);
        $dataReversed = array_reverse($smalldata);
      }
   

      return json_encode($dataReversed);

 }

 function diaryLog($topic)
  {

      /*
      $results = self::$link->query("SElECT  topic,content,tags,people  FROM diarylog
      where (tags like '%$topic%' or topic like '%$topic%' or people like '%$topic%') order by gamets asc");
      */
      $topicTok=explode(" ",strtr($topic,array("'"=>"")));
      $topicFmt=implode(" OR ",$topicTok);
      $results = self::$link->query(SQLite3::escapeString("SElECT  topic,content,tags,people  FROM diarylogv2
      where (tags MATCH \"$topicFmt\" or topic MATCH \"$topicFmt\" or content MATCH \"$topicFmt\" or people MATCH \"$topicFmt\") ORDER BY rank"));
      
      
      if (!$results) {  // No match, will return a list of current memories
          $results = self::$link->query(SQLite3::escapeString("SElECT  topic,tags  FROM diarylogv2 order by gamets asc"));
          
          if (!$results) 
            return  json_encode([]);
        
          $data=[];

          while ($row = $results->fetchArray(SQLITE3_ASSOC)) 
            $data[] = $row;
     
      } else {       // Return best matching memory
   
        $data=[];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
          $data[] = $row;
          break;    // Only space for one memory
        }
        
      }

      return json_encode($data);

 }


 function get_current_task()
  {

      $results = self::$link->query("SElECT  description  FROM currentmission order by gamets desc");
      if (!$results)
        return  json_encode([]);
  
      $data="";

      $n=0;
      while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        
        if ($n==0)
          $data="Current task/quest/plan: {$row["description"]}.";
        else if ($n==1)
          $data.="Previous task/quest/plan: {$row["description"]}.";
        else
          break;
        $n++;
      }

      return $data;

 }


  function lastRetFunc($actor, $lastNelements = -2)
  {
    $lastDialogFull = array();
    $results = self::$link->query("select  a.data  as data  FROM  eventlog a 
    WHERE data like '%$actor%' and type in ('funcret')  order by gamets desc,ts desc LIMIT 0,1");
    $lastData = "";
    while ($row = $results->fetchArray()) {
      $pattern = "/\{(.*?)\(/";
      preg_match($pattern, $row["data"], $matches);
      $functionName = $matches[1];
      $lastDialogFull[] = array('role' => 'function', 'name' => $functionName, 'content' => $row["data"]);

    }

    $lastDialogFullReversed = array_reverse($lastDialogFull);
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
