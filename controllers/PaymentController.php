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

        $jobId = sanitizeInput($payload['job_id'] ?? '');

        if (!$jobId) {
            setFlash('error', 'Missing job ID for payment.');
            redirect('/?page=dashboard');
        }

        try {
            $job = $this->jobs->getJobById($jobId);
            $parentId = (string) ($_SESSION['user_id'] ?? '');

            if (!$job || (string) $job['parent_id'] !== $parentId) {
                setFlash('error', 'Unauthorized access.');
                redirect('/?page=dashboard');
            }

            if ((string) ($job['status'] ?? '') !== 'completed') {
                setFlash('error', 'Payments can only be processed for completed jobs.');
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
