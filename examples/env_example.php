<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpEasyParcel\Config;
use PhpEasyParcel\EasyParcel;

// Load environment variables from .env file
Config::loadEnv();

// Create EasyParcel instance using environment variables
// No need to pass API key or country - they'll be loaded from .env
$easyparcel = new EasyParcel();

// Check if we're in sandbox mode based on .env configuration
echo "Current environment: " . (Config::isSandbox() ? "Sandbox" : "Production") . "\n";
echo "Base URL: " . $easyparcel->getBaseUrl() . "\n";

// Check balance
try {
    $response = $easyparcel->checkBalance();
    
    if (isset($response['error_code']) && $response['error_code'] === '0') {
        echo "Credit Balance: " . $response['result'] . "\n";
    } else {
        echo "Error: " . $response['error_remark'] . "\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

// You can also manually override the environment
echo "\nSwitching to sandbox mode manually:\n";
$easyparcel->useSandbox();
echo "New Base URL: " . $easyparcel->getBaseUrl() . "\n";

// Or switch back to production
echo "\nSwitching to production mode manually:\n";
$easyparcel->useProduction();
echo "New Base URL: " . $easyparcel->getBaseUrl() . "\n";

// You can also create an instance with explicit API key
echo "\nCreating a new instance with explicit API key:\n";
$customEasyparcel = new EasyParcel('your-api-key-here', 'my');
echo "Base URL: " . $customEasyparcel->getBaseUrl() . "\n";

// Note about API key usage
echo "\nNote: EasyParcel uses the same API key for both production and sandbox environments.\n";
echo "The environment is determined by the base URL, not the API key.\n";
