<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/PaymentController.php
 |--------------------------------------------------------------------------
 | Handles payment status updates.
 */

class PaymentController
{
    private Payment $payments;
    private Job $jobs;

    public function __construct()
    {
        $this->payments = new Payment();
        $this->jobs = new Job();
    }

    public function processPayment(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/?page=dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? null);

        if (!$jobId) {
            setFlash('error', 'Missing job ID.');
            redirect('/?page=dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || (string) $job['parent_id'] !== (string) $_SESSION['user_id']) {
            setFlash('error', 'Unauthorized access.');
            redirect('/?page=dashboard');
        }

        if ((string) ($job['status'] ?? '') !== 'completed') {
            setFlash('error', 'Payments can only be processed for completed jobs.');
            redirect('/?page=dashboard');
        }

        $success = $this->payments->updateStatus($jobId, 'paid');

        if ($success) {
            setFlash('success', 'Payment successful! Thank you.');
        } else {
            setFlash('error', 'Could not process payment at this time.');
        }

        redirect('/?page=dashboard');
    }
}
