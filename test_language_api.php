<?php
// Test the Language API directly
require_once 'wp-config.php';
require_once 'wp-load.php';

echo "Testing Language API...\n";

// Check if the file exists
$api_file = 'includes/Rest/LanguageAPI.php';
if (file_exists($api_file)) {
    echo "LanguageAPI.php file exists\n";
    require_once $api_file;
    echo "LanguageAPI.php loaded successfully\n";
    
    // Try to instantiate the class
    try {
        $api = new LanguageAPI();
        echo "LanguageAPI instantiated successfully\n";
    } catch (Exception $e) {
        echo "Error instantiating LanguageAPI: " . $e->getMessage() . "\n";
    }
} else {
    echo "LanguageAPI.php file not found\n";
}

// Test the REST API endpoint
echo "\nTesting REST API endpoint...\n";
$response = wp_remote_get(home_url('/wp-json/gst/v1/languages'));
if (is_wp_error($response)) {
    echo "REST API error: " . $response->get_error_message() . "\n";
} else {
    echo "REST API response code: " . wp_remote_retrieve_response_code($response) . "\n";
    echo "REST API response body: " . wp_remote_retrieve_body($response) . "\n";
}
?>
