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

// Core app helpers and models
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/Router.php';

// Dynamically require models and controllers to ensure lightweight loading
foreach (glob(dirname(__DIR__) . '/models/*.php') as $filename) {
    require_once $filename;
}
foreach (glob(dirname(__DIR__) . '/controllers/*.php') as $filename) {
    require_once $filename;
}

startSecureSession();

// Lazy load controllers
$ctrl = new class {
    private array $instances = [];
    public function __get(string $name) {
        $class = ucfirst($name);
        return $this->instances[$class] ??= new $class();
    }
};

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($method === 'GET' && str_starts_with($path, '/assets/')) {
    $requested = realpath(dirname(__DIR__) . $path);
    $assetsRoot = realpath(dirname(__DIR__) . '/assets');
    if (!$requested || !$assetsRoot || !str_starts_with($requested, $assetsRoot) || !is_file($requested)) {
        http_response_code(404);
        exit;
    }
    $mimeType = match (strtolower(pathinfo($requested, PATHINFO_EXTENSION))) {
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        default => mime_content_type($requested) ?: 'application/octet-stream',
    };
    header('Content-Type: ' . $mimeType);
    readfile($requested);
    exit;
}

try {
    $router = new Router();

    // API Routes
    $router->post('/api/login', fn() => $ctrl->authController->loginApi(requestJsonBody()));
    $router->get('/api/me', fn() => $ctrl->authController->meApi(requireJwtAuth()));
    $router->get('/api/messages', fn() => $ctrl->messageController->apiFetch($_GET));

    // Authentication Routes
    $router->post('/login', fn() => $ctrl->authController->login($_POST));
    $router->post('/register', fn() => $ctrl->authController->register($_POST));
    $router->get('/forgot-password', fn() => $ctrl->authController->showForgotPassword());
    $router->post('/forgot-password', fn() => $ctrl->authController->forgotPassword($_POST));
    $router->get('/reset-password', fn() => $ctrl->authController->showResetPassword($_GET));
    $router->post('/reset-password', fn() => $ctrl->authController->resetPassword($_POST));
    
    $router->post('/logout', function() use ($ctrl) {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/');
        }
        $ctrl->authController->logout();
    });

    // Profile & Services
    $router->get('/services', fn() => $ctrl->serviceController->showForm());
    $router->post('/services', fn() => $ctrl->serviceController->create($_POST));
    
    $router->get('/profile/servant', fn() => $ctrl->profileController->showServantForm());
    $router->post('/profile/servant', fn() => $ctrl->profileController->saveServantProfile($_POST, $_FILES));
    
    $router->get('/profile/account', fn() => $ctrl->profileController->showAccountForm());
    $router->post('/profile/account', fn() => $ctrl->profileController->saveAccountProfile($_POST));
    
    $router->get('/profile/employer', fn() => $ctrl->profileController->showEmployerForm());
    $router->post('/profile/employer', fn() => $ctrl->profileController->saveEmployerProfile($_POST));
    
    $router->get('/servants', fn() => $ctrl->profileController->listServants($_GET));

    // Hire Requests & Jobs
    $router->post('/hire-requests', fn() => $ctrl->hireRequestController->createRequest($_POST));
    $router->get('/servant/requests', fn() => $ctrl->hireRequestController->index());
    $router->post('/servant/requests/status', fn() => $ctrl->hireRequestController->updateRequestStatus($_POST));
    
    $router->get('/job/book', fn() => $ctrl->jobController->showBookForm($_GET));
    $router->post('/jobs', fn() => $ctrl->jobController->create($_POST));
    $router->post('/jobs/apply', fn() => $ctrl->jobController->apply($_POST));
    $router->post('/jobs/accept', fn() => $ctrl->jobController->accept($_POST));
    $router->post('/jobs/reject', fn() => $ctrl->jobController->reject($_POST));
    $router->post('/jobs/confirm', fn() => $ctrl->jobController->confirm($_POST));
    $router->get('/jobs/detail', fn() => $ctrl->jobController->showDetail());
    $router->get('/jobs/available', fn() => $ctrl->jobController->showAvailableJobs());

    // Messages & Payments & Reviews
    $router->get('/messages', fn() => $ctrl->messageController->index($_GET));
    $router->post('/messages', fn() => $ctrl->messageController->store($_POST));
    $router->post('/payments/pay', fn() => $ctrl->paymentController->processPayment($_POST));
    $router->post('/reviews', fn() => $ctrl->reviewController->store($_POST));

    // Public Profiles
    $router->get('/provider/view.php', fn() => $ctrl->profileController->showServantPublic());
    $router->get('/parent/view.php', fn() => $ctrl->profileController->showEmployerPublic());

    // Parent/Provider Dashboards (Legacy explicit targets)
    $router->get('/parent/jobs', fn() => $ctrl->jobController->showParentJobs());
    $router->get('/parent/jobs.php', fn() => $ctrl->jobController->showParentJobs());
    $router->get('/parent/payments', fn() => $ctrl->paymentController->index());
    $router->get('/parent/payments.php', fn() => $ctrl->paymentController->index());
    $router->get('/parent/providers', fn() => $ctrl->profileController->listServants($_GET));
    $router->get('/parent/providers.php', fn() => $ctrl->profileController->listServants($_GET));
    
    $router->get('/provider/jobs', fn() => $ctrl->jobController->showProviderJobs());
    $router->get('/provider/jobs.php', fn() => $ctrl->jobController->showProviderJobs());
    $router->get('/provider/applications', fn() => $ctrl->jobController->showProviderApplications());
    $router->get('/provider/applications.php', fn() => $ctrl->jobController->showProviderApplications());
    $router->get('/provider/payments', fn() => $ctrl->paymentController->index());
    $router->get('/provider/payments.php', fn() => $ctrl->paymentController->index());

    // Admin Routes
    $router->get('/admin/verifications', fn() => $ctrl->profileController->showAdminVerifications());
    $router->get('/admin/verified_user', fn() => $ctrl->profileController->showAdminVerifications());
    $router->post('/admin/servant-verification', fn() => $ctrl->profileController->updateServantVerification($_POST));
    $router->get('/admin/users', fn() => $ctrl->adminUserController->index());
    $router->get('/admin/users/detail', fn() => $ctrl->adminUserController->showUserDetail());
    $router->post('/admin/users/toggle-block', fn() => $ctrl->adminUserController->toggleBlock($_POST));
    $router->post('/admin/users/delete', fn() => $ctrl->adminUserController->delete($_POST));
    $router->get('/admin/jobs', fn() => $ctrl->adminJobController->index());
    $router->get('/admin/jobs/detail', fn() => $ctrl->adminJobController->showDetail());
    $router->get('/admin/providers', fn() => $ctrl->adminUserController->listProviders());
    $router->get('/admin/providers/detail', fn() => $ctrl->adminUserController->showProviderDetail());

    // Setup Legacy Fallback Map
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

    $router->setFallback(function (string $method, string $path) use ($ctrl, $pathToPage) {
        if ($method !== 'GET') {
            return;
        }

        $requestedPage = sanitizeInput($_GET['page'] ?? '');
        if ($requestedPage === '' && isset($pathToPage[$path])) {
            $requestedPage = $pathToPage[$path];
        }

        if ($requestedPage === '') {
            $requestedPage = authUser() ? 'dashboard' : 'login';
        }

        switch ($requestedPage) {
            case 'login': return $ctrl->authController->showLogin();
            case 'register': return $ctrl->authController->showRegister();
            case 'dashboard':
                $user = authUser();
                if (!$user) redirect('/login');
                $role = normalizeRole((string) ($user['role'] ?? ''));
                if ($role === 'parent') return $ctrl->marketplaceController->employerDashboard();
                if ($role === 'provider') return $ctrl->marketplaceController->servantDashboard();
                if ($role === 'administrator') return $ctrl->marketplaceController->adminDashboard();
                return $ctrl->marketplaceController->index();
            case 'profiles':
                $user = authUser();
                if (!$user) redirect('/login');
                $role = normalizeRole((string) ($user['role'] ?? ''));
                if ($role === 'parent') return $ctrl->profileController->showEmployerForm();
                if ($role === 'provider') return $ctrl->profileController->showServantForm();
                http_response_code(403);
                renderView('errors/404', ['title' => 'Forbidden', 'message' => 'You do not have permission.']);
                exit;
            case 'profile-account': return $ctrl->profileController->showAccountForm();
            case 'listings':
                $user = authUser();
                $role = $user ? normalizeRole((string) ($user['role'] ?? '')) : null;
                if ($role === 'parent' || $role === 'administrator') return $ctrl->profileController->listServants($_GET);
                return $ctrl->marketplaceController->index();
            case 'messages': return $ctrl->messageController->index($_GET);
        }
    });

    $router->dispatch($method, $path);

} catch (Throwable $exception) {
    error_log('Application error: ' . $exception->getMessage());
    http_response_code(500);
    echo isProduction() ? 'An unexpected error occurred.' : escape($exception->getMessage());
}
