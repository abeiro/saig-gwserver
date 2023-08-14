<?php 

// Fake Close conection asap

ignore_user_abort(true);
set_time_limit(1200);
header('Content-Encoding: none');
header('Content-Length: ' . ob_get_length());
header('Connection: close');
echo "\n";

@ob_end_flush();
@ob_flush();
@flush();


// We now have now almost 1200 seconds to do whaterver thing.
if ($_GET["action"]=="tokenizePrompt") {
	// tokenizePrompt($jsonEncodedData)
	require_once($path . "conf.php");
	require_once($path . "lib/$DRIVER.class.php");
	require_once($path . "lib/Misc.php");

	$jsonEncodedData=($_POST["jsonEncodedData"]);
	
	$costPerThousandTokens = getCostPerThousandInputTokens();
        // connect to local Python server servicing tokenizing requests
        $tokenizer_url = 'http://127.0.0.1:8090';
        $tokenizer_headers = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nContent-Length: ".strlen($jsonEncodedData),
                'content' => $jsonEncodedData,
                'timeout' => 30				
            )
        );
        $tokenizer_context = stream_context_create($tokenizer_headers);
        $tokenizer_buffer = file_get_contents('http://127.0.0.1:8090', false, $tokenizer_context);
        if ($tokenizer_buffer !== false) {
            $tokenizer_buffer = trim($tokenizer_buffer);
            if (ctype_digit($tokenizer_buffer)) { // make sure the response from tokenizer is a number (num of tokens)
                $numTokens = intval($tokenizer_buffer);
                $cost = $numTokens * $costPerThousandTokens * 0.001;
				$db = new sql();	// Instantiate just before make insert

                $db->insert_and_calc_totals(
                    'openai_token_count',
                    array(
                        'input_tokens' => $tokenizer_buffer,
                        'output_tokens' => '0',
                        'cost_USD' => $cost,
                        'localts' => time(),
                        'datetime' => date("Y-m-d H:i:s"),
                        'model' => $GLOBALS["GPTMODEL"]
                    )
                );
            }
        } else {
            error_log("error: tokenizer buf false\n");
        }
    
} 

?>
