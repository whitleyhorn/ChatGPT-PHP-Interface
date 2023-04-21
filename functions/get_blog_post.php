<?php
include_once($_SERVER['DOCUMENT_ROOT']. '/functions/request_chatgpt.php');

$contentType = $_SERVER['CONTENT_TYPE'];

if ($contentType !== 'application/json') {
  http_response_code(400);
  echo json_encode(array('error' => 'Invalid content type'));
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$summary = $data['summary'] ?? '';

if($summary) echo json_encode(get_blog_post($summary));

function get_blog_post($summary){
  // Turn case opinion summary into blog post
  $request_body = array(
    'model' => 'gpt-3.5-turbo',
    'messages' => array(
        array('role' => 'system', 'content' => 'You are a blog writer who writes entertaining and informative content about legal cases.'),
        array('role' => 'user', 'content' => "Act as an entertaining, informative, and SEO-friendly blog writer who writes about interesting legal cases in a way that makes people want to learn about them. You write for laypeople, not legal professionals. I'm going to send you information about a legal case. Please provide a punchy headline of up to 10 words, a short summary of the case of about 100 words, a longer summary of the case of about 500 words, and finally, a 50 word summary of an unusual or interesting feature of the case. Case information: ${summary}"),
    ),
    'temperature' => 0.8,
    'max_tokens' => 1500,
  );

  try {
    return request_chatgpt($request_body);
  } catch (Exception $e) {
    // re-throw the exception
    throw new Exception('Error getting blog post: ' . $e->getMessage());
  }
}
