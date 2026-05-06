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
require_once dirname(__DIR__) . '/models/Job.php';
require_once dirname(__DIR__) . '/models/JobApplication.php';
require_once dirname(__DIR__) . '/models/Service.php';
require_once dirname(__DIR__) . '/models/Message.php';
require_once dirname(__DIR__) . '/models/Payment.php';
require_once dirname(__DIR__) . '/models/Review.php';
require_once dirname(__DIR__) . '/models/Notification.php';

require_once dirname(__DIR__) . '/controllers/AuthController.php';
require_once dirname(__DIR__) . '/controllers/MarketplaceController.php';
require_once dirname(__DIR__) . '/controllers/ProfileController.php';
require_once dirname(__DIR__) . '/controllers/HireRequestController.php';
require_once dirname(__DIR__) . '/controllers/JobController.php';
require_once dirname(__DIR__) . '/controllers/ServiceController.php';
require_once dirname(__DIR__) . '/controllers/MessageController.php';
require_once dirname(__DIR__) . '/controllers/PaymentController.php';
require_once dirname(__DIR__) . '/controllers/ReviewController.php';
require_once dirname(__DIR__) . '/controllers/AdminUserController.php';
require_once dirname(__DIR__) . '/controllers/AdminJobController.php';


startSecureSession();

// Lazy load controllers using an anonymous class to avoid redundant DB indexing on every page load
$ctrl = new class {
    private array $instances = [];
    public function __get(string $name) {
        $class = ucfirst($name);
        return $this->instances[$class] ??= new $class();
    }
};


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
    '/profile/account' => 'profile-account',
    '/profiles' => 'profiles',
    '/listings' => 'listings',
    '/servants' => 'listings',
    '/messages' => 'messages',
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
    if ($method === 'POST' && $path === '/api/login') {
        $ctrl->authController->loginApi(requestJsonBody());
        return;
    }

    if ($method === 'GET' && $path === '/api/me') {
        $claims = requireJwtAuth();
        $ctrl->authController->meApi($claims);
        return;
    }

    if ($method === 'GET' && $path === '/api/messages') {
        $ctrl->messageController->apiFetch($_GET);
        return;
    }

    if ($method === 'POST' && $path === '/login') {
        $ctrl->authController->login($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/register') {
        $ctrl->authController->register($_POST);
        return;
    }

    if ($method === 'GET' && $path === '/verify-email') {
        $ctrl->authController->verifyEmail($_GET);
        return;
    }

    if ($method === 'GET' && $path === '/forgot-password') {
        $ctrl->authController->showForgotPassword();
        return;
    }

    if ($method === 'POST' && $path === '/forgot-password') {
        $ctrl->authController->forgotPassword($_POST);
        return;
    }

    if ($method === 'GET' && $path === '/reset-password') {
        $ctrl->authController->showResetPassword($_GET);
        return;
    }

    if ($method === 'POST' && $path === '/reset-password') {
        $ctrl->authController->resetPassword($_POST);
        return;
    }

    if ($method === 'GET' && $path === '/services') {
        $ctrl->serviceController->showForm();
        return;
    }

    if ($method === 'POST' && $path === '/services') {
        $ctrl->serviceController->create($_POST);
        return;
    }


    if ($method === 'GET' && $path === '/profile/servant') {
        $ctrl->profileController->showServantForm();
        return;
    }

    if ($method === 'GET' && ($path === '/admin/verifications' || $path === '/admin/verified_user')) {
        $ctrl->profileController->showAdminVerifications();
        return;
    }

    if ($method === 'GET' && $path === '/profile/account') {
        $ctrl->profileController->showAccountForm();
        return;
    }

    if ($method === 'POST' && $path === '/profile/account') {
        $ctrl->profileController->saveAccountProfile($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/profile/servant') {
        $ctrl->profileController->saveServantProfile($_POST, $_FILES);
        return;
    }

    if ($method === 'POST' && $path === '/admin/servant-verification') {
        $ctrl->profileController->updateServantVerification($_POST);
        return;
    }

    if ($method === 'GET' && $path === '/profile/employer') {
        $ctrl->profileController->showEmployerForm();
        return;
    }


    if ($method === 'GET' && $path === '/servants') {
        $ctrl->profileController->listServants($_GET);
        return;
    }

    if ($method === 'POST' && $path === '/hire-requests') {
        $ctrl->hireRequestController->createRequest($_POST);
        return;
    }

    if ($method === 'GET' && $path === '/servant/requests') {
        $ctrl->hireRequestController->index();
        return;
    }

    if ($method === 'POST' && $path === '/servant/requests/status') {
        $ctrl->hireRequestController->updateRequestStatus($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/profile/employer') {
        $ctrl->profileController->saveEmployerProfile($_POST);
        return;
    }

    if ($method === 'GET' && $path === '/job/book') {
        $ctrl->jobController->showBookForm($_GET);
        return;
    }

    if ($method === 'GET' && $path === '/messages') {
        $ctrl->messageController->index($_GET);
        return;
    }

    if ($method === 'POST' && $path === '/messages') {
        $ctrl->messageController->store($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/jobs') {
        $ctrl->jobController->create($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/jobs/apply') {
        $ctrl->jobController->apply($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/jobs/accept') {
        $ctrl->jobController->accept($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/jobs/reject') {
        $ctrl->jobController->reject($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/jobs/confirm') {
        $ctrl->jobController->confirm($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/payments/pay') {
        $ctrl->paymentController->processPayment($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/reviews') {
        $ctrl->reviewController->store($_POST);
        return;
    }

    // New public detail routes for marketplace UX
    if ($method === 'GET' && $path === '/provider/view.php') {
        $ctrl->profileController->showServantPublic();
        return;
    }

    if ($method === 'GET' && $path === '/parent/view.php') {
        $ctrl->profileController->showEmployerPublic();
        return;
    }

    if ($method === 'GET' && $path === '/jobs/detail') {
        $ctrl->jobController->showDetail();
        return;
    }

    if ($method === 'GET' && $path === '/jobs/available') {
        $ctrl->jobController->showAvailableJobs();
        return;
    }

    // Back-compat parent/provider dashboard targets
    if ($method === 'GET' && ($path === '/parent/jobs' || $path === '/parent/jobs.php')) {
        $ctrl->jobController->showParentJobs();
        return;
    }

    if ($method === 'GET' && ($path === '/parent/payments' || $path === '/parent/payments.php')) {
        $ctrl->paymentController->index();
        return;
    }

    if ($method === 'GET' && ($path === '/parent/providers' || $path === '/parent/providers.php')) {
        $ctrl->profileController->listServants($_GET);
        return;
    }

    if ($method === 'GET' && ($path === '/provider/jobs' || $path === '/provider/jobs.php')) {
        $ctrl->jobController->showProviderJobs();
        return;
    }

    if ($method === 'GET' && ($path === '/provider/applications' || $path === '/provider/applications.php')) {
        $ctrl->jobController->showProviderApplications();
        return;
    }

    if ($method === 'GET' && ($path === '/provider/payments' || $path === '/provider/payments.php')) {
        $ctrl->paymentController->index();
        return;
    }

    if ($method === 'GET' && $path === '/admin/users') {
        $ctrl->adminUserController->index();
        return;
    }

    if ($method === 'GET' && $path === '/admin/users/detail') {
        $ctrl->adminUserController->showUserDetail();
        return;
    }

    if ($method === 'POST' && $path === '/admin/users/toggle-block') {
        $ctrl->adminUserController->toggleBlock($_POST);
        return;
    }

    if ($method === 'GET' && $path === '/admin/jobs') {
        $ctrl->adminJobController->index();
        return;
    }

    if ($method === 'GET' && $path === '/admin/jobs/detail') {
        $ctrl->adminJobController->showDetail();
        return;
    }

    if ($method === 'GET' && $path === '/admin/providers') {
        $ctrl->adminUserController->listProviders();
        return;
    }

    if ($method === 'GET' && $path === '/admin/providers/detail') {
        $ctrl->adminUserController->showProviderDetail();
        return;
    }

    if ($method === 'POST' && $path === '/admin/users/delete') {
        $ctrl->adminUserController->delete($_POST);
        return;
    }

    if ($method === 'POST' && $path === '/logout') {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/');
        }
        $ctrl->authController->logout();
        return;
    }

    if ($method === 'GET') {
        $requestedPage = sanitizeInput($_GET['page'] ?? '');
        $hasMappedPage = isset($pathToPage[$path]);

        if ($requestedPage === '' && $hasMappedPage) {
            $requestedPage = $pathToPage[$path];
        }

        // For routes not mapped to a legacy page, defer to explicit path handlers below.
        if ($requestedPage !== '' || $hasMappedPage) {
            if ($requestedPage === '') {
                $requestedPage = authUser() ? 'dashboard' : 'login';
            }

            switch ($requestedPage) {
                case 'login':
                    $ctrl->authController->showLogin();
                    return;

                case 'register':
                    $ctrl->authController->showRegister();
                    return;

                case 'dashboard':
                    $user = authUser();

                    if (!$user) {
                        redirect('/login');
                    }

                    $role = normalizeRole((string) ($user['role'] ?? ''));

                    if ($role === 'parent') {
                        $ctrl->marketplaceController->employerDashboard();
                        return;
                    }

                    if ($role === 'provider') {
                        $ctrl->marketplaceController->servantDashboard();
                        return;
                    }

                    if ($role === 'administrator') {
                        $ctrl->marketplaceController->adminDashboard();
                        return;
                    }

                    $ctrl->marketplaceController->index();
                    return;

                case 'profiles':
                    $user = authUser();

                    if (!$user) {
                        redirect('/login');
                    }

                    $role = normalizeRole((string) ($user['role'] ?? ''));

                    if ($role === 'parent') {
                        $ctrl->profileController->showEmployerForm();
                        return;
                    }

                    if ($role === 'provider') {
                        $ctrl->profileController->showServantForm();
                        return;
                    }

                    http_response_code(403);
                    renderView('errors/404', [
                        'title' => 'Forbidden',
                        'message' => 'You do not have permission to access profiles.',
                    ]);
                    return;

                case 'profile-account':
                    $ctrl->profileController->showAccountForm();
                    return;

                case 'listings':
                    $user = authUser();
                    $role = $user ? normalizeRole((string) ($user['role'] ?? '')) : null;

                    if ($role === 'parent' || $role === 'administrator') {
                        $ctrl->profileController->listServants($_GET);
                        return;
                    }

                    $ctrl->marketplaceController->index();
                    return;

                case 'messages':
                    $ctrl->messageController->index($_GET);
                    return;

            }
        }
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
