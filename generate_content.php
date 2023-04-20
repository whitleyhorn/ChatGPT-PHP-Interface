<?php
include($_SERVER['DOCUMENT_ROOT'] . '/functions/get_case_data.php');
include($_SERVER['DOCUMENT_ROOT'] . '/functions/get_blog_post.php');
include($_SERVER['DOCUMENT_ROOT'] . '/functions/get_case_extraction.php');

// VALIDATE
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('HTTP/1.1 405 Method Not Allowed');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Only POST requests are allowed.')));
}

$practice_area = trim($_POST['practice-area']);
$jurisdiction = trim($_POST['jurisdiction']);
$county = trim($_POST['county']);
$keywords = trim($_POST['keywords']);

if (empty($practice_area) || empty($jurisdiction)) {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Missing required parameters.')));
}

$keywords_array = array_map('trim', explode(",", $keywords));

if (count($keywords_array) > 3) {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Number of keywords should be 3 or fewer.')));
}

// Check if the input length is between 10 and 500 characters
$input_length = strlen($practice_area . ' ' . $jurisdiction . ' ' . $county . ' ' . $keywords);
if ($input_length < 10 || $input_length > 500) {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('error' => 'Input length should be between 10 and 500 characters.')));
}

// GET CASE DATA
$decision_date_gte = '2013-01-01'; 
$options = [
    'practice_area' => $practice_area,
    'county' => $county,
    'jurisdiction' => $jurisdiction,
    'keywords' => $keywords,
    'decision_date_gte' => $decision_date_gte,
    'page_size' => 10,
    'testing' => false
];

$case_data = get_case_data($options);

if (isset($case_data['error'])) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array('error' => $case_data['error'])));
}

$contentArr = [];

for($i = 0; $i < 1; $i++){
  try {
    $content = [];
    $opinion = $case_data[$i]['opinion'];
    $extraction = get_case_extraction($opinion);
    $post = get_blog_post($extraction);
    $content['opinion'] = $opinion;
    $content['extraction'] = $extraction;
    $content['post'] = $post;
    array_push($contentArr, $content);
  } catch (Exception $e) {
    $error_message = 'Error processing case ' . $i . ': ' . $e->getMessage();
    array_push($contentArr, array('error' => $error_message));
  }
}

echo json_encode($contentArr);
