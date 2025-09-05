<?php
/**
 * Test script for language saving functionality
 * Run this from WordPress root: php test_language_save.php
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

// Load the LanguageManager
require_once('wp-content/themes/get-sheilded-theme/includes/Language/LanguageManager.php');

echo "=== Language Save Test ===\n";

// Test data
$test_languages = [
    'en' => [
        'name' => 'English',
        'code' => 'en',
        'flag' => '🇺🇸',
        'country' => 'United States',
        'is_default' => true,
        'active' => true
    ],
    'es' => [
        'name' => 'Spanish',
        'code' => 'es',
        'flag' => '🇪🇸',
        'country' => 'Spain',
        'is_default' => false,
        'active' => true
    ]
];

echo "1. Testing direct WordPress option save...\n";

// Test 1: Direct WordPress option save
$result1 = update_option('gst_languages', $test_languages);
echo "   update_option result: " . ($result1 ? 'SUCCESS' : 'FAILED') . "\n";

$verify1 = get_option('gst_languages', []);
echo "   Verification - loaded " . count($verify1) . " languages\n";

// Test 2: Delete and add
echo "\n2. Testing delete + add approach...\n";
delete_option('gst_languages');
$result2 = add_option('gst_languages', $test_languages);
echo "   add_option result: " . ($result2 ? 'SUCCESS' : 'FAILED') . "\n";

$verify2 = get_option('gst_languages', []);
echo "   Verification - loaded " . count($verify2) . " languages\n";

// Test 3: Check database directly
echo "\n3. Checking database directly...\n";
global $wpdb;
$db_result = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name = 'gst_languages'");
echo "   Database records found: " . count($db_result) . "\n";
if (!empty($db_result)) {
    $unserialized = maybe_unserialize($db_result[0]->option_value);
    echo "   Database data count: " . (is_array($unserialized) ? count($unserialized) : 'not array') . "\n";
}

// Test 4: Test LanguageManager class
echo "\n4. Testing LanguageManager class...\n";
$language_manager = new LanguageManager();
$languages_from_class = $language_manager->get_all_languages();
echo "   LanguageManager loaded: " . count($languages_from_class) . " languages\n";

// Test 5: Test REST API endpoint
echo "\n5. Testing REST API endpoint...\n";
$request = new WP_REST_Request('POST', '/gst/v1/languages');
$request->set_param('languages', $test_languages);
$request->set_param('switcher_enabled', true);

$response = $language_manager->save_languages_simple($request);
echo "   REST API response success: " . ($response->get_data()['success'] ? 'YES' : 'NO') . "\n";

$final_verify = get_option('gst_languages', []);
echo "   Final verification - loaded " . count($final_verify) . " languages\n";

echo "\n=== Test Complete ===\n";
?>
