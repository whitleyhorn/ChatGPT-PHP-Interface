<?php

function request_chatgpt($request_body){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . getenv('OPENAI_API_KEY')
  ));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
  $response = curl_exec($ch);
  curl_close($ch);

  // PARSE RESULTS
  $results = json_decode($response, true);

  // HANDLE ERROR
  if(isset($results['error'])) {
    $error_message = $results['error']['message'];
    throw new Exception($error_message);
  }

  $outputs = array();
  foreach ($results['choices'] as $choice) {
    $message = $choice['message']['content'];
    $message = str_replace("\n", "<br>", $message); // replace line breaks with <br> tags
    $outputs[] = $message;
  }

  return $outputs[0];
}
