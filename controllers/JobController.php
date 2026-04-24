<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/ purpose
 |--------------------------------------------------------------------------
 | Handle HTTP input, coordinate models, then choose the view to render.
 */

class JobController
{
    private Job $jobs;
    private JobApplication $applications;
    private ServantProfile $servantProfiles;

    public function __construct()
    {
        $this->jobs = new Job();
        $this->applications = new JobApplication();
        $this->servantProfiles = new ServantProfile();
    }

    public function create(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/?page=dashboard');
        }

        $parentId = (string) ($_SESSION['user_id'] ?? '');
        $time = sanitizeInput($payload['time'] ?? null);
        $duration = sanitizeInput($payload['duration'] ?? null);
        $serviceType = sanitizeInput($payload['service_type'] ?? null);
        $location = sanitizeInput($payload['location'] ?? null);
        $instructions = sanitizeInput($payload['instructions'] ?? null);

        $errors = [];

        if (!validateRequired($time)) {
            $errors[] = 'Time is required.';
        }
        if (!validateRequired($duration)) {
            $errors[] = 'Duration is required.';
        }
        if (!validateRequired($serviceType)) {
            $errors[] = 'Type of service is required.';
        }
        if (!validateRequired($location)) {
            $errors[] = 'Location is required.';
        }
        if (!validateRequired($instructions)) {
            $errors[] = 'Special instructions are required.';
        }

        if (!empty($errors)) {
            rememberOldInput([
                'time' => $time,
                'duration' => $duration,
                'service_type' => $serviceType,
                'location' => $location,
                'instructions' => $instructions,
            ]);
            setFlash('error', implode(' ', $errors));
            redirect('/?page=dashboard');
        }

        try {
            $created = $this->jobs->createJob(
                $parentId,
                $time,
                $duration,
                $serviceType,
                $location,
                $instructions
            );

            if ($created) {
                clearOldInput();
                setFlash('success', 'Job posted successfully with status open.');
            } else {
                setFlash('error', 'Job could not be created. Please try again.');
            }
        } catch (InvalidArgumentException $exception) {
            rememberOldInput([
                'time' => $time,
                'duration' => $duration,
                'service_type' => $serviceType,
                'location' => $location,
                'instructions' => $instructions,
            ]);
            setFlash('error', $exception->getMessage());
        } catch (Throwable $exception) {
            error_log('Job creation failed: ' . $exception->getMessage());
            setFlash('error', 'Could not create job right now. Please try again.');
        }

        redirect('/?page=dashboard');
    }

    public function apply(array $payload): void
    {
        requireRole('service_provider');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/?page=dashboard');
        }

        $providerId = (string) ($_SESSION['user_id'] ?? '');
        $jobId = sanitizeInput($payload['job_id'] ?? null);

        if ($providerId === '' || $jobId === '') {
            setFlash('error', 'Missing job application data.');
            redirect('/?page=dashboard');
        }

        if (!$this->servantProfiles->isApprovedByUserId($providerId)) {
            setFlash('error', 'Only verified service providers can apply to jobs.');
            redirect('/?page=dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || (string) ($job['status'] ?? '') !== 'open') {
            setFlash('error', 'Selected job is not available.');
            redirect('/?page=dashboard');
        }

        try {
            $created = $this->applications->createApplication($jobId, $providerId);
            if ($created) {
                setFlash('success', 'Application submitted successfully.');
            } else {
                setFlash('error', 'Application could not be saved. Please try again.');
            }
        } catch (RuntimeException $exception) {
            setFlash('error', $exception->getMessage());
        } catch (Throwable $exception) {
            error_log('Job application failed: ' . $exception->getMessage());
            setFlash('error', 'Could not submit application right now. Please try again.');
        }

        redirect('/?page=dashboard');
    }

    public function accept(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/?page=dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? null);
        $providerId = sanitizeInput($payload['provider_id'] ?? null);
        $parentId = (string) ($_SESSION['user_id'] ?? '');

        if (!$jobId || !$providerId) {
            setFlash('error', 'Missing required data.');
            redirect('/?page=dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || (string) $job['parent_id'] !== $parentId) {
            setFlash('error', 'You are not authorized to accept applicants for this job.');
            redirect('/?page=dashboard');
        }

        if ((string) $job['status'] !== 'open') {
            setFlash('error', 'This job is no longer open.');
            redirect('/?page=dashboard');
        }

        try {
            $updated = $this->applications->updateApplicationStatus($jobId, $providerId, 'accepted');

            if ($updated) {
                // Mark job as active and record the provider
                $this->jobs->acceptProvider($jobId, $providerId);

                // Reject other applicants
                $this->applications->rejectOtherApplicants($jobId, $providerId);

                setFlash('success', 'Applicant accepted. The job is now active.');
            } else {
                setFlash('error', 'Could not accept applicant. Please try again.');
            }
        } catch (Throwable $exception) {
            error_log('Accept job application failed: ' . $exception->getMessage());
            setFlash('error', 'An error occurred while accepting the applicant.');
        }

        redirect('/?page=dashboard');
    }

    public function reject(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/?page=dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? null);
        $providerId = sanitizeInput($payload['provider_id'] ?? null);
        $parentId = (string) ($_SESSION['user_id'] ?? '');

        if (!$jobId || !$providerId) {
            setFlash('error', 'Missing required data.');
            redirect('/?page=dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || (string) $job['parent_id'] !== $parentId) {
            setFlash('error', 'You are not authorized to reject applicants for this job.');
            redirect('/?page=dashboard');
        }

        try {
            $updated = $this->applications->updateApplicationStatus($jobId, $providerId, 'rejected');
            if ($updated) {
                setFlash('success', 'Applicant has been rejected.');
            } else {
                setFlash('error', 'Could not reject applicant. Please try again.');
            }
        } catch (Throwable $exception) {
            error_log('Reject job application failed: ' . $exception->getMessage());
            setFlash('error', 'An error occurred while rejecting the applicant.');
        }

        redirect('/?page=dashboard');
    }

    public function confirm(array $payload): void
    {
        $user = authUser();
        if (!$user) {
            redirect('/?page=login');
        }

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/?page=dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? null);
        $userId = (string) ($_SESSION['user_id'] ?? '');
        $role = normalizeRole((string) ($user['role'] ?? ''));

        if (!$jobId) {
            setFlash('error', 'Missing job ID.');
            redirect('/?page=dashboard');
        }

        try {
            $success = $this->jobs->confirmJob($jobId, $userId, $role);
            if ($success) {
                setFlash('success', 'Your confirmation has been recorded.');
            } else {
                setFlash('error', 'Could not record confirmation. You might not be authorized for this job.');
            }
        } catch (Throwable $exception) {
            error_log('Job confirmation failed: ' . $exception->getMessage());
            setFlash('error', 'An error occurred during confirmation.');
        }

        redirect('/?page=dashboard');
    }
}
