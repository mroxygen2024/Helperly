<?php

declare(strict_types=1);

class PaymentController
{
    private Payment $payments;

    public function __construct()
    {
        $this->payments = new Payment();
    }

    public function processPayment(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/?page=dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? '');

        if (!$jobId) {
            setFlash('error', 'Missing job ID for payment.');
            redirect('/?page=dashboard');
        }

        try {
            $jobModel = new Job();
            $job = $jobModel->getJobById($jobId);
            $parentId = (string) ($_SESSION['user_id'] ?? '');

            if (!$job || (string) $job['parent_id'] !== $parentId) {
                setFlash('error', 'Unauthorized access.');
                redirect('/?page=dashboard');
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
