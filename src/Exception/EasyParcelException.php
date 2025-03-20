<?php

namespace PhpEasyParcel\Exception;

class EasyParcelException extends \Exception
{
    /**
     * @var string|null Error code from API
     */
    private $errorCode;

    /**
     * @var array|null Raw response data
     */
    private $responseData;

    /**
     * EasyParcelException constructor.
     *
     * @param string $message Error message
     * @param string|null $errorCode Error code
     * @param array|null $responseData Raw response data
     * @param int $code Exception code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        ?string $errorCode = null,
        ?array $responseData = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->responseData = $responseData;
    }

    /**
     * Get error code from API
     *
     * @return string|null
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Get raw response data
     *
     * @return array|null
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }
}
