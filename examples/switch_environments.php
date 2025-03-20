<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpEasyParcel\EasyParcel;
use PhpEasyParcel\ShipmentBuilder;
use PhpEasyParcel\Response;

// Replace with your actual API key
$apiKey = 'your-api-key'; // Same API key is used for both production and sandbox
$country = 'my'; // 'my' for Malaysia, 'sg' for Singapore, etc.

// Method 1: Using the constructor options
echo "Method 1: Using constructor options\n";
echo "==================================\n";

// Production environment (default)
$productionClient = new EasyParcel($apiKey, $country);
echo "Using production environment: " . $productionClient->getBaseUrl() . "\n";

// Sandbox environment via constructor options
$sandboxOptions = [
    'base_uri' => "https://demo.connect.easyparcel.{$country}/"
];
$sandboxClient = new EasyParcel($apiKey, $country, $sandboxOptions);
echo "Using sandbox environment: " . $sandboxClient->getBaseUrl() . "\n\n";

// Method 2: Using the environment setter methods
echo "Method 2: Using environment setter methods\n";
echo "========================================\n";

// Create a client and switch between environments
$client = new EasyParcel($apiKey, $country);

// Switch to sandbox
$client->useSandbox();
echo "Switched to sandbox: " . $client->getBaseUrl() . "\n";

// Switch back to production
$client->useProduction();
echo "Switched to production: " . $client->getBaseUrl() . "\n\n";

// Example of checking balance in both environments
echo "Example: Checking balance in both environments\n";
echo "============================================\n";

try {
    // Create a client
    $client = new EasyParcel($apiKey, $country);
    
    // Check balance in production
    echo "Checking balance in production...\n";
    $balanceResponse = $client->checkBalance();
    $balance = new Response($balanceResponse);
    
    if ($balance->isSuccessful()) {
        echo "Production Credit Balance: " . $balance->getResult()['credit_balance'] . "\n\n";
    } else {
        echo "Error: " . $balance->getErrorMessage() . "\n\n";
    }
    
    // Switch to sandbox and check balance
    $client->useSandbox();
    echo "Checking balance in sandbox...\n";
    $balanceResponse = $client->checkBalance();
    $balance = new Response($balanceResponse);
    
    if ($balance->isSuccessful()) {
        echo "Sandbox Credit Balance: " . $balance->getResult()['credit_balance'] . "\n\n";
    } else {
        echo "Error: " . $balance->getErrorMessage() . "\n\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n\n";
}

echo "Note: EasyParcel uses the same API key for both production and sandbox environments.\n";
echo "      The environment is determined by the base URL, not the API key.\n";
