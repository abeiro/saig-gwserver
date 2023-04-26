<?php
error_reporting(E_ERROR);
require_once("lib/sql.class.php");
require_once("lib/Misc.php");
require_once("conf.php");

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
<div>
<a href='index.php?table=response'>Responses</a> ::
<a href='index.php?table=event'>Events</a> ::
<a href='index.php?table=log'>Log</a> ::
<a href='index.php?clean=true&table=response'>Clean sent</a> ::
<a href='index.php?reset=true&table=event'>Reset events</a> ::
<a href='index.php?reinstall=true'>Reinstall</a> ::
<!--<a href='index.php?openai=true'>OpenAI API Usage</a> -->::
</div>
<div style='border:1px solid grey'>
<form action='index.php' method='post'>
    <input type='text' name='prompt' value='(Chat as Herika)'>
    <input type='text' size='128' name='preprompt' value='{$GLOBALS["PLAYER_NAME"]}': What do you think about Adrianne?'>
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
