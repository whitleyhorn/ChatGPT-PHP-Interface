<?php

print_r(get_case_data('Divorce', 'Morris', 'New Jersey', '2013-01-01', 10, false));

/**
 * Fetches case data from the API or a JSON API mock for testing.
 *
 * @param string $practice_area The practice area to search for.
 * @param string $county The county to search for.
 * @param string $jurisdiction The jurisdiction to search for.
 * @param string $decision_date_gte The minimum decision date to search for.
 * @param int $page_size The number of results to return per page.
 * @param bool $use_api Whether to use the actual API or a mock API for testing.
 *
 * @return array The array of case data.
 */
function get_case_data($practice_area, $county, $jurisdiction, $decision_date_gte, $page_size, $use_api){
     try {
        $response_data = fetch_cases(func_get_args());
        $cases = extract_case_data($response_data);
        return $cases;
    } catch (Exception $e) {
        // TODO: Handle the error in some way (just echoing for now)
        echo 'Error fetching case data: ' . $e->getMessage();
        return [];
    }
}

/**
 * Fetches the case data from the API or a JSON API mock for testing.
 *
 * @param array $options The options to use when fetching the data.
 *   The following options are supported:
 *   - use_api: Whether to use the actual API or a mock API for testing.
 *   - practice_area: The practice area to search for.
 *   - county: The county to search for.
 *   - jurisdiction: The jurisdiction to search for.
 *   - decision_date_gte: The minimum decision date to search for.
 *   - page_size: The number of results to return per page.
 *
 * @return array The full response from the API or mock API.
 * @throws Exception If there's an error fetching the data or decoding the JSON response.
 */
function fetch_cases($options){
    $default_options = [
        'use_api' => false,
        'practice_area' => '',
        'county' => '',
        'jurisdiction' => '',
        'decision_date_gte' => '2013-01-01',
        'page_size' => 10
    ];

    // Merge the default options with the provided options
    $options = array_merge($default_options, $options);
     
    if($options['use_api'] === false){
        // Read the JSON data from file, for testing, to reduce unnecessary API calls
        $file_path = 'sample_response.json';
        $response_data = json_decode(file_get_contents($file_path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error decoding JSON response: ' . json_last_error_msg());
        }
        return $response_data;
    }

    // Set up the API endpoint and authentication details
    $base_url = 'https://api.case.law/v1/cases/';
    $api_key = getenv('CASE_LAW_API_KEY');

    $county = $options['county'] ? "{$options['county']} county" : "";
    $search_term = $options['practice_area'] . ' ' . $county;

    // Construct the full API endpoint URL with the query parameters
    $url = $base_url . '?search=' . urlencode($search_term) . '&jurisdiction=' . $options['jurisdiction'] . '&decision_date__gte=' . $options['decision_date_gte'] . '&page_size=' . $options['page_size'] . '&full_case=true';

    // Set up the headers with the API key
    $headers = [
        'Authorization: Token ' . $api_key,
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
            // Shorten opinion so we can submit it to ChatGPT for summarization
            // $shortened_opinion = substr($opinion, 0, 14000);
            $shortened_opinion = substr($opinion, 0, 500); // For testing
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
            'opinion' => $shortened_opinion
        ];
    }

    return $cases;
}
?>

