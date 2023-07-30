<?php
error_reporting(E_ERROR);

if (!file_exists("conf.php")) {
    @copy("conf.sample.php", "conf.php");
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
    $db->delete("responselog", "sent=1");
}
if ($_GET["reset"]) {
    $db->delete("eventlog", "true");
}

if ($_GET["sendclean"]) {
    $db->update("responselog", "sent=0", "sent=1 ");
}

if ($_GET["cleanlog"]) {
    $db->delete("log", "true");
}

if ($_GET["export"] && $_GET["export"] == "log") {
    while (@ob_end_clean());

    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=log.csv");

    $data = $db->fetchAll("select response,url,prompt,rowid from log order by rowid desc");
    $n = 0;
    foreach ($data as $row) {
        if ($n == 0) {
            echo "'" . implode("'\t'", array_keys($row)) . "'\n";
            $n++;
        }
        $rowCleaned = [];
        foreach ($row as $cellname => $cell) {
            if ($cellname == "prompt")
                $cell = base64_encode(br2nl($cell));
            $rowCleaned[] = strtr($cell, array("\n" => " ", "\r" => " ", "'" => "\""));
        }

        echo "'" . implode("'\t'", ($rowCleaned)) . "'\n";
    }
    die();
}

if ($_GET["reinstall"]) {
    require_once("cmd/install-db.php");
    header("Location: index.php?table=response");
}

if ($_POST["prompt"]) {
    require_once("chat/generic.php");
    $GLOBALS["DEBUG_MODE"] = true;
    $responseText = requestGeneric($_POST["prompt"], $_POST["preprompt"], $_POST["queue"], 10);
    $res = parseResponseV2($responseText, "", $_POST["queue"]);
    //die($res);
    header("Location: index.php?table=response");
}

if ($_POST["command"]) {
    $db->insert(
        'responselog',
        array(
            'localts' => time(),
            'sent' => 0,
            'text' => $_POST["command"] . "@" . $_POST["parameter"],
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

?>

<!-- navbar -->
<?php
$debugPaneLink = true;
include("tmpl/navbar.php");
?>
<!--<a href='index.php?openai=true'  >OpenAI API Usage</a> -->

<div class="clearfix"></div>

<div class="container-fluid">

    <!-- debug pane -->
    <div class="debugpane d-none">
        <?php
        include("tmpl/debug-pane.php");
        ?>
    </div>

    <!-- auto info -->
    <?php
    if ($_GET["autorefresh"]) {
    ?>
    <p class="my-2">
        <small class='text-body-secondary fs-5'>Autorefreshes every 5 secs</small>
    </p>
    <?php
    }

    /* Actions */
    if ($_GET["table"] == "responselog") {
        $results = $db->fetchAll("select  A.*,ROWID FROM responselog a order by ROWID asc");
        echo "<h3 class='my-2'>Response queue</h3>";
        print_array_as_table($results);
    }

    if ($_GET["table"] == "eventlog") {
        $results = $db->fetchAll("select  A.*,ROWID FROM eventlog a order by gamets desc,ts  desc,localts desc");
        echo "<h3 class='my-2'>Event log</h3>";
        print_array_as_table($results);
        if ($_GET["autorefresh"]) {
            header("Refresh:5");
        }
    }
    if ($_GET["table"] == "cache") {
        $results = $db->fetchAll("select  A.*,ROWID FROM eventlog a order by ts  desc");
        echo "<h3 class='my-2'>Event log</h3>";
        print_array_as_table($results);
    }
    if ($_GET["table"] == "log") {
        $results = $db->fetchAll("select  A.*,ROWID FROM log a order by localts desc,rowid desc");
        echo "<h3 class='my-2'>Debug log</h3>";
        print_array_as_table($results);
    }

    if ($_GET["table"] == "quests") {
        $results = $db->fetchAll("SElECT  name,id_quest,briefing,data from quests");
        $finalRow = [];
        foreach ($results as $row) {
            if (isset($finalRow[$row["id_quest"]]))
                continue;
            else
                $finalRow[$row["id_quest"]] = $row;
        }
        echo "<h3 class='my-2'>Quest log</h3>";

        print_array_as_table(array_values($finalRow));
    }

    if ($_GET["table"] == "currentmission") {
        $results = $db->fetchAll("select  A.*,ROWID FROM currentmission A order by gamets desc,localts desc,rowid desc");
        echo "<h3 class='my-2'>Current Mission log</h3>";
        print_array_as_table($results);
    }

    if ($_GET["table"] == "diarylog") {
        $results = $db->fetchAll("select  A.*,ROWID FROM diarylog A order by gamets asc,rowid asc");
        echo "<h3 class='my-2'>Diary log</h3>";
        print_array_as_table($results);
    }

    if ($_GET["table"] == "books") {
        $results = $db->fetchAll("select  A.*,ROWID FROM books A order by gamets desc,rowid desc");
        echo "<h3 class='my-2'>Book log</h3>";
        print_array_as_table($results);
    } 
    ?>
</div> <!-- close main container -->
<?php

include("tmpl/footer.html");

$buffer = ob_get_contents();
ob_end_clean();
$title = "Gateway Server CP for {$GLOBALS["PLAYER_NAME"]}";
$title .= (($_GET["autorefresh"]) ? " (autorefreshes every 5 secs)" : "");
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
