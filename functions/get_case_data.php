<?php

/**
 * Fetches case data from the API or a JSON API mock for testing.
 *
 * @param array $options An associative array of options to use when fetching the data.
 *   The following options are supported:
 *   - practice_area: The practice area to search for.
 *   - county: (optional) The county to search for.
 *   - jurisdiction: The jurisdiction to search for.
 *   - keywords: (optional) The keywords to search for.
 *   - decision_date_gte: The minimum decision date to search for (gte = 'greater than or equal to')
 *   - page_size: The number of results to return per page.
 * @param bool $testing (optional) Whether to use a mock API for testing (default: false).
 *
 * @return array The array of case data.
 */
function get_case_data($options, $testing = false) {
    try {
        $response_data = ($testing) ? 
            json_decode(file_get_contents('../sample_response.json'), true) : 
            fetch_cases($options);
        $cases = extract_case_data($response_data);
        return $cases;
    } catch (Exception $e) {
        // Handle the error by returning an error message
        return ['error' => 'Error fetching case data: ' . $e->getMessage()];
    }
}

/**
 * Fetches the case data from the API or a JSON API mock for testing.
 *
 * @param array $options An associative array of options to use when fetching the data.
 *   The following options are supported:
 *   - practice_area: The practice area to search for.
 *   - county: (optional) The county to search for.
 *   - jurisdiction: The jurisdiction to search for.
 *   - keywords: (optional) The jurisdiction to search for.
 *   - decision_date_gte: The minimum decision date to search for.
 *   - page_size: The number of results to return per page.
 *
 * @return array The full response from the API or mock API.
 * @throws Exception If there's an error fetching the data or decoding the JSON response.
 */
function fetch_cases($options){
    // Define default options as an associative array with parameter names as keys
    $default_options = [
        'practice_area' => '',
        'county' => '',
        'jurisdiction' => '',
        'keywords' => '',
        'decision_date_gte' => '2013-01-01',
        'page_size' => 10,
    ];

    // Merge the default options with the provided options
    $options = array_merge($default_options, $options);

    // Set up the API endpoint and authentication details
    $base_url = 'https://api.case.law/v1/cases/';
    $api_key = getenv('CASE_LAW_API_KEY');

    $county = $options['county'] ? "{$options['county']} county" : "";
    $search_term = trim($options['practice_area'] . ' ' . $county . ' ' . $options['keywords']);

    // Construct the full API endpoint URL with the query parameters
    // full_case=true gives us the casebody, which includes the `opinion` that we use to turn into a blog post
    // ordering=random so we can grab the first three cases and not have too much duplication over time
    $url = $base_url . '?search=' . urlencode($search_term) . '&jurisdiction=' . $options['jurisdiction'] . '&decision_date__gte=' . $options['decision_date_gte'] . '&page_size=' . $options['page_size'] . '&full_case=true&ordering=random';

    // Set up the headers with the API key
    $headers = [
        //'Authorization: Token ' . $api_key,
        'Content-Type: application/json'
    ];

    // Initialize the cURL session
    $ch = curl_init();

    // Set the cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        throw new Exception('Error in cURL request: ' . curl_error($ch));
    }


    // Close the cURL session
    curl_close($ch);


    // Decode the JSON response into an associative array
    $response_data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error decoding JSON response: ' . json_last_error_msg());
    }
    return $response_data;
}


/**
 * Extracts the relevant data from each case in the response data.
 *
 * @param array $response_data The response data from the API.
 *
 * @return array The array of case data.
 * @warning If there's an error parsing the opinion for a case, a warning message will be logged.
 */
function extract_case_data($response_data){
    // Extract the relevant data from each case and store it in a list of arrays
    $cases = [];
    foreach ($response_data['results'] as $case) {
        // Not sure if every case will have a casebody, so handling possible errors
        try {
            $opinion = $case['casebody']['data']['opinions'][0]['text'];
        } catch (Exception $e) {
            trigger_error('Warning: Error parsing opinion for case ' . $case['id'] . ': ' . $e->getMessage(), E_USER_WARNING);
            $case_text = '';
        }

        $cases[] = [
            'id' => $case['id'],
            'name' => $case['name'],
            'decision_date' => $case['decision_date'],
            'frontend_url' => $case['frontend_url'],
            'court' => $case['court']['name'],
            'opinion' => $opinion
        ];
    }

    return $cases;
}
?>

