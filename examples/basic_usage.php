<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpEasyParcel\EasyParcel;
use PhpEasyParcel\ShipmentBuilder;
use PhpEasyParcel\Response;

// Replace with your actual API key
$apiKey = 'your-api-key';
$country = 'my'; // 'my' for Malaysia, 'sg' for Singapore, etc.

// Initialize EasyParcel client
$easyparcel = new EasyParcel($apiKey, $country);

// Example 1: Check credit balance
try {
    echo "Checking credit balance...\n";
    $balanceResponse = $easyparcel->checkBalance();
    $balance = new Response($balanceResponse);
    
    if ($balance->isSuccessful()) {
        echo "Credit Balance: " . $balance->getResult() . "\n\n";
    } else {
        echo "Error: " . $balance->getErrorMessage() . "\n\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n\n";
}

// Example 2: Get shipping rates
try {
    echo "Getting shipping rates...\n";
    
    // Create shipment using ShipmentBuilder
    $shipment = ShipmentBuilder::create()
        ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
        ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
        ->withDimensions(1.0, 10, 10, 10)
        ->build();
    
    $ratesResponse = $easyparcel->getRates($shipment);
    $rates = new Response($ratesResponse);
    
    if ($rates->isSuccessful()) {
        $availableRates = $rates->getRates();
        echo "Available rates:\n";
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

// Example 3: Submit an order (uncomment to test)
/*
try {
    echo "Submitting an order...\n";
    
    // Assuming you got a service_id from the rates response
    $serviceId = 'EP-MY0003'; // Replace with an actual service ID
    
    $order = ShipmentBuilder::create()
        ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
        ->fromCompany('ABC Company')
        ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
        ->toEmail('jane@example.com')
        ->withDimensions(1.0, 10, 10, 10)
        ->withContent('Books')
        ->withServiceId($serviceId)
        ->withCollectionDate(date('Y-m-d', strtotime('+1 day')))
        ->withSmsNotification(true)
        ->build();
    
    $orderResponse = $easyparcel->submitOrder($order);
    $orderResult = new Response($orderResponse);
    
    if ($orderResult->isSuccessful()) {
        $orderDetails = $orderResult->getOrderDetails();
        echo "Order submitted successfully!\n";
        echo "Order Number: " . $orderResult->getOrderNumber() . "\n";
        echo "Status: " . $orderResult->getParcelStatus() . "\n";
        echo "Shipment Cost: " . $orderResult->getShipmentCost() . "\n\n";
    } else {
        echo "Error: " . $orderResult->getErrorMessage() . "\n\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n\n";
}
*/

// Example 4: Pay for an order (uncomment to test)
/*
try {
    echo "Paying for an order...\n";
    
    // Replace with an actual order number from a previous submit order response
    $orderNo = 'EPC-123456789';
    
    $payResponse = $easyparcel->payOrder($orderNo);
    $payResult = new Response($payResponse);
    
    if ($payResult->isSuccessful()) {
        echo "Payment successful!\n";
        echo "Order Number: " . $payResult->getOrderNumber() . "\n";
        echo "Status: " . $payResult->getParcelStatus() . "\n";
        echo "Tracking Number: " . $payResult->getTrackingNumber() . "\n\n";
    } else {
        echo "Error: " . $payResult->getErrorMessage() . "\n\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n\n";
}
*/
