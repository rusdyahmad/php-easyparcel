<?php

namespace PhpEasyParcel;

use Dotenv\Dotenv;

class Config
{
    /**
     * Load environment variables from .env file
     *
     * @param string|null $path Path to the directory containing .env file
     * @return void
     */
    public static function loadEnv(?string $path = null): void
    {
        $path = $path ?: dirname(__DIR__);
        
        if (file_exists($path . '/.env')) {
            $dotenv = Dotenv::createImmutable($path);
            $dotenv->load();
        }
    }

    /**
     * Get the API key from environment variables
     *
     * @return string|null
     */
    public static function getApiKey(): ?string
    {
        return $_ENV['EASYPARCEL_API_KEY'] ?? null;
    }

    /**
     * Get the country code from environment variables
     *
     * @return string
     */
    public static function getCountry(): string
    {
        return $_ENV['EASYPARCEL_COUNTRY'] ?? 'my';
    }

    /**
     * Check if sandbox environment is enabled
     *
     * @return bool
     */
    public static function isSandbox(): bool
    {
        return ($_ENV['EASYPARCEL_ENV'] ?? 'production') === 'sandbox';
    }
}
