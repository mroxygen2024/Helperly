<?php

declare(strict_types=1);

class PaymentController
{
    private Payment $payments;

    public function __construct()
    {
        $this->payments = new Payment();
    }

    public function index(): void
    {
        $user = authUser();
        if (!$user) {
            redirect('/login');
        }

        $userId = (string) ($_SESSION['user_id'] ?? '');
        $role = normalizeRole((string) ($user['role'] ?? ''));
        
        $payments = [];
        if ($role === 'parent') {
            $payments = $this->payments->getPaymentsByParentId($userId);
        } else if ($role === 'provider') {
            $payments = $this->payments->getPaymentsByProviderId($userId);
        }

        renderView('payments/index', [
            'title' => 'My Payments',
            'payments' => $payments,
            'user' => $user,
        ]);
    }

    public function processPayment(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? '');

        if (!$jobId) {
            setFlash('error', 'Missing job ID for payment.');
            redirect('/dashboard');
        }

        try {
            $jobModel = new Job();
            $job = $jobModel->getJobById($jobId);
            $parentId = (string) ($_SESSION['user_id'] ?? '');

            if (!$job || (string) $job['parent_id'] !== $parentId) {
                setFlash('error', 'Unauthorized access.');
                redirect('/dashboard');
            }

            $updated = $this->payments->updateStatus($jobId, 'paid');
            if ($updated) {
                setFlash('success', 'Payment processed successfully.');
            } else {
                setFlash('error', 'Could not process payment. Please try again.');
            }
        } catch (Throwable $exception) {
            error_log('Payment processing failed: ' . $exception->getMessage());
            setFlash('error', 'An error occurred while processing the payment.');
        }

        redirect('/?page=dashboard');
    }
}
