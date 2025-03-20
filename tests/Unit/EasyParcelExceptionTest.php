<?php

namespace PhpEasyParcel\Tests\Unit;

use PhpEasyParcel\Exception\EasyParcelException;
use PHPUnit\Framework\TestCase;

class EasyParcelExceptionTest extends TestCase
{
    /**
     * Test creating an exception with basic parameters
     */
    public function testBasicException(): void
    {
        $exception = new EasyParcelException('Error message');
        
        $this->assertEquals('Error message', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getErrorCode());
        $this->assertNull($exception->getResponseData());
    }

    /**
     * Test creating an exception with all parameters
     */
    public function testFullException(): void
    {
        $responseData = [
            'error_code' => '1001',
            'error_remark' => 'Invalid API key',
        ];
        
        $exception = new EasyParcelException(
            'API Error: Invalid API key',
            '1001',
            $responseData,
            400
        );
        
        $this->assertEquals('API Error: Invalid API key', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
        $this->assertEquals('1001', $exception->getErrorCode());
        $this->assertEquals($responseData, $exception->getResponseData());
    }

    /**
     * Test creating an exception with a previous exception
     */
    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous error');
        $exception = new EasyParcelException(
            'API Error',
            '1001',
            null,
            400,
            $previous
        );
        
        $this->assertEquals('API Error', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
        $this->assertEquals('1001', $exception->getErrorCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
