<?php

namespace PhpEasyParcel;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EasyParcel
{
    /**
     * @var string API key
     */
    private $apiKey;

    /**
     * @var string Country code (my, sg, etc.)
     */
    private $country;

    /**
     * @var Client HTTP client
     */
    private $client;

    /**
     * @var string Base URL for API calls
     */
    private $baseUrl;

    /**
     * @var array API endpoints
     */
    private $endpoints = [
        'checkBalance' => '?ac=EPCheckCreditBalance',
        'getRates' => '?ac=EPRateCheckingBulk',
        'submitOrder' => '?ac=EPSubmitOrderBulk',
        'payOrder' => '?ac=EPPayOrderBulk',
        'getParcelCategory' => '?ac=EPGetParcelCategory',
        'getCourierList' => '?ac=EPCourierList',
        'getCourierDropoff' => '?ac=EPCourierDropoff',
    ];

    /**
     * EasyParcel constructor.
     *
     * @param string|null $apiKey API key (if null, will try to load from .env)
     * @param string|null $country Country code (my, sg, etc.)
     * @param array $options Additional options
     */
    public function __construct(?string $apiKey = null, ?string $country = null, array $options = [])
    {
        // Try to load from environment if not provided
        if ($apiKey === null) {
            Config::loadEnv();
            $apiKey = Config::getApiKey();
            
            if ($apiKey === null) {
                throw new \InvalidArgumentException(
                    'API key is required. Either provide it directly or set it in the .env file.'
                );
            }
        }
        
        $this->apiKey = $apiKey;
        $this->country = strtolower($country ?? Config::getCountry());
        
        // Check if a custom base URL is provided in options
        if (isset($options['base_uri'])) {
            $this->baseUrl = rtrim($options['base_uri'], '/');
            unset($options['base_uri']);
        } else {
            // Default production URL, or sandbox if configured in .env
            $this->baseUrl = Config::isSandbox() 
                ? "https://demo.connect.easyparcel.{$this->country}"
                : "https://connect.easyparcel.{$this->country}";
        }
        
        $clientOptions = array_merge([
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false,
        ], $options);
        
        $this->client = new Client($clientOptions);
    }

    /**
     * Set environment to sandbox
     *
     * @return self
     */
    public function useSandbox(): self
    {
        $this->baseUrl = "https://demo.connect.easyparcel.{$this->country}";
        return $this;
    }

    /**
     * Set environment to production
     *
     * @return self
     */
    public function useProduction(): self
    {
        $this->baseUrl = "https://connect.easyparcel.{$this->country}";
        return $this;
    }

    /**
     * Check credit balance
     *
     * @return array Response data
     * @throws \Exception
     */
    public function checkBalance(): array
    {
        $params = [
            'api' => $this->apiKey,
        ];

        return $this->request('checkBalance', $params);
    }

    /**
     * Get shipping rates
     *
     * @param array $shipment Shipment details
     * @return array Response data
     * @throws \Exception
     */
    public function getRates(array $shipment): array
    {
        $params = [
            'api' => $this->apiKey,
            'bulk' => [$shipment],
        ];

        return $this->request('getRates', $params);
    }

    /**
     * Get shipping rates for multiple shipments
     *
     * @param array $shipments Array of shipment details
     * @return array Response data
     * @throws \Exception
     */
    public function getBulkRates(array $shipments): array
    {
        $params = [
            'api' => $this->apiKey,
            'bulk' => $shipments,
        ];

        return $this->request('getRates', $params);
    }

    /**
     * Submit order
     *
     * @param array $order Order details
     * @return array Response data
     * @throws \Exception
     */
    public function submitOrder(array $order): array
    {
        $params = [
            'api' => $this->apiKey,
            'bulk' => [$order],
        ];

        return $this->request('submitOrder', $params);
    }

    /**
     * Submit multiple orders
     *
     * @param array $orders Array of order details
     * @return array Response data
     * @throws \Exception
     */
    public function submitBulkOrders(array $orders): array
    {
        $params = [
            'api' => $this->apiKey,
            'bulk' => $orders,
        ];

        return $this->request('submitOrder', $params);
    }

    /**
     * Pay for order
     *
     * @param string $orderNo Order number
     * @return array Response data
     * @throws \Exception
     */
    public function payOrder(string $orderNo): array
    {
        $params = [
            'api' => $this->apiKey,
            'bulk' => [
                [
                    'order_no' => $orderNo,
                ]
            ],
        ];

        return $this->request('payOrder', $params);
    }

    /**
     * Pay for multiple orders
     *
     * @param array $orderNos Array of order numbers
     * @return array Response data
     * @throws \Exception
     */
    public function payBulkOrders(array $orderNos): array
    {
        $bulk = [];
        foreach ($orderNos as $orderNo) {
            $bulk[] = [
                'order_no' => $orderNo,
            ];
        }

        $params = [
            'api' => $this->apiKey,
            'bulk' => $bulk,
        ];

        return $this->request('payOrder', $params);
    }

    /**
     * Get parcel category list
     *
     * @return array Response data
     * @throws \Exception
     */
    public function getParcelCategoryList(): array
    {
        $params = [
            'api' => $this->apiKey,
        ];

        return $this->request('getParcelCategory', $params);
    }

    /**
     * Get courier list
     *
     * @return array Response data
     * @throws \Exception
     */
    public function getCourierList(): array
    {
        $params = [
            'api' => $this->apiKey,
        ];

        return $this->request('getCourierList', $params);
    }

    /**
     * Get courier dropoff points
     *
     * @param string $courierCode Courier code
     * @param string $postcode Postcode
     * @return array Response data
     * @throws \Exception
     */
    public function getCourierDropoff(string $courierCode, string $postcode): array
    {
        $params = [
            'api' => $this->apiKey,
            'courier_id' => $courierCode,
            'postcode' => $postcode,
        ];

        return $this->request('getCourierDropoff', $params);
    }

    /**
     * Make API request
     *
     * @param string $endpoint Endpoint key
     * @param array $params Request parameters
     * @return array Response data
     * @throws \Exception
     */
    private function request(string $endpoint, array $params): array
    {
        if (!isset($this->endpoints[$endpoint])) {
            throw new \InvalidArgumentException("Invalid endpoint: {$endpoint}");
        }

        $url = $this->baseUrl . $this->endpoints[$endpoint];

        try {
            $response = $this->client->post($url, [
                'form_params' => $params,
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }

            return $this->processResponse($data);
        } catch (GuzzleException $e) {
            throw new \Exception('API request failed: ' . $e->getMessage());
        }
    }

    /**
     * Process and normalize API response
     *
     * @param array $response Raw API response
     * @return array Normalized response with success/error information
     */
    public function processResponse(array $response): array
    {
        $result = [
            'success' => false,
            'data' => null,
            'error' => null,
            'raw' => $response
        ];
        
        // Check if the API call was successful
        if (isset($response['error_code']) && $response['error_code'] === '0') {
            $result['success'] = true;
            
            // Handle different response structures
            if (isset($response['result'])) {
                // For bulk operations that return success/fail arrays
                if (is_array($response['result']) && isset($response['result']['success'])) {
                    $result['data'] = $response['result']['success'];
                    
                    // If there are failures, include them in the error field
                    if (isset($response['result']['fail']) && !empty($response['result']['fail'])) {
                        $result['error'] = $response['result']['fail'];
                    }
                } 
                // For operations that return an array of results
                else if (is_array($response['result']) && !empty($response['result'])) {
                    $result['data'] = $response['result'];
                }
                // For operations that return a single result
                else {
                    $result['data'] = $response['result'];
                }
            } else {
                // For operations that don't return a result field
                $result['data'] = $response;
            }
        } else {
            // API call failed
            $result['success'] = false;
            $result['error'] = [
                'code' => $response['error_code'] ?? 'unknown',
                'message' => $response['error_remark'] ?? 'Unknown error'
            ];
        }
        
        return $result;
    }

    /**
     * Create a shipment array for rate checking
     *
     * @param array $params Shipment parameters
     * @return array Shipment array
     */
    public function createShipment(array $params): array
    {
        // Required parameters
        $required = ['pick_code', 'send_code', 'weight'];
        foreach ($required as $field) {
            if (!isset($params[$field])) {
                throw new \InvalidArgumentException("Missing required parameter: {$field}");
            }
        }

        // Default values
        $defaults = [
            'pick_country' => $this->country,
            'send_country' => $this->country,
            'width' => 0,
            'height' => 0,
            'length' => 0,
        ];

        return array_merge($defaults, $params);
    }

    /**
     * Create an order array for order submission
     *
     * @param array $params Order parameters
     * @return array Order array
     */
    public function createOrder(array $params): array
    {
        // Required parameters
        $required = [
            'pick_name', 'pick_contact', 'pick_addr1', 'pick_city', 'pick_code', 'pick_state', 'pick_country',
            'send_name', 'send_contact', 'send_addr1', 'send_city', 'send_code', 'send_state', 'send_country',
            'weight', 'service_id', 'content'
        ];
        
        foreach ($required as $field) {
            if (!isset($params[$field])) {
                throw new \InvalidArgumentException("Missing required parameter: {$field}");
            }
        }

        // Default values
        $defaults = [
            'pick_country' => $this->country,
            'send_country' => $this->country,
            'width' => 0,
            'height' => 0,
            'length' => 0,
            'collect_date' => date('Y-m-d'),
            'sms' => 0,
            'addon_whatsapp_tracking_enabled' => 0,
        ];

        return array_merge($defaults, $params);
    }

    /**
     * Get the base URL
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Make a direct API call with a custom endpoint
     *
     * @param string $action API action (without the '?ac=' prefix)
     * @param array $params Additional parameters (api key will be added automatically)
     * @return array Response data
     * @throws \Exception
     */
    public function call(string $action, array $params = []): array
    {
        // Add API key to parameters
        $params['api'] = $this->apiKey;
        
        // Construct the endpoint URL
        $url = $this->baseUrl . '?ac=' . $action;
        
        try {
            $response = $this->client->post($url, [
                'form_params' => $params,
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }

            return $this->processResponse($data);
        } catch (GuzzleException $e) {
            throw new \Exception('API request failed: ' . $e->getMessage());
        }
    }
}
