<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpEasyParcel\EasyParcel;
use PhpEasyParcel\ShipmentBuilder;
use PhpEasyParcel\Response;

// Replace with your actual API key
$apiKey = 'your-api-key'; // Same API key is used for both production and sandbox
$country = 'my'; // 'my' for Malaysia, 'sg' for Singapore, etc.

// Example 1: Using the production environment (default)
$productionClient = new EasyParcel($apiKey, $country);

// Example 2: Using the sandbox environment
// To use the sandbox environment, you need to modify the base URL
$sandboxOptions = [
    'base_uri' => "https://demo.connect.easyparcel.{$country}/"
];
$sandboxClient = new EasyParcel($apiKey, $country, $sandboxOptions);

// Now you can use either client for your API calls
try {
    echo "Checking credit balance in PRODUCTION environment...\n";
    $balanceResponse = $productionClient->checkBalance();
    $balance = new Response($balanceResponse);
    
    if ($balance->isSuccessful()) {
        echo "Production Credit Balance: " . $balance->getResult()['credit_balance'] . "\n\n";
    } else {
        echo "Error: " . $balance->getErrorMessage() . "\n\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n\n";
}

try {
    echo "Checking credit balance in SANDBOX environment...\n";
    $balanceResponse = $sandboxClient->checkBalance();
    $balance = new Response($balanceResponse);
    
    if ($balance->isSuccessful()) {
        echo "Sandbox Credit Balance: " . $balance->getResult()['credit_balance'] . "\n\n";
    } else {
        echo "Error: " . $balance->getErrorMessage() . "\n\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n\n";
}

// Example: Get shipping rates using the sandbox environment
try {
    echo "Getting shipping rates from SANDBOX...\n";
    
    // Create shipment using ShipmentBuilder
    $shipment = ShipmentBuilder::create()
        ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
        ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
        ->withDimensions(1.0, 10, 10, 10)
        ->build();
    
    $ratesResponse = $sandboxClient->getRates($shipment);
    $rates = new Response($ratesResponse);
    
    if ($rates->isSuccessful()) {
        $availableRates = $rates->getRates();
        echo "Available rates from sandbox:\n";
        foreach ($availableRates as $rate) {
            echo "- " . $rate['service_name'] . ": " . $rate['price'] . " " . $rate['currency'] . "\n";
            echo "  Service ID: " . $rate['service_id'] . "\n";
        }
        echo "\n";
    } else {
        echo "Error: " . $rates->getErrorMessage() . "\n\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n\n";
}

echo "Note: EasyParcel uses the same API key for both production and sandbox environments.\n";
echo "      The environment is determined by the base URL, not the API key.\n";
