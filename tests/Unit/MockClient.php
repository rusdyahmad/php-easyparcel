<?php

namespace PhpEasyParcel\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class MockClient
{
    /**
     * @var array
     */
    private $container = [];

    /**
     * Create a mock client with predefined responses
     *
     * @param array $responses Array of response data
     * @return Client
     */
    public function createClient(array $responses): Client
    {
        $this->container = [];
        $history = Middleware::history($this->container);
        
        $mockResponses = [];
        foreach ($responses as $response) {
            $mockResponses[] = new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($response)
            );
        }
        
        $mock = new MockHandler($mockResponses);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        
        return new Client(['handler' => $handlerStack]);
    }

    /**
     * Get the request history
     *
     * @return array
     */
    public function getHistory(): array
    {
        return $this->container;
    }

    /**
     * Get the last request
     *
     * @return RequestInterface|null
     */
    public function getLastRequest(): ?RequestInterface
    {
        if (empty($this->container)) {
            return null;
        }
        
        return $this->container[count($this->container) - 1]['request'];
    }

    /**
     * Get the last request body as array
     *
     * @return array
     */
    public function getLastRequestBody(): array
    {
        $request = $this->getLastRequest();
        if (!$request) {
            return [];
        }
        
        parse_str((string) $request->getBody(), $params);
        return $params;
    }
}
