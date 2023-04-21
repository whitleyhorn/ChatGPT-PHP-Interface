<?php
// VALIDATE
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('HTTP/1.1 405 Method Not Allowed');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Only POST requests are allowed.')));
}

if (!isset($_POST['input'])) {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Missing required parameter: input.')));
}

$notes = trim($_POST['input']);

// Validate the input to ensure that it is between 10 and 500 characters long.
if (strlen($notes) < 10 || strlen($notes) > 500) {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Invalid input length.')));
}


if (empty($notes)) {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Input parameter cannot be empty.')));
}

// MAKE REQUEST
$request_body = array(
  'model' => 'gpt-3.5-turbo',
  'messages' => array(
    array('role' => 'system', 'content' => 'You are a helpful assistant that writes professional sales emails.'),
    array('role' => 'user', 'content' => 'You are writing a professional sales email to the potential client mentioned in the following notes. Address the email to the potential client directly. Notes: ' . $notes),
  ),
  'temperature' => 0.7,
  'max_tokens' => 500,
);

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
$outputs = array();
foreach ($results['choices'] as $choice) {
  $message = $choice['message']['content'];
  $message = str_replace("\n", "<br>", $message); // replace line breaks with <br> tags
  $outputs[] = $message;
}

// RESPOND
echo json_encode(implode("<br><br>", $outputs));
