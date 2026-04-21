<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | config/ purpose
 |--------------------------------------------------------------------------
 | Centralized app configuration and bootstrap helpers live in this folder.
 */

if (!defined('APP_BOOTSTRAPPED')) {
    define('APP_BOOTSTRAPPED', true);

    date_default_timezone_set('UTC');

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
            $trimmed = trim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            $separatorPosition = strpos($trimmed, '=');
            if ($separatorPosition === false) {
                continue;
            }

            $name = trim(substr($trimmed, 0, $separatorPosition));
            $value = trim(substr($trimmed, $separatorPosition + 1));

            if ($name === '') {
                continue;
            }

            $length = strlen($value);
            if ($length >= 2) {
                $firstChar = $value[0];
                $lastChar = $value[$length - 1];

                if (($firstChar === '"' && $lastChar === '"') || ($firstChar === "'" && $lastChar === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            if (getenv($name) !== false) {
                continue;
            }

            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    function appEnv(): string
    {
        return getenv('APP_ENV') ?: 'development';
    }

    function isProduction(): bool
    {
        return appEnv() === 'production';
    }

    function appConfig(): array
    {
        return [
            'app_name' => getenv('APP_NAME') ?: 'Servant Marketplace',
            'base_url' => rtrim(getenv('BASE_URL') ?: 'http://localhost:8000', '/'),
            'mongodb_uri' => getenv('MONGODB_URI') ?: '',
            'mongodb_db' => getenv('MONGODB_DB') ?: 'servant_marketplace',
            'session_name' => getenv('SESSION_NAME') ?: 'servant_session',
            'jwt_secret' => getenv('JWT_SECRET') ?: '',
            'jwt_ttl_seconds' => (int) (getenv('JWT_TTL_SECONDS') ?: 3600),
        ];
    }

    function configureErrorReporting(): void
    {
        error_reporting(E_ALL);
        ini_set('display_errors', isProduction() ? '0' : '1');
        ini_set('log_errors', '1');
    }

    function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $sessionName = appConfig()['session_name'];

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

    loadEnvironmentFromFile(dirname(__DIR__) . '/.env');
    configureErrorReporting();
}
