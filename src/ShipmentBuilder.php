<?php

namespace PhpEasyParcel;

/**
 * ShipmentBuilder - A fluent interface for building EasyParcel shipment data
 *
 * @package PhpEasyParcel
 * @method static ShipmentBuilder create() Create a new shipment builder
 * @method ShipmentBuilder from(string $name, string $contact, string $address1, string $city, string $postcode, string $state, string $country) Set pickup details
 * @method ShipmentBuilder to(string $name, string $contact, string $address1, string $city, string $postcode, string $state, string $country) Set receiver details
 * @method ShipmentBuilder fromCompany(string $company) Set sender company name
 * @method ShipmentBuilder toEmail(string $email) Set receiver email
 * @method ShipmentBuilder withDimensions(float $weight, float $width, float $length, float $height) Set parcel dimensions
 * @method ShipmentBuilder withContent(string $content, float $value) Set parcel content
 * @method ShipmentBuilder withServiceId(string $serviceId) Set service ID
 * @method ShipmentBuilder withCollectionDate(string $date) Set collection date
 * @method ShipmentBuilder withReference(string $reference) Set reference number
 * @method array build() Get the shipment data
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
     * @return self
     */
    public static function create(): self
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
     * @return self
     */
    public function from(
        string $name,
        string $contact,
        string $address1,
        string $city,
        string $postcode,
        string $state,
        string $country
    ): self {
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
     * @return self
     */
    public function fromCompany(string $company): self
    {
        $this->data['pick_company'] = $company;
        return $this;
    }

    /**
     * Set sender address line 2
     *
     * @param string $address2 Address line 2
     * @return self
     */
    public function fromAddress2(string $address2): self
    {
        $this->data['pick_addr2'] = $address2;
        return $this;
    }

    /**
     * Set sender mobile number
     *
     * @param string $mobile Mobile number
     * @return self
     */
    public function fromMobile(string $mobile): self
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
     * @return self
     */
    public function to(
        string $name,
        string $contact,
        string $address1,
        string $city,
        string $postcode,
        string $state,
        string $country
    ): self {
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
     * @return self
     */
    public function toCompany(string $company): self
    {
        $this->data['send_company'] = $company;
        return $this;
    }

    /**
     * Set receiver address line 2
     *
     * @param string $address2 Address line 2
     * @return self
     */
    public function toAddress2(string $address2): self
    {
        $this->data['send_addr2'] = $address2;
        return $this;
    }

    /**
     * Set receiver mobile number
     *
     * @param string $mobile Mobile number
     * @return self
     */
    public function toMobile(string $mobile): self
    {
        $this->data['send_mobile'] = $mobile;
        return $this;
    }

    /**
     * Set receiver email
     *
     * @param string $email Email address
     * @return self
     */
    public function toEmail(string $email): self
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
     * @return self
     */
    public function withDimensions(float $weight, float $width = 0, float $length = 0, float $height = 0): self
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
     * @return self
     */
    public function withContent(string $content, float $value = 0): self
    {
        $this->data['content'] = $content;
        $this->data['value'] = $value;
        
        return $this;
    }

    /**
     * Set service ID
     *
     * @param string $serviceId Service ID
     * @return self
     */
    public function withServiceId(string $serviceId): self
    {
        $this->data['service_id'] = $serviceId;
        return $this;
    }

    /**
     * Set collection date
     *
     * @param string $date Collection date (YYYY-MM-DD)
     * @return self
     */
    public function withCollectionDate(string $date): self
    {
        $this->data['collect_date'] = $date;
        return $this;
    }

    /**
     * Set reference number
     *
     * @param string $reference Reference number for the shipment
     * @return self
     */
    public function withReference(string $reference): self
    {
        $this->data['reference_number'] = $reference;
        return $this;
    }

    /**
     * Enable insurance addon
     *
     * @param bool $enabled Whether to enable insurance
     * @return self
     */
    public function withInsurance(bool $enabled = true): self
    {
        $this->data['addon_insurance_enabled'] = $enabled ? 1 : 0;
        return $this;
    }

    /**
     * Enable SMS notification
     *
     * @param bool $enabled Whether to enable SMS notification
     * @return self
     */
    public function withSmsNotification(bool $enabled = true): self
    {
        $this->data['sms'] = $enabled ? 1 : 0;
        return $this;
    }

    /**
     * Enable WhatsApp tracking
     *
     * @param bool $enabled Whether to enable WhatsApp tracking
     * @return self
     */
    public function withWhatsAppTracking(bool $enabled = true): self
    {
        $this->data['addon_whatsapp_tracking_enabled'] = $enabled ? 1 : 0;
        return $this;
    }

    /**
     * Set parcel category ID
     *
     * @param string $categoryId Parcel category ID
     * @return self
     */
    public function withParcelCategory(string $categoryId): self
    {
        $this->data['parcel_category_id'] = $categoryId;
        return $this;
    }

    /**
     * Set pickup point
     *
     * @param string $point Pickup point
     * @return self
     */
    public function withPickupPoint(string $point): self
    {
        $this->data['pick_point'] = $point;
        return $this;
    }

    /**
     * Set dropoff point
     *
     * @param string $point Dropoff point
     * @return self
     */
    public function withDropoffPoint(string $point): self
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
