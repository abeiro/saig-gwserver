<?php
error_reporting(E_ERROR);

if (!file_exists("conf.php")) {
    @copy("conf.sample.php","conf.php");
    die(header("Location: conf_editor.php"));
}



require_once("lib/sql.class.php");
require_once("lib/Misc.php");
require_once("conf.php");
ob_start();
include("tmpl/head.html");
$db = new sql();


/* Actions */
if ($_GET["clean"]) {
    $db->delete("responselog","sent=1");

}
if ($_GET["reset"]) {
    $db->delete("eventlog","true");

}

if ($_GET["sendclean"]) {
       $db->update("responselog", "sent=0", "sent=1 ");

}

if ($_GET["cleanlog"]) {
    $db->delete("log","true");

}


if ($_GET["export"] && $_GET["export"]=="log") {
    while(@ob_end_clean());
    
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=log.csv");
    
    $data=$db->fetchAll("select response,url,prompt,rowid from log order by rowid desc");
    $n=0;
    foreach ($data as $row) {
        if ($n==0) {
               echo "'".implode("'\t'",array_keys($row))."'\n";
               $n++;
        }
        $rowCleaned=[];
        foreach ($row as $cellname=>$cell) {
            if ($cellname=="prompt")
                $cell=base64_encode(br2nl($cell));
            $rowCleaned[]=strtr($cell,array("\n"=>" ","\r"=>" ","'"=>"\""));
        }
        
        echo "'".implode("'\t'",($rowCleaned))."'\n";
    }
    die();
}

if ($_GET["reinstall"]) {
    require_once("cmd/install-db.php");
    header("Location: index.php?table=response");
}

if ($_POST["prompt"]) {
    require_once("chat/generic.php");
    $GLOBALS["DEBUG_MODE"]=true;
    $responseText=requestGeneric($_POST["prompt"],$_POST["preprompt"], $_POST["queue"],10);
   	$res=parseResponseV2($responseText,"",$_POST["queue"]);
    //die($res);
    header("Location: index.php?table=response");
}

if ($_POST["command"]) {
    $db->insert(
        'responselog',
        array(
            'localts' => time(),
            'sent' => 0,
            'text' => $_POST["command"]."@".$_POST["parameter"],
            'actor' => "{$GLOBALS["HERIKA_NAME"]}",
            'action' => 'command'
        )
    );
    header("Location: index.php?table=response");
}

if ($_POST["animation"]) {
    $db->insert(
        'responselog',
        array(
            'localts' => time(),
            'sent' => 0,
            'text' => trim($_POST["animation"]),
            'actor' => "{$GLOBALS["HERIKA_NAME"]}",
            'action' => 'animation'
        )
    );
    header("Location: index.php?table=response");
}

/* Actions */


echo "<h1>Gateway Server CP for {$GLOBALS["PLAYER_NAME"]}".(($_GET["autorefresh"])?" (autorefreshes every 5 secs)":"")." </h1>";
echo "
<div class='menupane'>
<nav id='menu'>
    <ul>
        <li><a href='#'>Data Tables</a>
            <ul>
                <li><a href='index.php?table=responselog'  title=''>Responses</a> </li>
                <li><a href='index.php?table=eventlog'  >Events</a></li>
                <li><a href='index.php?table=log'  >Log</a></li>
                <li><a href='index.php?table=quests'  >Quest journal</a></li>
                <li><a href='index.php?table=currentmission'  >Current mission</a></li>
                <li><a href='index.php?table=diarylog'  >Diary</a></li>
                <li><a href='index.php?table=books'  >Books</a></li>
                <li><a href='index.php?table=eventlog&autorefresh=true'  >Monitor events</a></li>
            </ul>
        </li>
        <li><a href='#'>Operations</a>
            <ul>
                <li><a href='index.php?clean=true&table=response'   title='Delete sent responses'  onclick=\"return confirm('Sure?')\">Clean sent</a> </li>
                <li><a href='index.php?sendclean=true&table=response' title='Marks unsent responses from queue What do you think about?'   onclick=\"return confirm('Sure?')\">Reset sent</a></li>
                <li><a href='index.php?reset=true&table=event'  title='Delete all events.'  onclick=\"return confirm('Sure?')\">Reset events</a></li>
                <li><a href='index.php?reinstall=true'  title='Drop all tables and then create them'  onclick=\"return confirm('Sure?')\">Reinstall</a></li>
                <li><a href='index.php?cleanlog=true'  title='Clean log table'  onclick=\"return confirm('Sure?')\">Clean Log</a></li>
                <li><a href='index.php?export=log'  title='Export Log (debugging purposes)' target='_blank' >Export Log</a></li>
            </ul>
        </li>

         <li><a href='#'>Troubleshooting</a>
            <ul>
                <li><a href='soundcache/'   target='_blank'>TTS cache</a></li>
                <li><a href='updater.php'   >Updater</a></li>
                <li><a href='tests.php'   target='_blank'>Test ChatGPT connection</a></li> 
            </ul>
        </li>
        
        <li><a href='conf_editor.php'>Configuration</a></li>
        
        <li><a href='#'>Immersion</a>
            <ul>
                <li><a href='addons/background'   target='_blank'>Background story generator</a></li>
                <li><a href='addons/diary'   target='_blank'>AI's diary</a></li>
            </ul>
        </li>
        <li><a href='#'>Please read</a>
            <ul>
                <li><a href='index.php?notes=true'>Notes from developers</a></li>
            </ul>
        </li>
        

    </ul>
</nav>

<span class='buttonify' style='display:block;position:absolute;top:5px;right:5px' onclick='toggleDP()' >Debug Pane</span> 

<!--<a href='index.php?openai=true'  >OpenAI API Usage</a> -->
</div>
<div style='clear:both' />

<div style='border:1px solid grey' class='debugpane'>
<form action='index.php' method='post'>
    <input type='text' name='prompt' value='(Chat as {$GLOBALS["HERIKA_NAME"]})'>
    <input type='text' size='128' name='preprompt' value='What...?'>
    <select name='queue'>
        <option value='AASPGDialogueHerika1WhatTopic'>What do you think about?</option>
        <option value='AASPGDialogueHerika2Branch1Topic'>What we should do?</option>
        <option value='AASPGDialogueHerika3Branch1Topic'>What do you know about this place?</option>
        <option value='AASPGQuestDialogue2Topic1B1Topic'>Tell me something (priority)</option>
        <option value='Simchat' selected='true'>Simulate input text</option>
    </select>
    <input type='submit' value='Request Chat'>
</form>
<form action='index.php' method='post'>
    <select name='command'>
        <option value='MoveTo'>MoveTo</option>
        <option value='SneakTo'>SneakTo</option>
        <option value='Attack'>Attack</option>
        <option value='Follow'>Follow</option>
        <option value='StopCurrent'>StopCurrent</option>
        <option value='Inspect'>Inspect</option>
        <option value='Relax'>Relax</option>
        <option value='StopAll'>StopAll</option>
        <option value='OpenInventory'>OpenInventory</option>
        <option value='SheatheWeapon'>SheatheWeapon</option>
        
    </select>
    <input type='text' value='' name='parameter' placeholder='parameter'>
    <input type='submit' value='Post command'>
</form>
<form action='index.php' method='post'>
<input type='text' name='animation' value=''>
<input type='submit' value='Post animation'>
</form>
</div>
";

if ($_GET["table"] == "responselog") {
    $results = $db->fetchAll("select  A.*,ROWID FROM responselog a order by ROWID asc");
    echo "<p>Response queue</p>";
    print_array_as_table($results);
}

if ($_GET["table"] == "eventlog") {
    $results = $db->fetchAll("select  A.*,ROWID FROM eventlog a order by gamets desc,ts  desc,localts desc");
    echo "<p>Event log</p>";
    print_array_as_table($results);
    if ($_GET["autorefresh"]) {
        header("Refresh:5");
        
    }
}
if ($_GET["table"] == "cache") {
    $results = $db->fetchAll("select  A.*,ROWID FROM eventlog a order by ts  desc");
    echo "<p>Event log</p>";
    print_array_as_table($results);
}
if ($_GET["table"] == "log") {
    $results = $db->fetchAll("select  A.*,ROWID FROM log a order by localts desc,rowid desc");
    echo "<p>Debug log</p>";
    print_array_as_table($results);
}

if ($_GET["table"] == "quests") {
    $results = $db->fetchAll("SElECT  name,id_quest,briefing,data from quests");
    $finalRow=[];
    foreach ($results as $row) {
        if (isset($finalRow[$row["id_quest"]]))
            continue;
        else
            $finalRow[$row["id_quest"]]=$row;
    }
    echo "<p>Quest log</p>";
    
    print_array_as_table(array_values($finalRow));
}

if ($_GET["table"] == "currentmission") {
    $results = $db->fetchAll("select  A.*,ROWID FROM currentmission A order by gamets desc,localts desc,rowid desc");
    echo "<p>Current Mission log</p>";
    print_array_as_table($results);
}

if ($_GET["table"] == "diarylog") {
    $results = $db->fetchAll("select  A.*,ROWID FROM diarylog A order by gamets asc,rowid asc");
    echo "<p>Diary log</p>";
    print_array_as_table($results);
    
}

if ($_GET["table"] == "books") {
    $results = $db->fetchAll("select  A.*,ROWID FROM books A order by gamets desc,rowid desc");
    echo "<p>Book log</p>";
    print_array_as_table($results);
    
}
if ($_GET["notes"]) {
    echo file_get_contents(__DIR__."/notes.html");
}


$buffer=ob_get_contents();
ob_end_clean();
$title = "Gateway Server CP for {$GLOBALS["PLAYER_NAME"]}";
$title.=(($_GET["autorefresh"])?" (autorefreshes every 5 secs)":"");
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
    
?>
