<?php
// VALIDATE
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('HTTP/1.1 405 Method Not Allowed');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Only POST requests are allowed.')));
}

$practice_area = trim($_POST['practice-area']);
$location = trim($_POST['location']);
$keywords = trim($_POST['keywords']);

if (empty($practice_area) || empty($location) || empty($keywords)) {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Missing required parameters.')));
}

$keywords_array = array_map('trim', explode(",", $keywords));

if (count($keywords_array) < 1 || count($keywords_array) > 3) {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Number of keywords should be between 1 and 3.')));
}

// Check if the input length is between 10 and 500 characters
$input_length = mb_strlen($practice_area . ' ' . $location . ' ' . $keywords, 'UTF-8');
if ($input_length < 10 || $input_length > 500) {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Input length should be between 10 and 500 characters.')));
}

// MAKE REQUEST

$request_body = array(
  'model' => 'gpt-3.5-turbo',
  'messages' => array(
      array('role' => 'system', 'content' => 'You are a blog writer who specializes in writing keyword-rich, SEO-optimized content in the legal field that generates traffic to lawyer directory pages. You never make up content if you do not know an answer.'),
      array('role' => 'user', 'content' => "Please output 3 interesting {$practice_area} legal cases in {$location} based on the following keywords: {$keywords}. If you don't know of any legal cases that match this description, simply state that you do not know. Do NOT make up any legal cases. Please give the specific names of the cases so they can be researched further."),
  ),
  'temperature' => 0.7,
  'max_tokens' => 256,
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
echo implode("<br><br>", $outputs);
