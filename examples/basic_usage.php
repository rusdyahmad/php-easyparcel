<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpEasyParcel\EasyParcel;
use PhpEasyParcel\ShipmentBuilder;
use PhpEasyParcel\Response;

// Load API key from .env file or specify directly
// If using .env, make sure to copy .env.example to .env and set your API key
$apiKey = 'your-api-key'; // Replace with your actual API key
$country = 'my'; // 'my' for Malaysia, 'sg' for Singapore, etc.

// Initialize EasyParcel client (defaults to production)
$easyparcel = new EasyParcel($apiKey, $country);

// To use sandbox environment instead, uncomment the following line:
// $easyparcel->useSandbox();

echo "Using environment: " . $easyparcel->getBaseUrl() . "\n\n";

// Example 1: Check credit balance
try {
    echo "Checking credit balance...\n";
    $balanceResponse = $easyparcel->checkBalance();
    $balance = new Response($balanceResponse);
    
    if ($balance->isSuccessful()) {
        echo "Credit Balance: " . $balance->getResult() . "\n\n";
    } else {
        echo "Error: " . $balance->getErrorMessage() . "\n";
        echo "Error code: " . $balance->getErrorCode() . "\n\n";
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
        ->withDimensions(1.0, 10, 10, 10) // 1kg, 10x10x10 cm
        ->build();
    
    $ratesResponse = $easyparcel->getRates($shipment);
    $rates = new Response($ratesResponse);
    
    if ($rates->isSuccessful()) {
        $availableRates = $rates->getRates();
        echo "Available rates:\n";
        foreach ($availableRates as $rate) {
            echo "- " . $rate['service_name'] . ": " . $rate['price'] . " " . ($rate['currency'] ?? 'MYR') . "\n";
            echo "  Service ID: " . $rate['service_id'] . "\n";
        }
        echo "\n";
    } else {
        echo "Error: " . $rates->getErrorMessage() . "\n";
        echo "Error code: " . $rates->getErrorCode() . "\n\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n\n";
}

// Example 3: Submit an order (uncomment to test)
/*
try {
    echo "Submitting an order...\n";
    
    // Assuming you got a service_id from the rates response
    $serviceId = 'EP-CS0XXX'; // Replace with an actual service ID from rates response
    
    // Method 1: Using ShipmentBuilder with additional order details
    $order = ShipmentBuilder::create()
        ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
        ->fromCompany('ABC Company')
        ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
        ->toEmail('jane@example.com')
        ->withDimensions(1.0, 10, 10, 10)
        ->withContent('Books')
        ->withValue(50.00) // Declared value of the parcel
        ->withServiceId($serviceId)
        ->withCollectionDate(date('Y-m-d', strtotime('+1 day')))
        ->withReference('ORDER-' . time()) // Your reference number
        ->build();
    
    // Method 2: Using createOrder method (alternative to ShipmentBuilder)
    // $order = $easyparcel->createOrder([
    //     'weight' => 1.0,
    //     'width' => 10,
    //     'length' => 10,
    //     'height' => 10,
    //     'content' => 'Books',
    //     'value' => 50.00,
    //     'service_id' => $serviceId,
    //     'pick_name' => 'John Doe',
    //     'pick_company' => 'ABC Company',
    //     'pick_contact' => '0123456789',
    //     'pick_addr1' => '123 Main Street',
    //     'pick_city' => 'Kuala Lumpur',
    //     'pick_state' => 'Kuala Lumpur',
    //     'pick_code' => '50000',
    //     'pick_country' => 'MY',
    //     'send_name' => 'Jane Smith',
    //     'send_contact' => '0123456789',
    //     'send_addr1' => '456 Second Street',
    //     'send_city' => 'Penang',
    //     'send_state' => 'Penang',
    //     'send_code' => '11950',
    //     'send_country' => 'MY',
    //     'send_email' => 'jane@example.com',
    //     'collect_date' => date('Y-m-d', strtotime('+1 day')),
    //     'reference' => 'ORDER-' . time(),
    // ]);
    
    $orderResponse = $easyparcel->submitOrder($order);
    $orderResult = new Response($orderResponse);
    
    if ($orderResult->isSuccessful()) {
        $orderDetails = $orderResult->getOrderDetails();
        echo "Order submitted successfully!\n";
        echo "Order Number: " . $orderResult->getOrderNumber() . "\n";
        echo "Status: " . $orderResult->getParcelStatus() . "\n";
        echo "Shipment Cost: " . $orderResult->getShipmentCost() . "\n\n";
    } else {
        echo "Error: " . $orderResult->getErrorMessage() . "\n";
        echo "Error code: " . $orderResult->getErrorCode() . "\n\n";
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
        echo "Error: " . $payResult->getErrorMessage() . "\n";
        echo "Error code: " . $payResult->getErrorCode() . "\n\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n\n";
}
*/
