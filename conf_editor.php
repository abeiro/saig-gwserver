<?php
error_reporting(E_ERROR);

// Credits to https://stackoverflow.com/questions/8226958/simple-php-editor-of-text-files
ob_start();

$url = 'conf_editor.php';
$file = 'conf.php';
$TITLE="Config editor";
echo file_get_contents("tmpl".DIRECTORY_SEPARATOR."head.html");

// check if form has been submitted
if (isset($_POST['text'])) {
    // save the text contents
    file_put_contents($file, $_POST['text']);

    // redirect to form again
    header(sprintf('Location: %s', $url));
    printf('<a href="%s">Moved</a>.', htmlspecialchars($url));
    exit();
}




// read the textfile
if ($_POST['text']) {
    $text = ($_POST['text']);
} else {
    $text = file_get_contents($file);
}

?>
<h1>Server configuration</h1>
<p>Push check button first to check syntax errors</p>
<!-- HTML form -->
<form action="" method="post" name="mainC" class="confeditor">
<div>
<textarea name="text" style="width:90%;min-height:300px" class="numbered"><?php echo htmlspecialchars($text); ?></textarea>
</div>
<br/>
<input type="button" name="save" value="Save" onclick='document.forms[0].target="";document.forms[0].action="conf_editor.php";document.forms[0].submit()'/>
<input type="button" value="Back" onclick="location.href='index.php'"/>
<input type="button" name="check" value="Check"  onclick='document.forms[0].target="checker";document.forms[0].action="conf_checker.php";document.forms[0].submit()'/>
</form>
<br/>
<iframe name="checker" border="1"  style="width:100%;min-height:200px;" scrolling="no"/>


<?php
$buffer=ob_get_contents();
ob_end_clean();
$title = "Gateway Server CP for {$GLOBALS["PLAYER_NAME"]}";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
?>
