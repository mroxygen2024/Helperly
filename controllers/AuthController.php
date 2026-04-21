<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/ purpose
 |--------------------------------------------------------------------------
 | Handle HTTP input, coordinate models, then choose the view to render.
 */

class AuthController
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function showLogin(): void
    {
        renderView('login', [
            'title' => 'Login',
            'csrfToken' => csrfToken(),
        ]);
    }

    public function showRegister(): void
    {
        renderView('register', [
            'title' => 'Register',
            'csrfToken' => csrfToken(),
        ]);
    }

    public function register(array $payload): void
    {
        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/register');
        }

        $name = sanitizeInput($payload['name'] ?? null);
        $email = sanitizeEmail($payload['email'] ?? null);
        $phone = sanitizePhone($payload['phone'] ?? null);
        $password = (string) ($payload['password'] ?? '');
        $role = sanitizeInput($payload['role'] ?? null);

        $errors = [];

        if (!validateRequired($name)) {
            $errors[] = 'Name is required.';
        }

        if (!validateEmail($email)) {
            $errors[] = 'Valid email is required.';
        }

        if (!validatePhone($phone)) {
            $errors[] = 'Valid phone number is required.';
        }

        if (!validatePassword($password)) {
            $errors[] = 'Password must be at least 8 chars and include letters and numbers.';
        }

        if (!validateRole($role)) {
            $errors[] = 'Role must be servant or employer.';
        }

        if ($this->users->findUserByEmail($email)) {
            $errors[] = 'Email is already registered.';
        }

        if (!empty($errors)) {
            rememberOldInput([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'role' => $role,
            ]);
            setFlash('error', implode(' ', $errors));
            redirect('/register');
        }

        try {
            $this->users->createUser(
                $name,
                $email,
                $phone,
                $password,
                $role
            );
        } catch (Throwable $exception) {
            error_log('Registration failed: ' . $exception->getMessage());
            setFlash('error', 'Registration failed. Please try again.');
            redirect('/register');
        }

        clearOldInput();
        setFlash('success', 'Registration successful. Please login.');
        redirect('/login');
    }

    public function login(array $payload): void
    {
        // Ensure session is active even if this action is invoked in isolation.
        startSecureSession();

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/login');
        }

        $email = sanitizeEmail($payload['email'] ?? null);
        $password = (string) ($payload['password'] ?? '');

        if (!validateEmail($email) || !validateRequired($password)) {
            rememberOldInput(['email' => $email]);
            setFlash('error', 'Email and password are required.');
            redirect('/login');
        }

        try {
            $user = $this->users->findUserByEmail($email);
        } catch (Throwable $exception) {
            error_log('Login failed during user lookup: ' . $exception->getMessage());
            rememberOldInput(['email' => $email]);
            setFlash('error', 'Unable to login right now. Please try again.');
            redirect('/login');
        }

        if (!$user || !password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            rememberOldInput(['email' => $email]);
            setFlash('error', 'Invalid login credentials.');
            redirect('/login');
        }

        session_regenerate_id(true);

        $userId = (string) ($user['_id'] ?? '');
        $userRole = (string) ($user['role'] ?? '');

        $_SESSION['auth_user'] = [
            'id' => $userId,
            'name' => (string) ($user['name'] ?? ''),
            'email' => (string) ($user['email'] ?? ''),
            'role' => $userRole,
        ];

        // Explicit keys required by role-based authorization checks.
        $_SESSION['user_id'] = $userId;
        $_SESSION['role'] = $userRole;

        clearOldInput();

        $redirectPath = $userRole === 'employer' ? '/employer/dashboard' : '/servant/dashboard';

        setFlash('success', 'Welcome back, ' . $_SESSION['auth_user']['name'] . '.');
        redirect($redirectPath);
    }

    public function loginApi(array $payload): void
    {
        $email = sanitizeEmail($payload['email'] ?? null);
        $password = (string) ($payload['password'] ?? '');

        if (!validateEmail($email) || !validateRequired($password)) {
            jsonResponse(['error' => 'Email and password are required.'], 422);
        }

        try {
            $user = $this->users->findUserByEmail($email);
        } catch (Throwable $exception) {
            error_log('API login failed during user lookup: ' . $exception->getMessage());
            jsonResponse(['error' => 'Unable to login right now. Please try again.'], 500);
        }

        if (!$user || !password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            jsonResponse(['error' => 'Invalid login credentials.'], 401);
        }

        $userId = (string) ($user['_id'] ?? '');
        $role = (string) ($user['role'] ?? '');

        $token = createJwt([
            'user_id' => $userId,
            'role' => $role,
        ]);

        $claims = verifyJwt($token);

        jsonResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $claims['exp'] ?? null,
            'user' => [
                'id' => $userId,
                'name' => (string) ($user['name'] ?? ''),
                'email' => (string) ($user['email'] ?? ''),
                'role' => $role,
            ],
        ]);
    }

    public function meApi(array $claims): void
    {
        $userId = (string) ($claims['user_id'] ?? '');

        if ($userId === '') {
            jsonResponse(['error' => 'Invalid token payload.'], 401);
        }

        try {
            $user = $this->users->findUserById($userId);
        } catch (Throwable $exception) {
            error_log('API me lookup failed: ' . $exception->getMessage());
            jsonResponse(['error' => 'Unable to fetch user right now.'], 500);
        }

        if (!$user) {
            jsonResponse(['error' => 'User not found.'], 404);
        }

        jsonResponse([
            'user' => [
                'id' => (string) ($user['_id'] ?? ''),
                'name' => (string) ($user['name'] ?? ''),
                'email' => (string) ($user['email'] ?? ''),
                'phone' => (string) ($user['phone'] ?? ''),
                'role' => (string) ($user['role'] ?? ''),
            ],
        ]);
    }

    public function logout(): void
    {
        startSecureSession();

        unset($_SESSION['auth_user'], $_SESSION['user_id'], $_SESSION['role']);
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'], $params['httponly']);
        }

        session_regenerate_id(true);
        session_destroy();
        redirect('/login');
    }
}
