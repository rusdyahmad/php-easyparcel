<?php

namespace PhpEasyParcel\Tests\Unit;

use PhpEasyParcel\ShipmentBuilder;
use PHPUnit\Framework\TestCase;

class ShipmentBuilderTest extends TestCase
{
    /**
     * Test building a basic shipment
     */
    public function testBuildBasicShipment(): void
    {
        $shipment = ShipmentBuilder::create()
            ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
            ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
            ->withDimensions(1.0, 10, 10, 10)
            ->build();

        $this->assertIsArray($shipment);
        $this->assertEquals('John Doe', $shipment['pick_name']);
        $this->assertEquals('0123456789', $shipment['pick_contact']);
        $this->assertEquals('123 Main Street', $shipment['pick_addr1']);
        $this->assertEquals('Kuala Lumpur', $shipment['pick_city']);
        $this->assertEquals('50000', $shipment['pick_code']);
        $this->assertEquals('Kuala Lumpur', $shipment['pick_state']);
        $this->assertEquals('my', $shipment['pick_country']);

        $this->assertEquals('Jane Smith', $shipment['send_name']);
        $this->assertEquals('0123456789', $shipment['send_contact']);
        $this->assertEquals('456 Second Street', $shipment['send_addr1']);
        $this->assertEquals('Penang', $shipment['send_city']);
        $this->assertEquals('11950', $shipment['send_code']);
        $this->assertEquals('Penang', $shipment['send_state']);
        $this->assertEquals('my', $shipment['send_country']);

        $this->assertEquals(1.0, $shipment['weight']);
        $this->assertEquals(10, $shipment['width']);
        $this->assertEquals(10, $shipment['length']);
        $this->assertEquals(10, $shipment['height']);
    }

    /**
     * Test building a complete shipment with all options
     */
    public function testBuildCompleteShipment(): void
    {
        $shipment = ShipmentBuilder::create()
            ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
            ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
            ->withDimensions(1.0, 10, 10, 10)
            ->withServiceId('EP-MY0003')
            ->withCollectionDate('2025-03-22')
            ->withContent('Documents')
            ->build();

        $this->assertIsArray($shipment);
        
        // Check sender and receiver details
        $this->assertEquals('John Doe', $shipment['pick_name']);
        $this->assertEquals('Jane Smith', $shipment['send_name']);
        
        // Check dimensions
        $this->assertEquals(1.0, $shipment['weight']);
        $this->assertEquals(10, $shipment['width']);
        $this->assertEquals(10, $shipment['length']);
        $this->assertEquals(10, $shipment['height']);
        
        // Check additional options
        $this->assertEquals('EP-MY0003', $shipment['service_id']);
        $this->assertEquals('2025-03-22', $shipment['collect_date']);
        $this->assertEquals('Documents', $shipment['content']);
    }

    /**
     * Test setting multiple address lines
     */
    public function testMultipleAddressLines(): void
    {
        $shipment = ShipmentBuilder::create()
            ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
            ->fromAddress2('Floor 2')
            ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
            ->toAddress2('Apartment 3B')
            ->withDimensions(1.0, 10, 10, 10)
            ->build();

        $this->assertIsArray($shipment);
        $this->assertEquals('123 Main Street', $shipment['pick_addr1']);
        $this->assertEquals('Floor 2', $shipment['pick_addr2']);
        
        $this->assertEquals('456 Second Street', $shipment['send_addr1']);
        $this->assertEquals('Apartment 3B', $shipment['send_addr2']);
    }

    /**
     * Test setting dropoff and pickup points
     */
    public function testDropoffAndPickupPoints(): void
    {
        $shipment = ShipmentBuilder::create()
            ->from('John Doe', '0123456789', '123 Main Street', 'Kuala Lumpur', '50000', 'Kuala Lumpur', 'my')
            ->to('Jane Smith', '0123456789', '456 Second Street', 'Penang', '11950', 'Penang', 'my')
            ->withDimensions(1.0, 10, 10, 10)
            ->withDropoffPoint('DP123')
            ->withPickupPoint('PP456')
            ->build();

        $this->assertIsArray($shipment);
        $this->assertEquals('PP456', $shipment['pick_point']);
        $this->assertEquals('DP123', $shipment['send_point']);
    }
}
