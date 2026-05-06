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

function canonicalRoles(): array
{
    return ['parent', 'provider', 'administrator'];
}

function normalizeRole(string $role): string
{
    $normalized = strtolower(trim($role));

    return match ($normalized) {
        // Backward compatibility for old data and legacy code paths.
        'employer' => 'parent',
        'servant', 'service_provider' => 'provider',
        'admin' => 'administrator',
        default => $normalized,
    };
}

function validateRole(string $role): bool
{
    return in_array(normalizeRole($role), canonicalRoles(), true);
}

function appUrl(string $path = ''): string
{
    $baseUrl = rtrim(appConfig()['base_url'], '/');
    $normalizedPath = '/' . ltrim($path, '/');

    if ($normalizedPath === '/') {
        return $baseUrl;
    }

    return $baseUrl . $normalizedPath;
}

function userHasRole(array $user, string|array $requiredRoles): bool
{
    $currentRole = normalizeRole((string) ($user['role'] ?? ''));
    $roles = is_array($requiredRoles) ? $requiredRoles : [$requiredRoles];

    foreach ($roles as $role) {
        if ($currentRole === normalizeRole((string) $role)) {
            return true;
        }
    }

    return false;
}

function generateVerificationToken(): string
{
    return bin2hex(random_bytes(32));
}

function hashVerificationToken(string $token): string
{
    return hash('sha256', $token);
}

function redirect(string $path): never
{
    header('Location: ' . appUrl($path));
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

function base64UrlEncode(string $input): string
{
    return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
}

function base64UrlDecode(string $input): string
{
    $padding = strlen($input) % 4;
    if ($padding > 0) {
        $input .= str_repeat('=', 4 - $padding);
    }

    $decoded = base64_decode(strtr($input, '-_', '+/'), true);
    if ($decoded === false) {
        throw new RuntimeException('Invalid base64url token segment.');
    }

    return $decoded;
}

function jwtSecret(): string
{
    $secret = (string) (appConfig()['jwt_secret'] ?? '');
    if ($secret === '') {
        throw new RuntimeException('JWT_SECRET is missing. Configure it in your environment.');
    }

    return $secret;
}

function createJwt(array $payload, ?int $ttlSeconds = null): string
{
    $config = appConfig();
    $now = time();
    $ttl = $ttlSeconds ?? max(1, (int) ($config['jwt_ttl_seconds'] ?? 3600));

    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $claims = $payload;
    $claims['iat'] = $now;
    $claims['exp'] = $now + $ttl;

    $headerEncoded = base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR));
    $payloadEncoded = base64UrlEncode(json_encode($claims, JSON_THROW_ON_ERROR));
    $unsignedToken = $headerEncoded . '.' . $payloadEncoded;

    $signature = hash_hmac('sha256', $unsignedToken, jwtSecret(), true);
    $signatureEncoded = base64UrlEncode($signature);

    return $unsignedToken . '.' . $signatureEncoded;
}

function verifyJwt(string $token): array
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        throw new RuntimeException('Malformed token.');
    }

    [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

    $unsignedToken = $headerEncoded . '.' . $payloadEncoded;
    $expectedSignature = base64UrlEncode(hash_hmac('sha256', $unsignedToken, jwtSecret(), true));

    if (!hash_equals($expectedSignature, $signatureEncoded)) {
        throw new RuntimeException('Invalid token signature.');
    }

    $header = json_decode(base64UrlDecode($headerEncoded), true, 512, JSON_THROW_ON_ERROR);
    if (($header['alg'] ?? '') !== 'HS256') {
        throw new RuntimeException('Unsupported token algorithm.');
    }

    $claims = json_decode(base64UrlDecode($payloadEncoded), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($claims)) {
        throw new RuntimeException('Invalid token payload.');
    }

    $exp = (int) ($claims['exp'] ?? 0);
    if ($exp <= 0 || $exp < time()) {
        throw new RuntimeException('Token has expired.');
    }

    return $claims;
}

function jsonResponse(array $payload, int $statusCode = 200): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function requestJsonBody(): array
{
    $rawBody = file_get_contents('php://input');
    if ($rawBody === false || trim($rawBody) === '') {
        return [];
    }

    $decoded = json_decode($rawBody, true);
    return is_array($decoded) ? $decoded : [];
}

function bearerTokenFromRequest(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? '';
    if (!is_string($header) || $header === '') {
        return null;
    }

    if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches) !== 1) {
        return null;
    }

    $token = trim($matches[1]);
    return $token !== '' ? $token : null;
}

function requireJwtAuth(): array
{
    $token = bearerTokenFromRequest();
    if ($token === null) {
        jsonResponse(['error' => 'Missing bearer token.'], 401);
    }

    try {
        $claims = verifyJwt($token);
    } catch (Throwable $exception) {
        jsonResponse(['error' => 'Unauthorized: ' . $exception->getMessage()], 401);
    }

    return $claims;
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

function requireRole(string|array $requiredRoles): void
{
    requireAuth();

    $sessionUser = [
        'role' => (string) ($_SESSION['role'] ?? ($_SESSION['auth_user']['role'] ?? '')),
    ];

    if (!userHasRole($sessionUser, $requiredRoles)) {
        http_response_code(403);
        setFlash('error', 'You do not have permission to access that page.');
        redirect('/');
    }
}

function imageKitConfig(): array
{
    $config = appConfig();

    return [
        'public_key' => (string) ($config['imagekit_public_key'] ?? ''),
        'private_key' => (string) ($config['imagekit_private_key'] ?? ''),
        'url_endpoint' => (string) ($config['imagekit_url_endpoint'] ?? ''),
    ];
}

function imageKitClient(): \ImageKit\ImageKit
{
    $config = imageKitConfig();

    if ($config['public_key'] === '' || $config['private_key'] === '' || $config['url_endpoint'] === '') {
        throw new RuntimeException('ImageKit is not configured. Set IMAGEKIT_PUBLIC_KEY, IMAGEKIT_PRIVATE_KEY, and IMAGEKIT_URL_ENDPOINT.');
    }

    return new \ImageKit\ImageKit(
        $config['public_key'],
        $config['private_key'],
        $config['url_endpoint']
    );
}

function uploadImageToImageKit(string $tmpFilePath, string $fileName, array $options = []): array
{
    if (!is_file($tmpFilePath) || !is_readable($tmpFilePath)) {
        throw new RuntimeException('Temporary upload file is not readable.');
    }

    $binary = file_get_contents($tmpFilePath);
    if ($binary === false || $binary === '') {
        throw new RuntimeException('Could not read uploaded image content.');
    }

    $payload = [
        'file' => base64_encode($binary),
        'fileName' => $fileName,
        'useUniqueFileName' => true,
    ];

    if (isset($options['folder']) && is_string($options['folder']) && $options['folder'] !== '') {
        $payload['folder'] = $options['folder'];
    }

    if (isset($options['tags']) && is_array($options['tags'])) {
        $payload['tags'] = $options['tags'];
    }

    $response = imageKitClient()->uploadFile($payload);
    $responseData = is_array($response) ? $response : (is_object($response) ? get_object_vars($response) : []);
    $result = $responseData['result'] ?? $responseData;
    $resultData = is_array($result) ? $result : (is_object($result) ? get_object_vars($result) : []);

    if (!is_string($resultData['url'] ?? null) || trim($resultData['url']) === '') {
        throw new RuntimeException('ImageKit upload succeeded but no URL was returned.');
    }

    return $resultData;
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

function uploadToImageKit(string $filePath, string $fileName): string
{
    $result = uploadImageToImageKit($filePath, $fileName);
    return (string) $result['url'];
}
