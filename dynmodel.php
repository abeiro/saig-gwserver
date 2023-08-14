<?php

$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR ;
require_once($path . "conf.php");

function removeAndReturnNext(&$array, $value) {
    $key = array_search($value, $array);
    
    if ($key !== false) {
        array_splice($array, $key, 1);
        
        if (isset($array[$key])) {
            return $array[$key];
        }
    }
    
    return current($array);
}

function DMgetCurrentModel() {
    $file=__DIR__.DIRECTORY_SEPARATOR."CurrentModel.json";
    if (!file_exists($file)) {
        DMsetCurrentModel("openai");
    }

    $cmj=file_get_contents($file);
    return json_decode($cmj,true);

}

function DMsetCurrentModel($model) {
    $file=__DIR__.DIRECTORY_SEPARATOR."CurrentModel.json";

    $cmj=file_put_contents($file,json_encode($model));

}

function DMtoggleModel() {
    $cm=DMgetCurrentModel();
    $arrayCopy=$GLOBALS["MODELS"];

    $nextModel=removeAndReturnNext($arrayCopy,$cm);

    DMsetCurrentModel($nextModel);
    return $nextModel;
}

$GLOBALS["MODEL"]=DMgetCurrentModel();

?>