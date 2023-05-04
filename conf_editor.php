<?php

// Credits to https://stackoverflow.com/questions/8226958/simple-php-editor-of-text-files
ob_start();

$url = 'conf_editor.php';
$file = 'conf.php';
$TITLE="Config editor";
require "tmpl/head.html";

// check if form has been submitted
if (isset($_POST['text'])&& $_POST['save']) {
    // save the text contents
    file_put_contents($file, $_POST['text']);

    // redirect to form again
    header(sprintf('Location: %s', $url));
    printf('<a href="%s">Moved</a>.', htmlspecialchars($url));
    exit();
}

if (isset($_POST['text']) && $_POST['check']) {
  
    highlight_string("{$_POST['text']}");    
  
}



// read the textfile
if ($_POST['check']) {
    $text = ($_POST['text']);
} else {
    $text = file_get_contents($file);
}

?>
<!-- HTML form -->
<form action="" method="post" name="mainC">
<div>
<textarea name="text" style="width:90%;min-height:300px"><?php echo htmlspecialchars($text); ?></textarea>
</div>
<input type="submit" name="save" value="Save"/>
<input type="button" value="Back" onclick="location.href='index.php'"/>
<input type="submit" name="check" value="Check" />
</form>

<?php
$buffer=ob_get_contents();
ob_end_clean();
$title = "Gateway Server CP for {$GLOBALS["PLAYER_NAME"]}";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
?>
