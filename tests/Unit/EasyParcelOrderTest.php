<?php

namespace PhpEasyParcel\Tests\Unit;

use PhpEasyParcel\EasyParcel;
use PhpEasyParcel\ShipmentBuilder;
use PHPUnit\Framework\TestCase;

class EasyParcelOrderTest extends TestCase
{
    /**
     * @var MockClient
     */
    private $mockClient;

    /**
     * @var EasyParcel
     */
    private $easyparcel;

    protected function setUp(): void
    {
        $this->mockClient = new MockClient();
        $this->easyparcel = new EasyParcel('test-api-key', 'my');
    }

    /**
     * Test submitting an order
     */
    public function testSubmitOrder(): void
    {
        // Mock response data
        $responseData = [
            'error_code' => '0',
            'error_remark' => '',
            'result' => [
                [
                    'order_number' => 'EP123456789',
                    'parcel_status' => 'pending',
                    'parcel_number' => '',
                    'price' => '6.50',
                    'service_id' => 'EP-MY0003',
                    'courier_id' => 'poslaju',
                    'courier_name' => 'Pos Laju',
                    'pickup_date' => '2025-03-22',
                ]
            ],
        ];

        // Create mock client and inject it into the EasyParcel instance
        $client = $this->mockClient->createClient([$responseData]);
        $this->injectMockClient($client);

        // Create a shipment using ShipmentBuilder
        $shipment = ShipmentBuilder::create()
            ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
            ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
            ->withDimensions(1.0, 10, 10, 10)
            ->withServiceId('EP-MY0003')
            ->withCollectionDate('2025-03-22')
            ->withContent('Documents')
            ->build();

        // Submit order
        $response = $this->easyparcel->submitOrder($shipment);

        // Verify the request
        $requestBody = $this->mockClient->getLastRequestBody();
        $this->assertEquals('test-api-key', $requestBody['api']);
        $this->assertArrayHasKey('bulk', $requestBody);
        
        // Verify the response
        $this->assertEquals('0', $response['error_code']);
        $this->assertEquals('EP123456789', $response['result'][0]['order_number']);
        $this->assertEquals('pending', $response['result'][0]['parcel_status']);
        $this->assertEquals('EP-MY0003', $response['result'][0]['service_id']);
    }

    /**
     * Test paying for an order
     */
    public function testPayOrder(): void
    {
        // Mock response data
        $responseData = [
            'error_code' => '0',
            'error_remark' => '',
            'result' => [
                [
                    'order_number' => 'EP123456789',
                    'parcel_status' => 'paid',
                    'parcel_number' => 'PL123456789MY',
                    'price' => '6.50',
                    'service_id' => 'EP-MY0003',
                    'courier_id' => 'poslaju',
                    'courier_name' => 'Pos Laju',
                    'pickup_date' => '2025-03-22',
                    'awb_url' => 'https://easyparcel.com/awb/EP123456789.pdf',
                ]
            ],
        ];

        // Create mock client and inject it into the EasyParcel instance
        $client = $this->mockClient->createClient([$responseData]);
        $this->injectMockClient($client);

        // Pay for order
        $response = $this->easyparcel->payOrder('EP123456789');

        // Verify the request
        $requestBody = $this->mockClient->getLastRequestBody();
        $this->assertEquals('test-api-key', $requestBody['api']);
        
        // Verify the response
        $this->assertEquals('0', $response['error_code']);
        $this->assertEquals('EP123456789', $response['result'][0]['order_number']);
        $this->assertEquals('paid', $response['result'][0]['parcel_status']);
        $this->assertEquals('PL123456789MY', $response['result'][0]['parcel_number']);
        $this->assertEquals('https://easyparcel.com/awb/EP123456789.pdf', $response['result'][0]['awb_url']);
    }

    /**
     * Test submitting bulk orders
     */
    public function testSubmitBulkOrders(): void
    {
        // Mock response data
        $responseData = [
            'error_code' => '0',
            'error_remark' => '',
            'result' => [
                [
                    'order_number' => 'EP123456789',
                    'parcel_status' => 'pending',
                    'service_id' => 'EP-MY0003',
                ],
                [
                    'order_number' => 'EP987654321',
                    'parcel_status' => 'pending',
                    'service_id' => 'EP-MY0004',
                ],
            ],
        ];

        // Create mock client and inject it into the EasyParcel instance
        $client = $this->mockClient->createClient([$responseData]);
        $this->injectMockClient($client);

        // Create multiple shipments
        $shipment1 = ShipmentBuilder::create()
            ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
            ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
            ->withDimensions(1.0, 10, 10, 10)
            ->withServiceId('EP-MY0003')
            ->withCollectionDate('2025-03-22')
            ->build();

        $shipment2 = ShipmentBuilder::create()
            ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
            ->to('Bob Johnson', '0123456789', '789 Third Street', 'Johor Bahru', '80000', 'Johor', 'my')
            ->withDimensions(2.0, 20, 20, 20)
            ->withServiceId('EP-MY0004')
            ->withCollectionDate('2025-03-22')
            ->build();

        // Submit bulk orders
        $response = $this->easyparcel->submitBulkOrders([$shipment1, $shipment2]);

        // Verify the request
        $requestBody = $this->mockClient->getLastRequestBody();
        $this->assertEquals('test-api-key', $requestBody['api']);
        $this->assertArrayHasKey('bulk', $requestBody);
        
        // Verify the response
        $this->assertEquals('0', $response['error_code']);
        $this->assertCount(2, $response['result']);
        $this->assertEquals('EP123456789', $response['result'][0]['order_number']);
        $this->assertEquals('EP987654321', $response['result'][1]['order_number']);
    }

    /**
     * Inject a mock client into the EasyParcel instance
     *
     * @param mixed $client
     * @return void
     */
    private function injectMockClient($client): void
    {
        $reflection = new \ReflectionClass($this->easyparcel);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->easyparcel, $client);
    }
}
