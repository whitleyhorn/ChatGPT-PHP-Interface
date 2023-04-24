<?php
include_once($_SERVER['DOCUMENT_ROOT']. '/functions/request_chatgpt.php');

$contentType = $_SERVER['CONTENT_TYPE'];

if ($contentType !== 'application/json') {
  http_response_code(400);
  echo json_encode(array('error' => 'Invalid content type'));
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$opinion = $data['opinion'] ?? '';

if($opinion) echo json_encode(get_case_extraction($opinion));

function get_case_extraction($opinion){
  // Extract Relevant Info On Cases
  // Shorten opinion so we don't hit the maximum context length in ChatGPT
  $shortened_opinion = substr($opinion, 0, 12000); 
  $request_body = array(
    'model' => 'gpt-3.5-turbo',
    'messages' => array(
        array('role' => 'system', 'content' => 'You are a helpful AI assistant'),
        array('role' => 'user', 'content' => "Act as a helpful AI assistant who specializes in taking long court opinions on legal cases and extracting all the information about the case that would be relevant to a layperson interested in the cases themselves. You do NOT extract any information that would be useless to a layperson, such as legal citations or jargon, and you convert any legal jargon that WOULD be relevant to a lay reader into easily understandable text.

  Please do your renowned extracting job on the following legal case court opinion text. The text was cut off early to be able to fit your character limit constraints. Your extraction should be roughly 500 words and cover as much relevant detail as possible. ${shortened_opinion}"),
    ),
    'temperature' => 0.5,
    'max_tokens' => 1000,
  );

  try {
    return request_chatgpt($request_body);
  } catch (Exception $e) {
    // re-throw the exception
    throw new Exception('Error extracting case information: ' . $e->getMessage());
  }
}
