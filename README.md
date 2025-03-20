# PHP EasyParcel

A PHP wrapper for the EasyParcel API.

## Installation

```bash
composer require rusdyahmad/php-easyparcel
```

## Requirements

- PHP 7.2 or higher
- Guzzle HTTP Client
- JSON extension

## Usage

### Initialization

There are multiple ways to initialize the EasyParcel client:

#### Method 1: Direct API Key

```php
use PhpEasyParcel\EasyParcel;

// Initialize with API key and country code
$easyparcel = new EasyParcel('your-api-key', 'my'); // 'my' for Malaysia, 'sg' for Singapore, etc.
```

#### Method 2: Using Environment Variables (.env)

Create a `.env` file in your project root (copy from `.env.example`):

```env
# EasyParcel API Keys
EASYPARCEL_API_KEY=your_api_key_here
EASYPARCEL_COUNTRY=my
EASYPARCEL_ENV=production  # or sandbox
```

Then initialize without parameters:

```php
use PhpEasyParcel\Config;
use PhpEasyParcel\EasyParcel;

// Load environment variables
Config::loadEnv();

// Initialize using environment variables
$easyparcel = new EasyParcel();
```

> **Note:** EasyParcel uses the same API key for both production and sandbox environments. The environment is determined by the base URL, not the API key.

### Check Credit Balance

```php
try {
    $response = $easyparcel->checkBalance();
    
    if (isset($response['error_code']) && $response['error_code'] === '0') {
        echo "Credit Balance: " . $response['result'];
    } else {
        echo "Error: " . $response['error_remark'];
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
```

### Get Shipping Rates

Using the direct method:

```php
try {
    $shipment = [
        'pick_code' => '50000',
        'pick_state' => 'Kuala Lumpur',
        'pick_country' => 'my',
        'send_code' => '11950',
        'send_state' => 'Penang',
        'send_country' => 'my',
        'weight' => 1.0,
        'width' => 10,
        'height' => 10,
        'length' => 10,
    ];
    
    $response = $easyparcel->getRates($shipment);
    
    if (isset($response['error_code']) && $response['error_code'] === '0') {
        $rates = $response['result'][0]['rates'];
        foreach ($rates as $rate) {
            echo $rate['service_name'] . ": " . $rate['price'] . "\n";
        }
    } else {
        echo "Error: " . $response['error_remark'];
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
```

Using the ShipmentBuilder:

```php
use PhpEasyParcel\ShipmentBuilder;

try {
    $shipment = ShipmentBuilder::create()
        ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
        ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
        ->withDimensions(1.0, 10, 10, 10)
        ->build();
    
    $response = $easyparcel->getRates($shipment);
    
    // Process response as above
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
```

### Submit Order

```php
use PhpEasyParcel\ShipmentBuilder;

try {
    $order = ShipmentBuilder::create()
        ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
        ->fromCompany('ABC Company')
        ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
        ->toEmail('jane@example.com')
        ->withDimensions(1.0, 10, 10, 10)
        ->withContent('Books')
        ->withServiceId('EP-MY0003') // Service ID from rates response
        ->withCollectionDate('2025-03-25')
        ->withSmsNotification(true)
        ->withWhatsAppTracking(true)
        ->build();
    
    $response = $easyparcel->submitOrder($order);
    
    if (isset($response['error_code']) && $response['error_code'] === '0') {
        $orderDetails = $response['result'][0];
        echo "Order Number: " . $orderDetails['order_number'] . "\n";
        echo "Status: " . $orderDetails['parcel_status'] . "\n";
    } else {
        echo "Error: " . $response['error_remark'];
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
```

### Pay for Order

```php
try {
    $orderNo = 'EPC-123456789'; // Order number from submit order response
    
    $response = $easyparcel->payOrder($orderNo);
    
    if (isset($response['error_code']) && $response['error_code'] === '0') {
        $paymentDetails = $response['result'][0];
        echo "Order Number: " . $paymentDetails['order_number'] . "\n";
        echo "Status: " . $paymentDetails['parcel_status'] . "\n";
        echo "Tracking Number: " . $paymentDetails['tracking_number'] . "\n";
    } else {
        echo "Error: " . $response['error_remark'];
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
```

### Using the Response Class

```php
use PhpEasyParcel\Response;

try {
    $shipment = ShipmentBuilder::create()
        ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
        ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
        ->withDimensions(1.0, 10, 10, 10)
        ->build();
    
    $rawResponse = $easyparcel->getRates($shipment);
    $response = new Response($rawResponse);
    
    if ($response->isSuccessful()) {
        $rates = $response->getRates();
        foreach ($rates as $rate) {
            echo $rate['service_name'] . ": " . $rate['price'] . "\n";
        }
    } else {
        echo "Error: " . $response->getErrorMessage();
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
```

## Available Methods

### EasyParcel Class

- `checkBalance()`: Check credit balance
- `getRates(array $shipment)`: Get shipping rates for a single shipment
- `getBulkRates(array $shipments)`: Get shipping rates for multiple shipments
- `submitOrder(array $order)`: Submit a single order
- `submitBulkOrders(array $orders)`: Submit multiple orders
- `payOrder(string $orderNo)`: Pay for a single order
- `payBulkOrders(array $orderNos)`: Pay for multiple orders
- `getParcelCategoryList()`: Get parcel category list
- `getCourierList()`: Get courier list
- `getCourierDropoff(string $courierCode, string $postcode)`: Get courier dropoff points
- `useSandbox()`: Switch to sandbox environment
- `useProduction()`: Switch to production environment
- `getBaseUrl()`: Get the current base URL

### ShipmentBuilder Class

- `from(string $name, string $contact, string $address1, string $city, string $postcode, string $state, string $country)`: Set pickup details
- `fromCompany(string $company)`: Set sender company name
- `fromAddress2(string $address2)`: Set sender address line 2
- `fromMobile(string $mobile)`: Set sender mobile number
- `to(string $name, string $contact, string $address1, string $city, string $postcode, string $state, string $country)`: Set receiver details
- `toCompany(string $company)`: Set receiver company name
- `toAddress2(string $address2)`: Set receiver address line 2
- `toMobile(string $mobile)`: Set receiver mobile number
- `toEmail(string $email)`: Set receiver email
- `withDimensions(float $weight, float $width, float $length, float $height)`: Set parcel dimensions
- `withContent(string $content, float $value)`: Set parcel content
- `withServiceId(string $serviceId)`: Set service ID
- `withCollectionDate(string $date)`: Set collection date
- `withInsurance(bool $enabled)`: Enable insurance addon
- `withSmsNotification(bool $enabled)`: Enable SMS notification
- `withWhatsAppTracking(bool $enabled)`: Enable WhatsApp tracking
- `withParcelCategory(string $categoryId)`: Set parcel category ID
- `withPickupPoint(string $point)`: Set pickup point
- `withDropoffPoint(string $point)`: Set dropoff point
- `build()`: Get the shipment data

### Response Class

- `isSuccessful()`: Check if the response is successful
- `getErrorCode()`: Get error code
- `getErrorMessage()`: Get error message
- `getResult()`: Get result data
- `getRates()`: Get rates from result
- `getOrderDetails()`: Get order details from result
- `getOrderNumber()`: Get order number from result
- `getParcelStatus()`: Get parcel status from result
- `getShipmentCost()`: Get shipment cost from result
- `getTrackingNumber()`: Get tracking number from result
- `getRawData()`: Get raw response data

## Switching Between Production and Sandbox Environments

EasyParcel provides both production and sandbox environments for testing. There are two ways to switch between these environments:

### Method 1: Using Constructor Options

```php
// Production environment (default)
$productionClient = new EasyParcel('your-api-key', 'my');

// Sandbox environment
$sandboxOptions = [
    'base_uri' => "https://demo.connect.easyparcel.my/"
];
$sandboxClient = new EasyParcel('your-api-key', 'my', $sandboxOptions);
```

### Method 2: Using Environment Setter Methods

```php
// Create a client (defaults to production)
$client = new EasyParcel('your-api-key', 'my');

// Switch to sandbox
$client->useSandbox();

// Switch back to production
$client->useProduction();

// Check current environment
echo $client->getBaseUrl();
```

Remember to use the same API key for both environments.

## License

MIT
