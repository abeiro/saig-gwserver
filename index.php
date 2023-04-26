<?php
error_reporting(E_ERROR);
require_once("lib/sql.class.php");
require_once("lib/Misc.php");
require_once("conf.php");
include("tmpl/head.html");
$db = new sql();

if ($_GET["clean"]) {
    $db->delete("responselog","sent=1");

}
if ($_GET["reset"]) {
    $db->delete("eventlog","true");

}



if ($_GET["reinstall"]) {
    require_once("cmd/install-db.php");
    header("Location: index.php?table=response");
}

if ($_POST["prompt"]) {
    require_once("chat/generic.php");
    $GLOBALS["DEBUG_MODE"]=false;
    requestGeneric($_POST["prompt"],$_POST["preprompt"], 'AASPGQuestDialogue2Topic1B1Topic',15);
    header("Location: index.php?table=response");
}

if ($_POST["command"]) {
    $db->insert(
        'responselog',
        array(
            'localts' => time(),
            'sent' => 0,
            'text' => $_POST["command"],
            'actor' => "Herika",
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
            'actor' => "Herika",
            'action' => 'animation'
        )
    );
    header("Location: index.php?table=response");
}


echo "<h1>Gateway Server CP for {$GLOBALS["PLAYER_NAME"]} </h1>";
echo "
<div class='menupane'>
<a href='index.php?table=response' class='buttonify'>Responses</a> ::
<a href='index.php?table=event'  class='buttonify'>Events</a> ::
<a href='index.php?table=log'  class='buttonify'>Log</a> ::
<a href='index.php?clean=true&table=response'  class='buttonify' onclick=\"return confirm('Sure?')\">Clean sent</a> ::
<a href='index.php?reset=true&table=event'  class='buttonify' onclick=\"return confirm('Sure?')\">Reset events</a> ::
<a href='index.php?reinstall=true'  class='buttonify' onclick=\"return confirm('Sure?')\">Reinstall</a> ::
<span onclick='toggleDP()' class='buttonify'>Debug Pane</span> 

<!--<a href='index.php?openai=true'  class='buttonify'>OpenAI API Usage</a> -->
</div>

<script>
function toggleDP() {document.getElementsByClassName('debugpane')[0].style.display=document.getElementsByClassName('debugpane')[0].style.display=='block'?'none':'block'}
</script>

<div style='border:1px solid grey' class='debugpane'>
<form action='index.php' method='post'>
    <input type='text' name='prompt' value='(Chat as Herika)'>
    <input type='text' size='128' name='preprompt' value='{$GLOBALS["PLAYER_NAME"]}: What...?'>
    <input type='submit' value='Request Chat'>
</form>
<form action='index.php' method='post'>
    <select name='command'>
        <option value='IdleLookFar'>IdleLookFar</option>
    </select>
    <input type='submit' value='Post command'>
</form>
<form action='index.php' method='post'>
<input type='text' name='animation' value=''>
<input type='submit' value='Post animation'>
</form>
</div>
";

if ($_GET["table"] == "response") {
    $results = $db->fetchAll("select  A.*,ROWID FROM responselog a order by ROWID asc");
    echo "<p>Response queue</p>";
    print_array_as_table($results);
}

if ($_GET["table"] == "event") {
    $results = $db->fetchAll("select  A.*,ROWID FROM eventlog a order by ts  desc");
    echo "<p>Event log</p>";
    print_array_as_table($results);
}
if ($_GET["table"] == "cache") {
    $results = $db->fetchAll("select  A.*,ROWID FROM eventlog a order by ts  desc");
    echo "<p>Event log</p>";
    print_array_as_table($results);
}
if ($_GET["table"] == "log") {
    $results = $db->fetchAll("select  A.*,ROWID FROM log a order by localts  desc");
    echo "<p>Repsonse log</p>";
    print_array_as_table($results);
}
?>
