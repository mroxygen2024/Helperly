<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | public/ purpose
 |--------------------------------------------------------------------------
 | Front controller: all requests enter here and are routed to controllers.
 */

$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($autoloadPath)) {
    require_once $autoloadPath;
}

// Core app helpers are required even when Composer is not installed yet.
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Listing.php';
require_once dirname(__DIR__) . '/models/ServantProfile.php';
require_once dirname(__DIR__) . '/models/EmployerProfile.php';
require_once dirname(__DIR__) . '/models/HireRequest.php';
require_once dirname(__DIR__) . '/controllers/AuthController.php';
require_once dirname(__DIR__) . '/controllers/MarketplaceController.php';
require_once dirname(__DIR__) . '/controllers/ProfileController.php';
require_once dirname(__DIR__) . '/controllers/HireRequestController.php';

startSecureSession();

$authController = new AuthController();
$marketplaceController = new MarketplaceController();
$profileController = new ProfileController();
$hireRequestController = new HireRequestController();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

/**
 * Map legacy pretty URLs to simple query pages.
 */
$pathToPage = [
    '/' => 'dashboard',
    '/login' => 'login',
    '/register' => 'register',
    '/dashboard' => 'dashboard',
    '/profiles' => 'profiles',
    '/listings' => 'listings',
];

if ($method === 'GET' && str_starts_with($path, '/assets/')) {
    $requested = realpath(dirname(__DIR__) . $path);
    $assetsRoot = realpath(dirname(__DIR__) . '/assets');

    if (!$requested || !$assetsRoot || !str_starts_with($requested, $assetsRoot) || !is_file($requested)) {
        http_response_code(404);
        exit;
    }

    $extension = strtolower(pathinfo($requested, PATHINFO_EXTENSION));
    $knownMimeTypes = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];

    $mimeType = $knownMimeTypes[$extension] ?? (mime_content_type($requested) ?: 'application/octet-stream');
    header('Content-Type: ' . $mimeType);
    readfile($requested);
    exit;
}

try {
    if ($method === 'GET') {
        $requestedPage = sanitizeInput($_GET['page'] ?? '');

        if ($requestedPage === '' && isset($pathToPage[$path])) {
            $requestedPage = $pathToPage[$path];
        }

        if ($requestedPage === '') {
            $requestedPage = authUser() ? 'dashboard' : 'login';
        }

        switch ($requestedPage) {
            case 'login':
                $authController->showLogin();
                return;

            case 'register':
                $authController->showRegister();
                return;

            case 'dashboard':
                $user = authUser();

                if (!$user) {
                    redirect('/?page=login');
                }

                if (($user['role'] ?? '') === 'employer') {
                    $marketplaceController->employerDashboard();
                    return;
                }

                if (($user['role'] ?? '') === 'servant') {
                    $marketplaceController->servantDashboard();
                    return;
                }

                $marketplaceController->index();
                return;

            case 'profiles':
                $user = authUser();

                if (!$user) {
                    redirect('/?page=login');
                }

                if (($user['role'] ?? '') === 'employer') {
                    $profileController->showEmployerForm();
                    return;
                }

                if (($user['role'] ?? '') === 'servant') {
                    $profileController->showServantForm();
                    return;
                }

                http_response_code(403);
                renderView('errors/404', [
                    'title' => 'Forbidden',
                    'message' => 'You do not have permission to access profiles.',
                ]);
                return;

            case 'listings':
                $user = authUser();

                if ($user && ($user['role'] ?? '') === 'employer') {
                    $profileController->listServants($_GET);
                    return;
                }

                $marketplaceController->index();
                return;

            default:
                http_response_code(404);
                renderView('errors/404', [
                    'title' => 'Not Found',
                    'message' => 'The page you requested does not exist.',
                ]);
                return;
        }
    }

    if ($method === 'POST' && $path === '/login') {
        $authController->login($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/register') {
        $authController->register($_POST);
        return;
    }

    if ($method === 'GET' && $path === '/profile/servant') {
        $profileController->showServantForm();
        return;
    }

    if ($method === 'POST' && $path === '/profile/servant') {
        $profileController->saveServantProfile($_POST);
        return;
    }

    if ($method === 'GET' && $path === '/profile/employer') {
        $profileController->showEmployerForm();
        return;
    }

    if ($method === 'GET' && $path === '/servants') {
        $profileController->listServants($_GET);
        return;
    }

    if ($method === 'POST' && $path === '/hire-requests') {
        $hireRequestController->createRequest($_POST);
        return;
    }

    if ($method === 'GET' && $path === '/servant/requests') {
        $hireRequestController->showIncomingRequests();
        return;
    }

    if ($method === 'POST' && $path === '/servant/requests/status') {
        $hireRequestController->updateRequestStatus($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/profile/employer') {
        $profileController->saveEmployerProfile($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/logout') {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/');
        }
        $authController->logout();
        return;
    }

    http_response_code(404);
    renderView('errors/404', [
        'title' => 'Not Found',
        'message' => 'The requested route could not be matched.',
    ]);
} catch (Throwable $exception) {
    error_log('Application error: ' . $exception->getMessage());
    http_response_code(500);
    echo isProduction() ? 'An unexpected error occurred.' : escape($exception->getMessage());
}
