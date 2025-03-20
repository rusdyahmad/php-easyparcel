<?php

namespace PhpEasyParcel\Tests\Unit;

use PhpEasyParcel\EasyParcel;
use PhpEasyParcel\ShipmentBuilder;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class EasyParcelRatesTest extends TestCase
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
     * Test getting shipping rates
     */
    public function testGetRates(): void
    {
        // Mock response data
        $responseData = [
            'error_code' => '0',
            'error_remark' => '',
            'result' => [
                [
                    'rates' => [
                        [
                            'service_id' => 'EP-MY0003',
                            'service_name' => 'Pos Laju',
                            'service_type' => 'document',
                            'price' => '6.50',
                            'currency' => 'MYR',
                            'dropoff_point' => null,
                            'pickup_point' => null,
                        ],
                        [
                            'service_id' => 'EP-MY0004',
                            'service_name' => 'DHL',
                            'service_type' => 'document',
                            'price' => '10.00',
                            'currency' => 'MYR',
                            'dropoff_point' => null,
                            'pickup_point' => null,
                        ],
                    ],
                ],
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
            ->build();

        // Get rates
        $response = $this->easyparcel->getRates($shipment);

        // Verify the request
        $requestBody = $this->mockClient->getLastRequestBody();
        $this->assertEquals('test-api-key', $requestBody['api']);
        $this->assertArrayHasKey('bulk', $requestBody);
        
        // Verify the response
        $this->assertEquals('0', $response['error_code']);
        $this->assertCount(2, $response['result'][0]['rates']);
        $this->assertEquals('EP-MY0003', $response['result'][0]['rates'][0]['service_id']);
        $this->assertEquals('Pos Laju', $response['result'][0]['rates'][0]['service_name']);
        $this->assertEquals('6.50', $response['result'][0]['rates'][0]['price']);
    }

    /**
     * Test getting bulk rates
     */
    public function testGetBulkRates(): void
    {
        // Mock response data
        $responseData = [
            'error_code' => '0',
            'error_remark' => '',
            'result' => [
                [
                    'rates' => [
                        [
                            'service_id' => 'EP-MY0003',
                            'service_name' => 'Pos Laju',
                            'service_type' => 'document',
                            'price' => '6.50',
                            'currency' => 'MYR',
                        ],
                    ],
                ],
                [
                    'rates' => [
                        [
                            'service_id' => 'EP-MY0004',
                            'service_name' => 'DHL',
                            'service_type' => 'document',
                            'price' => '12.00',
                            'currency' => 'MYR',
                        ],
                    ],
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
            ->build();

        $shipment2 = ShipmentBuilder::create()
            ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
            ->to('Bob Johnson', '0123456789', '789 Third Street', 'Johor Bahru', '80000', 'Johor', 'my')
            ->withDimensions(2.0, 20, 20, 20)
            ->build();

        // Get bulk rates
        $response = $this->easyparcel->getBulkRates([$shipment1, $shipment2]);

        // Verify the request
        $requestBody = $this->mockClient->getLastRequestBody();
        $this->assertEquals('test-api-key', $requestBody['api']);
        $this->assertArrayHasKey('bulk', $requestBody);
        
        // Verify the response
        $this->assertEquals('0', $response['error_code']);
        $this->assertCount(2, $response['result']);
        $this->assertEquals('EP-MY0003', $response['result'][0]['rates'][0]['service_id']);
        $this->assertEquals('EP-MY0004', $response['result'][1]['rates'][0]['service_id']);
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
