<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/AdminUserController.php
 |--------------------------------------------------------------------------
 | Handles administrative user management tasks.
 */

class AdminUserController
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index(): void
    {
        requireRole('administrator');

        $users = $this->users->getAllUsers(100);

        renderView('admin/users', [
            'title' => 'User Management',
            'csrfToken' => csrfToken(),
            'users' => $users,
            'currentUser' => authUser(),
        ]);
    }

    public function toggleBlock(array $payload): void
    {
        requireRole('administrator');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/admin/users');
        }

        $userId = sanitizeInput($payload['user_id'] ?? '');
        $isBlocked = (bool) ($payload['block'] ?? false);

        if ($userId === (string) ($_SESSION['user_id'] ?? '')) {
            setFlash('error', 'You cannot block your own account.');
            redirect('/admin/users');
        }

        try {
            $success = $this->users->updateBlockedStatus($userId, $isBlocked);
            if ($success) {
                $status = $isBlocked ? 'blocked' : 'unblocked';
                setFlash('success', "User successfully {$status}.");
            } else {
                setFlash('error', 'Could not update user status.');
            }
        } catch (Throwable $exception) {
            error_log('User block toggle failed: ' . $exception->getMessage());
            setFlash('error', 'An error occurred.');
        }

        redirect('/admin/users');
    }

    public function delete(array $payload): void
    {
        requireRole('administrator');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/admin/users');
        }

        $userId = sanitizeInput($payload['user_id'] ?? '');

        if ($userId === (string) ($_SESSION['user_id'] ?? '')) {
            setFlash('error', 'You cannot delete your own account.');
            redirect('/admin/users');
        }

        try {
            $success = $this->users->deleteUser($userId);
            if ($success) {
                setFlash('success', 'User successfully deleted.');
            } else {
                setFlash('error', 'Could not delete user.');
            }
        } catch (Throwable $exception) {
            error_log('User deletion failed: ' . $exception->getMessage());
            setFlash('error', 'An error occurred.');
        }

        redirect('/admin/users');
    }
}
