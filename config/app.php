<?php

declare(strict_types=1);

/**
 * config/app.php
 *
 * Centralized application configuration and bootstrap logic.
 * Follows 12-factor app principles by loading configuration from environment variables.
 */

if (!defined('APP_BOOTSTRAPPED')) {
    define('APP_BOOTSTRAPPED', true);

    date_default_timezone_set('UTC');

    /**
     * Load environment variables from a .env file into putenv(), $_ENV, and $_SERVER.
     */
    function loadEnvironmentFromFile(string $filePath): void
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            [$name, $value] = array_map('trim', $parts);
            
            // Remove quotes if present
            if (preg_match('/^["\'](.*)["\']$/', $value, $matches)) {
                $value = $matches[1];
            }

            // Only set if not already set (allow system env vars to override .env)
            if (getenv($name) === false) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    /**
     * Get a configuration value from the environment with validation and defaults.
     */
    function env(string $key, mixed $default = null, bool $required = false): mixed
    {
        $value = getenv($key);

        if ($value === false) {
            if ($required) {
                throw new RuntimeException("Missing required environment variable: {$key}");
            }
            return $default;
        }

        // Handle boolean strings
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }

    /**
     * Return the full application configuration array.
     */
    function appConfig(): array
    {
        static $config = null;

        if ($config !== null) {
            return $config;
        }

        $config = [
            'app_name' => (string) env('APP_NAME', 'Servant Marketplace'),
            'app_env' => (string) env('APP_ENV', 'development'),
            'app_debug' => (bool) env('APP_DEBUG', false),
            'base_url' => rtrim((string) env('APP_URL', 'http://localhost:8000'), '/'),
            'app_key' => (string) env('APP_KEY', ''),
            'mongodb_uri' => (string) env('MONGODB_URI', '', true),
            'mongodb_db' => (string) env('MONGODB_DB', 'servant_marketplace'),
            'redis_host' => (string) env('REDIS_HOST', '127.0.0.1'),
            'redis_port' => (int) env('REDIS_PORT', 6379),
            'redis_password' => (string) env('REDIS_PASSWORD', ''),
            'jwt_secret' => (string) env('JWT_SECRET', '', true),
            'jwt_ttl_seconds' => (int) env('JWT_TTL_SECONDS', 3600),
            'session_name' => (string) env('SESSION_NAME', 'servant_session'),
            'imagekit_public_key' => (string) env('IMAGEKIT_PUBLIC_KEY', ''),
            'imagekit_private_key' => (string) env('IMAGEKIT_PRIVATE_KEY', ''),
            'imagekit_url_endpoint' => rtrim((string) env('IMAGEKIT_URL_ENDPOINT', ''), '/'),
            'smtp_host' => (string) env('SMTP_HOST', ''),
            'smtp_port' => (int) env('SMTP_PORT', 587),
            'smtp_username' => (string) env('SMTP_USERNAME', ''),
            'smtp_password' => (string) env('SMTP_PASSWORD', ''),
            'smtp_encryption' => (string) env('SMTP_ENCRYPTION', 'tls'),
            'smtp_from_email' => (string) env('SMTP_FROM_EMAIL', 'no-reply@servant-marketplace.local'),
            'smtp_from_name' => (string) env('SMTP_FROM_NAME', 'Servant Marketplace'),
        ];

        return $config;
    }

    function appEnv(): string { return (string) env('APP_ENV', 'development'); }
    function isProduction(): bool { return appEnv() === 'production'; }

    function configureErrorReporting(): void
    {
        $debug = (bool) env('APP_DEBUG', !isProduction());
        error_reporting(E_ALL);
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('log_errors', '1');
    }

    function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = appConfig();
        $sessionName = $config['session_name'];

        session_name($sessionName);
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    // Bootstrap
    loadEnvironmentFromFile(dirname(__DIR__) . '/.env');
    configureErrorReporting();
}
