<?php



function split_sentences($paragraph)
{
    $paragraphNcr = br2nl($paragraph); // Some BR detected sometimes in response
	// Split the paragraph into an array of sentences using a regular expression
    preg_match_all('/[^\n?.!]+[?.!]/', $paragraphNcr, $matches);
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

    
    if (strpos($rawResponse, "(Context location") !== false) {
        $rawResponseSplited = explode(":", $rawResponse);
        $sentences = split_sentences($rawResponseSplited[2]);

    } else if (strpos($rawResponse, "(Context new location") !== false) {
        $rawResponseSplited = explode(":", $rawResponse);
        $sentences = split_sentences($rawResponseSplited[2]);

    } else if (strpos($rawResponse, "Herika:") !== false) {
        $rawResponseSplited = explode(":", $rawResponse);
        $sentences = split_sentences($rawResponseSplited[1]);
    } else {
        $sentences = split_sentences($rawResponse);
    }

    if ($GLOBALS["DEBUG_MODE"])
        print_r($sentences);

    $sentence = trim((implode(".", $sentences)));

    return $sentence;
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
        echo "<tr>";
        foreach ($row as $cell) {
            if (strpos($cell, 'background chat') !== false)
                echo "<td style='font-style:italic'>" . $cell . "</td>";
            else if (strpos($cell, 'Plugineer:') !== false)
                echo "<td  style='color:blue'>" . $cell . "</td>";
            else if (strpos($cell, 'obtains a quest') !== false)
                echo "<td  style='font-weight:bold'>" . $cell . "</td>";
            else if (strpos($cell, 'Herika:') !== false)
                echo "<td  style='color:green'>" . $cell . "</td>";
            else
                echo "<td>" . $cell . "</td>";
        }
        echo "</tr>";
    }

    // End the HTML table
    echo "</table>";
}






?>
