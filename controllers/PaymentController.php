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
                // Notify provider that payment was made
                $providerId = (string) ($job['selected_provider_id'] ?? '');
                if ($providerId) {
                    $notif = new Notification();
                    $notif->create(
                        $providerId,
                        'payment_received',
                        'Payment Received',
                        'Payment for job "' . ($job['service_type'] ?? 'job') . '" has been processed by the parent.',
                        '/jobs/detail?id=' . $jobId
                    );
                }

                setFlash('success', 'Payment processed successfully.');
            } else {
                setFlash('error', 'Could not process payment. Please try again.');
            }
        } catch (Throwable $exception) {
            error_log('Payment processing failed: ' . $exception->getMessage());
            setFlash('error', 'An error occurred while processing the payment.');
        }

        // If job is completed allow parent to leave a review: redirect to job detail and open review modal
        if (isset($job) && is_array($job) && ($job['status'] ?? '') === 'completed') {
            redirect('/jobs/detail?id=' . $jobId . '&open_review=1');
        }

        redirect('/?page=dashboard');
    }
}
