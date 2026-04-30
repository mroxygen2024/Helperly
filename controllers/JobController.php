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

    public function showBookForm(array $query): void
    {
        requireRole('parent');
        $providerId = sanitizeInput($query['provider_id'] ?? '');

        if (!$providerId || !$this->servantProfiles->isApprovedByUserId($providerId)) {
            setFlash('error', 'Invalid or unverified service provider selected.');
            redirect('/servants');
        }

        $providerProfile = $this->servantProfiles->getProfileByUserId($providerId);
        $employerProfiles = new EmployerProfile();
        $employerProfile = $employerProfiles->getProfileByUserId((string) ($_SESSION['user_id'] ?? ''));

        renderView('jobs/book', [
            'title' => 'Book Provider',
            'csrfToken' => csrfToken(),
            'provider_id' => $providerId,
            'provider' => $providerProfile,
            'employer_location' => $employerProfile['location'] ?? '',
        ]);
    }

    public function create(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/dashboard');
        }

        $parentId = (string) ($_SESSION['user_id'] ?? '');
        $time = sanitizeInput($payload['time'] ?? null);
        $duration = sanitizeInput($payload['duration'] ?? '');
        $serviceType = sanitizeInput($payload['service_type'] ?? '');
        $location = sanitizeInput($payload['location'] ?? '');
        $instructions = sanitizeInput($payload['instructions'] ?? '');
        $selectedProviderId = sanitizeInput($payload['selected_provider_id'] ?? null);
        $rate = (float) ($payload['rate'] ?? 0);
        $paymentMethod = sanitizeInput($payload['payment_method'] ?? 'cash');

        // If direct booking, we pull the rate from the provider profile to be safe
        if ($selectedProviderId) {
            if (!$this->servantProfiles->isApprovedByUserId($selectedProviderId)) {
                setFlash('error', 'The selected provider is no longer available or not verified.');
                redirect('/servants');
            }
            $profile = $this->servantProfiles->getProfileByUserId($selectedProviderId);
            if ($profile && isset($profile['rate'])) {
                $rate = (float) $profile['rate'];
            }
        }

        $numericDuration = (float) filter_var($duration, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $totalCost = $rate * $numericDuration;

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
                'rate' => $rate,
                'payment_method' => $paymentMethod
            ]);
            setFlash('error', implode(' ', $errors));
            redirect('/dashboard');
        }

        try {
            $created = $this->jobs->createJob(
                $parentId,
                $time,
                $duration,
                $serviceType,
                $location,
                $instructions,
                $selectedProviderId ?: null,
                $rate,
                $totalCost,
                $paymentMethod
            );

            if ($created) {
                clearOldInput();
                setFlash('success', 'Job ' . ($selectedProviderId ? 'booked and assigned directly.' : 'posted successfully with status open.'));
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

        redirect('/dashboard');
    }

    public function apply(array $payload): void
    {
        requireRole('provider');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/dashboard');
        }

        $providerId = (string) ($_SESSION['user_id'] ?? '');
        $jobId = sanitizeInput($payload['job_id'] ?? null);

        if ($providerId === '' || $jobId === '') {
            setFlash('error', 'Missing job application data.');
            redirect('/dashboard');
        }

        if (!$this->servantProfiles->isApprovedByUserId($providerId)) {
            setFlash('error', 'Only verified service providers can apply to jobs.');
            redirect('/dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || (string) ($job['status'] ?? '') !== 'open') {
            setFlash('error', 'Selected job is not available.');
            redirect('/dashboard');
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

        redirect('/dashboard');
    }

    public function accept(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? null);
        $providerId = sanitizeInput($payload['provider_id'] ?? null);
        $parentId = (string) ($_SESSION['user_id'] ?? '');

        if (!$jobId || !$providerId) {
            setFlash('error', 'Missing required data.');
            redirect('/dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || (string) $job['parent_id'] !== $parentId) {
            setFlash('error', 'You are not authorized to accept applicants for this job.');
            redirect('/dashboard');
        }

        if ((string) $job['status'] !== 'open') {
            setFlash('error', 'This job is no longer open.');
            redirect('/dashboard');
        }

        try {
            $updated = $this->applications->updateApplicationStatus($jobId, $providerId, 'accepted');

            if ($updated) {
               // Fetch provider details to get their hourly rate
                $profile = $this->servantProfiles->getProfileByUserId($providerId);
                $rate = (float) ($profile['rate'] ?? 0);
                $numericDuration = (float) filter_var($job['duration'] ?? '', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $totalCost = $rate * $numericDuration;
                
                $paymentMethod = sanitizeInput($payload['payment_method'] ?? (string)($job['payment_method'] ?? 'cash'));
                
                // Mark job as active and record the provider with the calculated cost
                $this->jobs->acceptProvider($jobId, $providerId, $rate, $totalCost, $paymentMethod);

                // Reject other applicants
                $this->applications->rejectOtherApplicants($jobId, $providerId);

                setFlash('success', 'Applicant accepted. The job is now active at a total cost of ' . $totalCost);
            } else {
                setFlash('error', 'Could not accept applicant. Please try again.');
            }
        } catch (Throwable $exception) {
            error_log('Accept job application failed: ' . $exception->getMessage());
            setFlash('error', 'An error occurred while accepting the applicant.');
        }

        redirect('/dashboard');
    }

    public function reject(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? null);
        $providerId = sanitizeInput($payload['provider_id'] ?? null);
        $parentId = (string) ($_SESSION['user_id'] ?? '');

        if (!$jobId || !$providerId) {
            setFlash('error', 'Missing required data.');
            redirect('/dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || (string) $job['parent_id'] !== $parentId) {
            setFlash('error', 'You are not authorized to reject applicants for this job.');
            redirect('/dashboard');
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

        redirect('/dashboard');
    }

    public function confirm(array $payload): void
    {
        $user = authUser();
        if (!$user) {
            redirect('/login');
        }

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? null);
        $userId = (string) ($_SESSION['user_id'] ?? '');
        $role = normalizeRole((string) ($user['role'] ?? ''));

        if (!$jobId) {
            setFlash('error', 'Missing job ID.');
            redirect('/dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || (string) ($job['status'] ?? '') !== 'active') {
            setFlash('error', 'This job is not in a state that can be confirmed.');
            redirect('/dashboard');
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

        redirect('/dashboard');
    }

    public function stop(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? null);
        $parentId = (string) ($_SESSION['user_id'] ?? '');

        if (!$jobId) {
            setFlash('error', 'Missing job ID.');
            redirect('/dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || (string) $job['parent_id'] !== $parentId) {
            setFlash('error', 'You are not authorized to stop this job.');
            redirect('/dashboard');
        }

        if (!in_array((string) ($job['status'] ?? ''), ['open', 'active'], true)) {
            // Early check for user-friendly error messaging; the model also enforces this atomically.
            setFlash('error', 'This job cannot be stopped in its current state.');
            redirect('/dashboard');
        }

        try {
            $stopped = $this->jobs->stopJob($jobId, $parentId);
            if ($stopped) {
                setFlash('success', 'Job has been cancelled successfully.');
            } else {
                setFlash('error', 'Could not cancel the job. Please try again.');
            }
        } catch (Throwable $exception) {
            error_log('Job stop failed: ' . $exception->getMessage());
            setFlash('error', 'An error occurred while cancelling the job.');
        }

        redirect('/dashboard');
    }
}
