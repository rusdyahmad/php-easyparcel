<?php

namespace PhpEasyParcel;

/**
 * ShipmentBuilder - A fluent interface for building EasyParcel shipment data
 *
 * @package PhpEasyParcel
 */
class ShipmentBuilder
{
    /**
     * @var array Shipment data
     */
    private $data = [];

    /**
     * Create a new shipment builder
     *
     * @return ShipmentBuilder A new instance of ShipmentBuilder
     */
    public static function create(): ShipmentBuilder
    {
        return new self();
    }

    /**
     * Set pickup details
     *
     * @param string $name Sender name
     * @param string $contact Sender contact number
     * @param string $address1 Sender address line 1
     * @param string $city Sender city
     * @param string $postcode Sender postcode
     * @param string $state Sender state
     * @param string $country Sender country code
     * @return ShipmentBuilder
     */
    public function from(
        string $name,
        string $contact,
        string $address1,
        string $city,
        string $postcode,
        string $state,
        string $country
    ): ShipmentBuilder {
        $this->data['pick_name'] = $name;
        $this->data['pick_contact'] = $contact;
        $this->data['pick_addr1'] = $address1;
        $this->data['pick_city'] = $city;
        $this->data['pick_code'] = $postcode;
        $this->data['pick_state'] = $state;
        $this->data['pick_country'] = strtolower($country);
        
        return $this;
    }

    /**
     * Set sender company name
     *
     * @param string $company Company name
     * @return ShipmentBuilder
     */
    public function fromCompany(string $company): ShipmentBuilder
    {
        $this->data['pick_company'] = $company;
        return $this;
    }

    /**
     * Set sender address line 2
     *
     * @param string $address2 Address line 2
     * @return ShipmentBuilder
     */
    public function fromAddress2(string $address2): ShipmentBuilder
    {
        $this->data['pick_addr2'] = $address2;
        return $this;
    }

    /**
     * Set sender mobile number
     *
     * @param string $mobile Mobile number
     * @return ShipmentBuilder
     */
    public function fromMobile(string $mobile): ShipmentBuilder
    {
        $this->data['pick_mobile'] = $mobile;
        return $this;
    }

    /**
     * Set receiver details
     *
     * @param string $name Receiver name
     * @param string $contact Receiver contact number
     * @param string $address1 Receiver address line 1
     * @param string $city Receiver city
     * @param string $postcode Receiver postcode
     * @param string $state Receiver state
     * @param string $country Receiver country code
     * @return ShipmentBuilder
     */
    public function to(
        string $name,
        string $contact,
        string $address1,
        string $city,
        string $postcode,
        string $state,
        string $country
    ): ShipmentBuilder {
        $this->data['send_name'] = $name;
        $this->data['send_contact'] = $contact;
        $this->data['send_addr1'] = $address1;
        $this->data['send_city'] = $city;
        $this->data['send_code'] = $postcode;
        $this->data['send_state'] = $state;
        $this->data['send_country'] = strtolower($country);
        
        return $this;
    }

    /**
     * Set receiver company name
     *
     * @param string $company Company name
     * @return ShipmentBuilder
     */
    public function toCompany(string $company): ShipmentBuilder
    {
        $this->data['send_company'] = $company;
        return $this;
    }

    /**
     * Set receiver address line 2
     *
     * @param string $address2 Address line 2
     * @return ShipmentBuilder
     */
    public function toAddress2(string $address2): ShipmentBuilder
    {
        $this->data['send_addr2'] = $address2;
        return $this;
    }

    /**
     * Set receiver mobile number
     *
     * @param string $mobile Mobile number
     * @return ShipmentBuilder
     */
    public function toMobile(string $mobile): ShipmentBuilder
    {
        $this->data['send_mobile'] = $mobile;
        return $this;
    }

    /**
     * Set receiver email
     *
     * @param string $email Email address
     * @return ShipmentBuilder
     */
    public function toEmail(string $email): ShipmentBuilder
    {
        $this->data['send_email'] = $email;
        return $this;
    }

    /**
     * Set parcel dimensions
     *
     * @param float $weight Weight in kg
     * @param float $width Width in cm
     * @param float $length Length in cm
     * @param float $height Height in cm
     * @return ShipmentBuilder
     */
    public function withDimensions(float $weight, float $width = 0, float $length = 0, float $height = 0): ShipmentBuilder
    {
        $this->data['weight'] = $weight;
        $this->data['width'] = $width;
        $this->data['length'] = $length;
        $this->data['height'] = $height;
        
        return $this;
    }

    /**
     * Set parcel content
     *
     * @param string $content Content description
     * @param float $value Content value
     * @return ShipmentBuilder
     */
    public function withContent(string $content, float $value = 0): ShipmentBuilder
    {
        $this->data['content'] = $content;
        $this->data['value'] = $value;
        
        return $this;
    }

    /**
     * Set service ID
     *
     * @param string $serviceId Service ID
     * @return ShipmentBuilder
     */
    public function withServiceId(string $serviceId): ShipmentBuilder
    {
        $this->data['service_id'] = $serviceId;
        return $this;
    }

    /**
     * Set collection date
     *
     * @param string $date Collection date (YYYY-MM-DD)
     * @return ShipmentBuilder
     */
    public function withCollectionDate(string $date): ShipmentBuilder
    {
        $this->data['collect_date'] = $date;
        return $this;
    }

    /**
     * Set reference number
     *
     * @param string $reference Reference number for the shipment
     * @return ShipmentBuilder
     */
    public function withReference(string $reference): ShipmentBuilder
    {
        $this->data['reference_number'] = $reference;
        return $this;
    }

    /**
     * Enable insurance addon
     *
     * @param bool $enabled Whether to enable insurance
     * @return ShipmentBuilder
     */
    public function withInsurance(bool $enabled = true): ShipmentBuilder
    {
        $this->data['addon_insurance_enabled'] = $enabled ? 1 : 0;
        return $this;
    }

    /**
     * Enable SMS notification
     *
     * @param bool $enabled Whether to enable SMS notification
     * @return ShipmentBuilder
     */
    public function withSmsNotification(bool $enabled = true): ShipmentBuilder
    {
        $this->data['sms'] = $enabled ? 1 : 0;
        return $this;
    }

    /**
     * Enable WhatsApp tracking
     *
     * @param bool $enabled Whether to enable WhatsApp tracking
     * @return ShipmentBuilder
     */
    public function withWhatsAppTracking(bool $enabled = true): ShipmentBuilder
    {
        $this->data['addon_whatsapp_tracking_enabled'] = $enabled ? 1 : 0;
        return $this;
    }

    /**
     * Set parcel category ID
     *
     * @param string $categoryId Parcel category ID
     * @return ShipmentBuilder
     */
    public function withParcelCategory(string $categoryId): ShipmentBuilder
    {
        $this->data['parcel_category_id'] = $categoryId;
        return $this;
    }

    /**
     * Set pickup point
     *
     * @param string $point Pickup point
     * @return ShipmentBuilder
     */
    public function withPickupPoint(string $point): ShipmentBuilder
    {
        $this->data['pick_point'] = $point;
        return $this;
    }

    /**
     * Set dropoff point
     *
     * @param string $point Dropoff point
     * @return ShipmentBuilder
     */
    public function withDropoffPoint(string $point): ShipmentBuilder
    {
        $this->data['send_point'] = $point;
        return $this;
    }

    /**
     * Get the shipment data
     *
     * @return array
     */
    public function build(): array
    {
        return $this->data;
    }
}
