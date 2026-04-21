<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | config/ purpose
 |--------------------------------------------------------------------------
 | Reusable utility functions to keep controllers and views small and clean.
 */

function escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function sanitizeInput(?string $value): string
{
    return trim((string) $value);
}

function sanitizeEmail(?string $value): string
{
    return filter_var(trim((string) $value), FILTER_SANITIZE_EMAIL) ?: '';
}

function sanitizePhone(?string $value): string
{
    $phone = trim((string) $value);

    // Keep only digits and a possible leading plus sign for normalized storage.
    $phone = preg_replace('/[^\d+]/', '', $phone) ?? '';
    $phone = preg_replace('/(?!^)\+/', '', $phone) ?? '';

    return $phone;
}

function validateRequired(string $value): bool
{
    return $value !== '';
}

function validateEmail(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone(string $phone): bool
{
    // Accept optional leading '+' followed by 8-15 digits.
    return (bool) preg_match('/^\+?\d{8,15}$/', $phone);
}

function validatePassword(string $password): bool
{
    // Minimum 8 chars, includes letters and digits.
    return (bool) preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password);
}

function validateRole(string $role): bool
{
    return in_array($role, ['servant', 'employer'], true);
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function setFlash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function getFlash(string $key): ?string
{
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

function rememberOldInput(array $input): void
{
    $_SESSION['old_input'] = $input;
}

function old(string $key, string $fallback = ''): string
{
    return (string) ($_SESSION['old_input'][$key] ?? $fallback);
}

function clearOldInput(): void
{
    unset($_SESSION['old_input']);
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    return is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function authUser(): ?array
{
    return $_SESSION['auth_user'] ?? null;
}

function requireAuth(): void
{
    if (!authUser()) {
        setFlash('error', 'Please login first.');
        redirect('/login');
    }
}

function requireRole(string $role): void
{
    requireAuth();

    $currentRole = (string) ($_SESSION['role'] ?? ($_SESSION['auth_user']['role'] ?? ''));
    if ($currentRole !== $role) {
        http_response_code(403);
        setFlash('error', 'You do not have permission to access that page.');
        redirect('/');
    }
}

function renderView(string $view, array $data = []): void
{
    $basePath = dirname(__DIR__);
    $viewFile = $basePath . '/views/' . $view . '.php';

    if (!file_exists($viewFile)) {
        http_response_code(500);
        echo 'View not found.';
        return;
    }

    extract($data, EXTR_SKIP);

    include $basePath . '/views/layouts/header.php';
    include $viewFile;
    include $basePath . '/views/layouts/footer.php';
}
