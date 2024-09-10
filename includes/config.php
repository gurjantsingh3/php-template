<?php
// Configuration settings
define('API_HOST', 'https://mwbapi.mytruebank.com'); // Production API host
define('API_AUTH_HEADER', 'Basic dXNlcm5hbWU6cGFzc3dvcmQ=');

// Function to fetch API data
function fetchApiData($endpoint) {
    $url = API_HOST . $endpoint;

    $options = [
        'http' => [
            'header'  => "Authorization: " . API_AUTH_HEADER . "\r\n" .
                         "Content-Type: application/json\r\n",
            'method'  => 'GET',
        ],
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return ['error' => 'Failed to fetch data'];
    }

    return json_decode($response, true);
}
?>
