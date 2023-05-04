<?php
$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
require_once($path . "conf.php"); // API KEY must be there

function tts($textString,$mood="friendly",$stringforhash)
{
    $region = 'westeurope';
    $AccessTokenUri = "https://" . $region . ".api.cognitive.microsoft.com/sts/v1.0/issueToken";
    $apiKey = $GLOBALS["AZURE_API_KEY"];

    $valid_tokens = array('angry', 'cheerful', 'assistant', 'calm', 'embarrassed', 'excited', 'lyrical', 'sad', 'shouting', 'whispering', 'terrified');
    $distancia_minima = PHP_INT_MAX;
    $token_mas_cercano = '';
    
    // Iteramos sobre cada token del array
    foreach ($valid_tokens as $token) {
        $distancia = levenshtein($mood, $token);
        if ($distancia < $distancia_minima) {
            $distancia_minima = $distancia;
            $token_mas_cercano = $token;
        }
    }
    $validMood=$token_mas_cercano;

    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header' => "Ocp-Apim-Subscription-Key: " . $apiKey . "\r\n" .
            "content-length: 0\r\n",
            'method' => 'POST',
        ),
    );

    $context = stream_context_create($options);

    //get the Access Token
    $access_token = file_get_contents($AccessTokenUri, false, $context);

    if (!$access_token) {
        return false;
    } else {
        //echo "Access Token: ". $access_token. "<br>";

        $ttsServiceUri = "https://" . $region . ".tts.speech.microsoft.com/cognitiveservices/v1";

        //$SsmlTemplate = "<speak version='1.0' xml:lang='en-us'><voice xml:lang='%s' xml:gender='%s' name='%s'>%s</voice></speak>";
        $doc = new DOMDocument();

        $root = $doc->createElement("speak");
        $root->setAttribute("version", "1.0");
        $root->setAttribute("xml:lang", "en-us");
        $root->setAttribute("xmlns:mstts", "https://www.w3.org/2001/mstts");


        $voice = $doc->createElement("voice");
        //$voice->setAttribute( "xml:lang" , "en-us" );
        $voice->setAttribute("xml:gender", "Female");
        $voice->setAttribute("name", "en-US-JennyNeural"); // Read https://learn.microsoft.com/es-es/azure/cognitive-services/speech-service/language-support?tabs=tts

        $text = $doc->createTextNode($textString);


        $prosody = $doc->createElement("prosody");
        $prosody->setAttribute("rate", "fast");
        $prosody->setAttribute( "volume" , "20" );
        

        $prosody->appendChild($text);

        $style = $doc->createElement("mstts:express-as");
        $style->setAttribute("style", $validMood);              // not supported for all voices
        $style->setAttribute("styledegree", "2");               // not supported for all voices
        //$style->setAttribute( "role" , "YoungAdultFemale" );  // not supported for all voices
        $style->appendChild($prosody);

        $voice->appendChild($style);
        $root->appendChild($voice);
        $doc->appendChild($root);
        $data = $doc->saveXML();

        //echo "tts post data: ". $data . "<br>";

        $options = array(
            'http' => array(
                'header' => "Content-type: application/ssml+xml\r\n" .
                "X-Microsoft-OutputFormat: riff-24khz-16bit-mono-pcm\r\n" .
                "Authorization: " . "Bearer " . $access_token . "\r\n" .
                "X-Search-AppId: 07D3234E49CE426DAA29772419F436CA\r\n" .
                "X-Search-ClientID: 1ECFAE91408841A480F00935DC390960\r\n" .
                "User-Agent: TTSPHP\r\n" .
                "content-length: " . strlen($data) . "\r\n",
                'method' => 'POST',
                'content' => $data,
            ),
        );

        $context = stream_context_create($options);

        // get the wave data
        $result = file_get_contents($ttsServiceUri, false, $context);
        if (!$result) {
            file_put_contents(dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR."soundcache/" . md5(trim($stringforhash)) . ".err", trim($$data));
            return false;
            //throw new Exception("Problem with $ttsServiceUri, $php_errormsg");
        } else {
            //echo "Wave data length: ". strlen($result);
        }
        //fwrite(STDOUT, $result);

        file_put_contents(dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR."soundcache/" . md5(trim($stringforhash)) . ".wav", $result);
        file_put_contents(dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR."soundcache/" . md5(trim($stringforhash)) . ".txt", trim($data));
    }
}