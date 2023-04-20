<?php
include_once($_SERVER['DOCUMENT_ROOT']. '/functions/request_chatgpt.php');

function get_blog_post($extraction){
  // Turn case opinion extraction into blog post
  $request_body = array(
    'model' => 'gpt-3.5-turbo',
    'messages' => array(
        array('role' => 'system', 'content' => 'You are a blog writer who writes entertaining and informative content about legal cases.'),
        array('role' => 'user', 'content' => "Act as an entertaining, informative, and SEO-friendly blog writer who writes about interesting legal cases in a way that makes people want to learn about them. You write for laypeople, not legal professionals. I'm going to send you information about a legal case. Please provide a punchy headline of up to 10 words, a short summary of the case of about 100 words, a longer summary of the case of about 500 words, and finally, a 50 word summary of an unusual or interesting feature of the case. Case information: ${extraction}"),
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
