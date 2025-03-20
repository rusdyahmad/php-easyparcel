<?php

namespace PhpEasyParcel\Tests\Unit;

use PhpEasyParcel\EasyParcel;
use PhpEasyParcel\Response;
use PHPUnit\Framework\TestCase;

class EasyParcelUtilityTest extends TestCase
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
     * Test checking credit balance
     */
    public function testCheckBalance(): void
    {
        // Mock response data
        $responseData = [
            'error_code' => '0',
            'error_remark' => '',
            'result' => [
                'credit_balance' => '100.50',
                'currency' => 'MYR',
            ],
        ];

        // Create mock client and inject it into the EasyParcel instance
        $client = $this->mockClient->createClient([$responseData]);
        $this->injectMockClient($client);

        // Check balance
        $response = $this->easyparcel->checkBalance();

        // Verify the request
        $requestBody = $this->mockClient->getLastRequestBody();
        $this->assertEquals('test-api-key', $requestBody['api']);
        
        // Verify the response
        $this->assertEquals('0', $response['error_code']);
        $this->assertEquals('100.50', $response['result']['credit_balance']);
        $this->assertEquals('MYR', $response['result']['currency']);
    }

    /**
     * Test getting courier list
     */
    public function testGetCourierList(): void
    {
        // Mock response data
        $responseData = [
            'error_code' => '0',
            'error_remark' => '',
            'result' => [
                [
                    'courier_id' => 'poslaju',
                    'courier_name' => 'Pos Laju',
                    'courier_logo' => 'https://easyparcel.com/my/image/poslaju.png',
                ],
                [
                    'courier_id' => 'dhl',
                    'courier_name' => 'DHL',
                    'courier_logo' => 'https://easyparcel.com/my/image/dhl.png',
                ],
            ],
        ];

        // Create mock client and inject it into the EasyParcel instance
        $client = $this->mockClient->createClient([$responseData]);
        $this->injectMockClient($client);

        // Get courier list
        $response = $this->easyparcel->getCourierList();

        // Verify the request
        $requestBody = $this->mockClient->getLastRequestBody();
        $this->assertEquals('test-api-key', $requestBody['api']);
        
        // Verify the response
        $this->assertEquals('0', $response['error_code']);
        $this->assertCount(2, $response['result']);
        $this->assertEquals('poslaju', $response['result'][0]['courier_id']);
        $this->assertEquals('Pos Laju', $response['result'][0]['courier_name']);
    }

    /**
     * Test the Response class
     */
    public function testResponseClass(): void
    {
        $responseData = [
            'error_code' => '0',
            'error_remark' => '',
            'result' => [
                [
                    'order_number' => 'EP123456789',
                    'parcel_status' => 'pending',
                    'price' => '6.50',
                    'service_id' => 'EP-MY0003',
                    'rates' => [
                        [
                            'service_id' => 'EP-MY0003',
                            'service_name' => 'Pos Laju',
                            'price' => '6.50',
                        ],
                        [
                            'service_id' => 'EP-MY0004',
                            'service_name' => 'DHL',
                            'price' => '10.00',
                        ],
                    ],
                ],
            ],
        ];

        $response = new Response($responseData);

        // Test basic methods
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('0', $response->getErrorCode());
        $this->assertEquals('', $response->getErrorMessage());
        $this->assertEquals($responseData['result'], $response->getResult());

        // Test specific getters
        $this->assertEquals('EP123456789', $response->getOrderNumber());
        $this->assertEquals('pending', $response->getParcelStatus());
        $this->assertCount(2, $response->getRates());
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
