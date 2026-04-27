<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

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

    public function showForgotPassword(): void
    {
        renderView('auth/forgot-password', [
            'title' => 'Forgot Password',
            'csrfToken' => csrfToken(),
        ]);
    }

    public function showResetPassword(array $query): void
    {
        $token = sanitizeInput($query['token'] ?? null);

        renderView('auth/reset-password', [
            'title' => 'Reset Password',
            'csrfToken' => csrfToken(),
            'token' => $token,
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
        $role = normalizeRole(sanitizeInput($payload['role'] ?? null));

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
            $errors[] = 'Role must be parent, service provider, or administrator.';
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

        $verificationToken = generateVerificationToken();

        try {
            $this->users->createUser(
                $name,
                $email,
                $phone,
                $password,
                $role,
                $verificationToken
            );

            $this->sendVerificationEmail($email, $verificationToken);
        } catch (Throwable $exception) {
            error_log('Registration failed: ' . $exception->getMessage());
            setFlash('error', 'Registration failed. Please try again.');
            redirect('/register');
        }

        clearOldInput();
        setFlash('success', 'Registration successful. Please verify your email before login.');
        redirect('/login');
    }

    public function verifyEmail(array $query): void
    {
        $token = sanitizeInput($query['token'] ?? null);

        if ($token === '' || strlen($token) !== 64 || !ctype_xdigit($token)) {
            setFlash('error', 'Invalid verification link.');
            redirect('/login');
        }

        try {
            $verified = $this->users->verifyUserByToken($token);
        } catch (Throwable $exception) {
            error_log('Email verification failed: ' . $exception->getMessage());
            setFlash('error', 'Could not verify account right now. Please try again later.');
            redirect('/login');
        }

        if (!$verified) {
            setFlash('error', 'Verification token is invalid or already used.');
            redirect('/login');
        }

        setFlash('success', 'Email verified successfully. You can now log in.');
        redirect('/login');
    }

    public function forgotPassword(array $payload): void
    {
        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/forgot-password');
        }

        $email = sanitizeEmail($payload['email'] ?? null);

        if (!validateEmail($email)) {
            rememberOldInput(['email' => $email]);
            setFlash('error', 'Valid email is required.');
            redirect('/forgot-password');
        }

        $token = generateVerificationToken();

        try {
            $exists = $this->users->createPasswordResetToken($email, $token, 3600);

            // Do not reveal whether the account exists.
            if ($exists) {
                $this->sendPasswordResetEmail($email, $token);
            }
        } catch (Throwable $exception) {
            error_log('Forgot password failed: ' . $exception->getMessage());
            setFlash('error', 'Could not process reset request right now. Please try again later.');
            redirect('/forgot-password');
        }

        clearOldInput();
        setFlash('success', 'If the email exists, a reset link has been sent.');
        redirect('/login');
    }

    public function resetPassword(array $payload): void
    {
        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/forgot-password');
        }

        $token = sanitizeInput($payload['token'] ?? null);
        $password = (string) ($payload['password'] ?? '');
        $confirmPassword = (string) ($payload['confirm_password'] ?? '');

        if ($token === '' || strlen($token) !== 64 || !ctype_xdigit($token)) {
            setFlash('error', 'Invalid reset token.');
            redirect('/forgot-password');
        }

        if (!validatePassword($password)) {
            setFlash('error', 'Password must be at least 8 chars and include letters and numbers.');
            redirect('/reset-password?token=' . urlencode($token));
        }

        if (!hash_equals($password, $confirmPassword)) {
            setFlash('error', 'Password confirmation does not match.');
            redirect('/reset-password?token=' . urlencode($token));
        }

        try {
            $updated = $this->users->resetPasswordByToken($token, $password);
        } catch (Throwable $exception) {
            error_log('Reset password failed: ' . $exception->getMessage());
            setFlash('error', 'Could not reset password right now. Please try again later.');
            redirect('/forgot-password');
        }

        if (!$updated) {
            setFlash('error', 'Reset token is invalid or expired.');
            redirect('/forgot-password');
        }

        setFlash('success', 'Password reset successful. Please login with your new password.');
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

        if ((bool) ($user['is_blocked'] ?? false)) {
            rememberOldInput(['email' => $email]);
            setFlash('error', 'Your account has been suspended. Please contact support.');
            redirect('/login');
        }

        $isVerified = array_key_exists('is_verified', $user) ? (bool) $user['is_verified'] : true;
        if (!$isVerified) {
            rememberOldInput(['email' => $email]);
            setFlash('error', 'Please verify your email before logging in.');
            redirect('/login');
        }

        session_regenerate_id(true);

        $userId = (string) ($user['_id'] ?? '');
        $userRole = normalizeRole((string) ($user['role'] ?? ''));

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

        // The dashboard route acts as a central hub and routes to the correct dashboard based on role
        $redirectPath = '/dashboard';

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

        if ((bool) ($user['is_blocked'] ?? false)) {
            jsonResponse(['error' => 'Your account has been suspended. Please contact support.'], 403);
        }

        $isVerified = array_key_exists('is_verified', $user) ? (bool) $user['is_verified'] : true;
        if (!$isVerified) {
            jsonResponse(['error' => 'Please verify your email before logging in.'], 403);
        }

        $userId = (string) ($user['_id'] ?? '');
        $role = normalizeRole((string) ($user['role'] ?? ''));

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

    private function sendVerificationEmail(string $email, string $token): void
    {
        $verificationUrl = rtrim(appConfig()['base_url'], '/') . '/verify-email?token=' . urlencode($token);
        $subject = 'Verify your Servant Marketplace account';
        $message = "Welcome! Verify your account by visiting: $verificationUrl";

        $this->sendTransactionalEmail($email, $subject, $message, 'Verification email');
    }

    private function sendPasswordResetEmail(string $email, string $token): void
    {
        $resetUrl = rtrim(appConfig()['base_url'], '/') . '/reset-password?token=' . urlencode($token);
        $subject = 'Reset your Servant Marketplace password';
        $message = "A password reset was requested for your account. Open this link within 1 hour: $resetUrl";

        $this->sendTransactionalEmail($email, $subject, $message, 'Password reset email');
    }

    private function sendTransactionalEmail(string $toEmail, string $subject, string $plainTextBody, string $label): void
    {
        $config = appConfig();

        if (!class_exists(PHPMailer::class)) {
            error_log($label . ' failed: PHPMailer is not installed.');
            return;
        }

        if (($config['smtp_host'] ?? '') === '') {
            error_log($label . ' skipped: SMTP_HOST is not configured.');
            return;
        }

        try {
            $mailer = new PHPMailer(true);
            $mailer->isSMTP();
            $mailer->Host = (string) $config['smtp_host'];
            $mailer->Port = max(1, (int) ($config['smtp_port'] ?? 587));
            $mailer->SMTPAuth = true;
            $mailer->Username = (string) ($config['smtp_username'] ?? '');
            $mailer->Password = (string) ($config['smtp_password'] ?? '');

            $encryption = strtolower(trim((string) ($config['smtp_encryption'] ?? 'tls')));
            if ($encryption === 'ssl') {
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls') {
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mailer->CharSet = 'UTF-8';
            $mailer->setFrom(
                (string) ($config['smtp_from_email'] ?? 'no-reply@servant-marketplace.local'),
                (string) ($config['smtp_from_name'] ?? 'Servant Marketplace')
            );
            $mailer->addAddress($toEmail);
            $mailer->Subject = $subject;
            $mailer->Body = $plainTextBody;
            $mailer->AltBody = $plainTextBody;

            $mailer->send();
        } catch (MailerException $exception) {
            error_log($label . ' failed for [' . $toEmail . ']: ' . $exception->getMessage());
        }
    }
}
