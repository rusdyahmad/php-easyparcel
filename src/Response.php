<?php

namespace PhpEasyParcel;

class Response
{
    /**
     * @var array Raw response data
     */
    private $data;

    /**
     * Response constructor.
     *
     * @param array $data Response data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Check if the response is successful
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return isset($this->data['error_code']) && $this->data['error_code'] === '0';
    }

    /**
     * Get error code
     *
     * @return string|null
     */
    public function getErrorCode(): ?string
    {
        return $this->data['error_code'] ?? null;
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->data['error_remark'] ?? null;
    }

    /**
     * Get result data
     *
     * @return mixed The result data, which could be an array, string, float, etc.
     */
    public function getResult()
    {
        return $this->data['result'] ?? null;
    }

    /**
     * Get result data as array
     *
     * @return array|null
     */
    public function getResultAsArray(): ?array
    {
        $result = $this->getResult();
        if (is_array($result)) {
            return $result;
        }
        return null;
    }

    /**
     * Get rates from result
     *
     * @return array
     */
    public function getRates(): array
    {
        if (!isset($this->data['result'][0]['rates'])) {
            return [];
        }

        return $this->data['result'][0]['rates'];
    }

    /**
     * Get order details from result
     *
     * @return array|null
     */
    public function getOrderDetails(): ?array
    {
        return $this->data['result'][0] ?? null;
    }

    /**
     * Get order number from result
     *
     * @return string|null
     */
    public function getOrderNumber(): ?string
    {
        return $this->data['result'][0]['order_number'] ?? null;
    }

    /**
     * Get parcel status from result
     *
     * @return string|null
     */
    public function getParcelStatus(): ?string
    {
        return $this->data['result'][0]['parcel_status'] ?? null;
    }

    /**
     * Get shipment cost from result
     *
     * @return float|null
     */
    public function getShipmentCost(): ?float
    {
        if (!isset($this->data['result'][0]['shipment_price'])) {
            return null;
        }

        return (float) $this->data['result'][0]['shipment_price'];
    }

    /**
     * Get tracking number from result
     *
     * @return string|null
     */
    public function getTrackingNumber(): ?string
    {
        return $this->data['result'][0]['tracking_number'] ?? null;
    }

    /**
     * Get raw response data
     *
     * @return array
     */
    public function getRawData(): array
    {
        return $this->data;
    }
}
