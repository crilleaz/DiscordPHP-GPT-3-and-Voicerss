<?php

function terminator($q){
    urlencode($q);
    $api_key = ""; //api.openai.com token
    $voicerss_key = ''; // Voicerss token key https://www.voicerss.org/api/
    $rapid_key = ''; // voicerss-text-to-speech.p.rapidapi.com
    $path_to_tts = '/var/www/html/bots/ai/tts/';

    $q = str_replace('"', '', $q);
    $prompt = '{
        "prompt": "'.$q.'",
        "max_tokens":4000
    }';

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.openai.com/v1/engines/text-davinci-003/completions",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_CONNECTTIMEOUT => 30,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $prompt,
      CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $api_key
      ),
    ));
    echo 'Prompt: ' . $prompt . PHP_EOL;

    $response1 = curl_exec($curl);

    // Attempt to decode the response as JSON
    $response1 = json_decode($response1, true);
    
    // Check if the "choices" key exists in the array
    if(array_key_exists("choices", $response1) && !empty($response1["choices"]) && !empty($response1["choices"][0]) && !empty($response1["choices"][0]["text"])) {
        $ai_response = $response1["choices"][0]["text"];
    }else{
        // set a default value if the key does not exist
        $ai_response = "";
    }
    
    // URL encode the AI response
    $ai_response = urlencode($ai_response);
    
    // Execute the cURL request to the VoiceRSS API
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://voicerss-text-to-speech.p.rapidapi.com/?key=$voicerss_key",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "src=". $ai_response ."&hl=en-us&v=Linda&r=0&c=mp3&f=32khz_16bit_stereo", // see voicerss.org/api/ for voices and settings
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: voicerss-text-to-speech.p.rapidapi.com",
            "X-RapidAPI-Key: $rapid_key",
            "content-type: application/x-www-form-urlencoded"
        ],
    ]);
    echo 'Response: ' . $ai_response . PHP_EOL;
    
    $response = curl_exec($curl);
    $random_string = substr(rand(), 0, 6);
    $mp3file = fopen($path_to_tts . $random_string . ".mp3", "w");
    fwrite($mp3file, $response);
    fclose($mp3file);
    curl_close($curl);
}

function unlink_mp3(){
    global $path_to_tts;
    $files = glob($path_to_tts . '*.mp3'); // get a list of all .mp3 files in the folder
    foreach($files as $file){ // iterate through the list
        unlink($file); // unlink each file
    }
}
?>