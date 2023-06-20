<?php



function split_sentences($paragraph)
{
    $paragraphNcr = br2nl($paragraph); // Some BR detected sometimes in response
	// Split the paragraph into an array of sentences using a regular expression
    preg_match_all('/[^\n?.!,]+[?.!,]/', $paragraphNcr, $matches);
    //print_r($matches);
    $sentences=$matches[0];
    // Check if the last sentence is truncated (i.e., doesn't end with a period)
    /*$last_sentence = end($sentences);
    if (!preg_match('/[.?|]$/', $last_sentence)) {
        // Remove the last sentence if it's truncated
        array_pop($sentences);
    }*/

    if (is_array($sentences))
        return $sentences;
    else
        return array($sentences);
}

function br2nl($string)
{
    return preg_replace('/[\r\n]+/', '', preg_replace('/\<br(\s*)?\/?\>/i', "", $string));
}

function cleanReponse($rawResponse)
{
    // Remove Context Location between parenthesys
    $pattern = '/\(C[^)]*\)/';
    $replacement = ''; 
    $rawResponse= preg_replace($pattern, $replacement, $rawResponse);
    
    $pattern = '/\{.*?\}/';
    $replacement = ''; 
    $rawResponse= preg_replace($pattern, $replacement, $rawResponse);

    $rawResponse=strtr($rawResponse,array("{"=>"","}"=>""));
    
    if (strpos($rawResponse, "(Context location") !== false) {
        $rawResponseSplited = explode(":", $rawResponse);
        $toSplit=$rawResponseSplited[2];

    } else if (strpos($rawResponse, "(Context new location") !== false) {
        $rawResponseSplited = explode(":", $rawResponse);
        $toSplit=$rawResponseSplited[2];

    } else
        $toSplit=$rawResponse;
    
    if (strpos($toSplit, "Herika:") !== false) {
        $rawResponseSplited = explode(":", $toSplit);
        $toSplit=$rawResponseSplited[1];
    }

    $sentences=split_sentences($toSplit);
    
    if ($GLOBALS["DEBUG_MODE"])
        print_r($sentences);

    $sentence = trim((implode(".", $sentences)));

    $sentenceX = strtr($sentence,array(
            ",."=>","
            )
    );

    // Strip no ascii.
    $sentenceXX = str_replace(
        array('á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú','¿','¡'),
        array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', '', ''),
        $sentenceX
    );
    
    
    return $sentenceXX;
}

function print_array_as_table($data)
{
    // Start the HTML table
    if (sizeof($data) < 1)
        return;
    echo "<table border='1' width='100%'>";


    // Print the first row with array keys
    echo "<tr>";
    foreach (array_keys($data[0]) as $key) {
        echo "<th>" . $key . "</th>";
    }
    echo "</tr>";

    // Print the remaining rows with array values
    foreach ($data as $row) {
        
        if ($row["url"]) {
            $colorIndex=abs(crc32(preg_replace('/in \d+ secs/', '', $row["url"])))%5;
            $colors=array("white","#ffffc5","#d3fffb","#fad2ff","#e4ffce");
        } else
            $colorIndex=0;
        echo "<tr>";
        foreach ($row as $n=>$cell) {
            if ($n=="prompt") {
                echo "<td style='background-color:{$colors[$colorIndex]}'><span class='foldableCtl' onclick='togglePre(this)' style='cursor:pointer'>[+]</span><pre class='foldable'>" . $cell . "</pre></td>";
            }
            else if (strpos($cell, 'background chat') !== false)
                echo "<td style='font-style:italic;background-color:{$colors[$colorIndex]}'>" . $cell . "</td>";
            else if (strpos($cell, $GLOBALS["PLAYER_NAME"].':') !== false)
                echo "<td  style='color:blue;background-color:{$colors[$colorIndex]}'>" . $cell . "</td>";
            else if (strpos($cell, 'obtains a quest') !== false)
                echo "<td  style='font-weight:bold;background-color:{$colors[$colorIndex]}'>" . $cell . "</td>";
            else if (strpos($cell, 'Herika:') !== false)
                echo "<td  style='color:green;background-color:{$colors[$colorIndex]}'>" . $cell . "</td>";
            else    
                echo "<td  style='background-color:{$colors[$colorIndex]}'>" . $cell . "</td>";
        }
        echo "</tr>";
    }

    // End the HTML table
    echo "</table>";
}






?>
