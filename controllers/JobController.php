<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/ purpose
 |--------------------------------------------------------------------------
 | Handle HTTP input, coordinate models, then choose the view to render.
 */

/**
 * JobController manages job postings, applications, and bookings.
 */
class JobController
{
    /** @var Job The job model for database operations. */
    private Job $jobs;
    /** @var JobApplication The job application model. */
    private JobApplication $applications;
    /** @var ServantProfile The servant profile model. */
    private ServantProfile $servantProfiles;
    /** @var Notification The notification model. */
    private Notification $notifications;

    /**
     * Constructor initializes all required models.
     */
    public function __construct()
    {
        $this->jobs = new Job();
        $this->applications = new JobApplication();
        $this->servantProfiles = new ServantProfile();
        $this->notifications = new Notification();
    }

    /**
     * Displays the job booking form for a specific provider.
     *
     * @param array $query The query parameters containing provider_id.
     */
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

    /**
     * Creates a new job posting from the booking form.
     *
     * @param array $payload The form payload containing job details.
     */
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
        if (!validateRequired($time))
            $errors[] = 'Time is required.';
        if (!validateRequired($duration))
            $errors[] = 'Duration is required.';
        if (!validateRequired($serviceType))
            $errors[] = 'Type of service is required.';
        if (!validateRequired($location))
            $errors[] = 'Location is required.';
        // Special instructions are now optional.

        if (!empty($errors)) {
            rememberOldInput($payload);
            setFlash('error', implode(' ', $errors));
            redirect('/dashboard');
        }

        try {
            $createdId = $this->jobs->createJob($parentId, $time, $duration, $serviceType, $location, $instructions, $selectedProviderId ?: null, $rate, $totalCost, $paymentMethod);
            if ($createdId) {
                if ($selectedProviderId) {
                    $this->notifications->create(
                        $selectedProviderId,
                        'job_assigned',
                        'New Job Assigned',
                        'You have been directly assigned to a ' . $serviceType . ' job.',
                        '/jobs/detail?id=' . $createdId
                    );
                }
                clearOldInput();
                setFlash('success', 'Job ' . ($selectedProviderId ? 'booked and assigned directly.' : 'posted successfully with status open.'));
            } else {
                setFlash('error', 'Job could not be created.');
            }
        } catch (Throwable $exception) {
            setFlash('error', $exception->getMessage());
        }
        redirect('/dashboard');
    }

    public function apply(array $payload): void
    {
        requireRole('provider');
        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/dashboard');
        }

        $providerId = (string) ($_SESSION['user_id'] ?? '');
        $providerName = (string) ($_SESSION['auth_user']['name'] ?? 'A provider');
        $jobId = sanitizeInput($payload['job_id'] ?? null);
        $coverLetter = sanitizeInput($payload['cover_letter'] ?? '');
        $availability = sanitizeInput($payload['availability'] ?? '');
        $timeline = sanitizeInput($payload['timeline'] ?? '');

        if (!$this->servantProfiles->isApprovedByUserId($providerId)) {
            setFlash('error', 'Only verified service providers can apply to jobs.');
            redirect('/dashboard');
        }

        try {
            if ($this->applications->createApplication($jobId, $providerId, $coverLetter, $availability, $timeline)) {
                $job = $this->jobs->getJobById($jobId);
                if ($job) {
                    $this->notifications->create(
                        (string) $job['parent_id'],
                        'application',
                        'New Application',
                        $providerName . ' has applied for your ' . ($job['service_type'] ?? 'job') . ' post.',
                        '/dashboard'
                    );
                }
                setFlash('success', 'Application submitted successfully.');
            } else {
                setFlash('error', 'Could not submit application.');
            }
        } catch (Throwable $exception) {
            setFlash('error', $exception->getMessage());
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

        try {
            $job = $this->jobs->getJobById($jobId);
            $profile = $this->servantProfiles->getProfileByUserId($providerId);
            $rate = (float) ($profile['rate'] ?? 0);
            $totalCost = $rate * (float) $job['duration'];
            $paymentMethod = $job['payment_method'] ?? 'cash';

            if ($this->applications->updateApplicationStatus($jobId, $providerId, 'accepted')) {
                $this->jobs->acceptProvider($jobId, $providerId, $rate, $totalCost, $paymentMethod);
                $this->applications->rejectOtherApplicants($jobId, $providerId);

                $this->notifications->create(
                    $providerId,
                    'job_accepted',
                    'Application Accepted',
                    'Your application for ' . ($job['service_type'] ?? 'a job') . ' has been accepted!',
                    '/jobs/detail?id=' . $jobId
                );

                setFlash('success', 'Applicant accepted.');
            }
        } catch (Throwable $exception) {
            setFlash('error', $exception->getMessage());
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

        try {
            if ($this->applications->updateApplicationStatus($jobId, $providerId, 'rejected')) {
                $job = $this->jobs->getJobById($jobId);
                $this->notifications->create(
                    $providerId,
                    'application_rejected',
                    'Application Rejected',
                    'Your application for ' . ($job['service_type'] ?? 'a job') . ' was not selected.',
                    '/jobs/detail?id=' . $jobId
                );
                setFlash('success', 'Applicant rejected.');
            } else {
                setFlash('error', 'Could not reject applicant.');
            }
        } catch (Throwable $exception) {
            setFlash('error', $exception->getMessage());
        }
        redirect('/dashboard');
    }

    public function confirm(array $payload): void
    {
        $user = authUser();
        if (!$user || !verifyCsrfToken($payload['csrf_token'] ?? null)) {
            redirect('/login');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? null);
        $userId = (string) ($_SESSION['user_id'] ?? '');
        $userName = (string) ($_SESSION['auth_user']['name'] ?? 'Someone');
        $role = normalizeRole((string) ($user['role'] ?? ''));

        try {
            if ($this->jobs->confirmJob($jobId, $userId, $role)) {
                $job = $this->jobs->getJobById($jobId);
                $receiverId = ($role === 'parent') ? (string) $job['selected_provider_id'] : (string) $job['parent_id'];

                $this->notifications->create(
                    $receiverId,
                    'job_confirmed',
                    'Job Completion Confirmed',
                    $userName . ' has confirmed the completion of ' . ($job['service_type'] ?? 'the job') . '.',
                    '/jobs/detail?id=' . $jobId
                );

                if ($job['status'] === 'completed') {
                    $this->notifications->create(
                        (string) $job['parent_id'],
                        'job_completed',
                        'Job Completed!',
                        'All parties confirmed completion of ' . ($job['service_type'] ?? 'the job') . '. You can now leave a review.',
                        '/jobs/detail?id=' . $jobId
                    );
                    $this->notifications->create(
                        (string) $job['selected_provider_id'],
                        'job_completed',
                        'Job Completed!',
                        'Job ' . ($job['service_type'] ?? '') . ' is now marked as completed. Payment is being processed.',
                        '/jobs/detail?id=' . $jobId
                    );
                }

                setFlash('success', 'Confirmation recorded.');
            }
        } catch (Throwable $exception) {
            setFlash('error', $exception->getMessage());
        }
        redirect('/dashboard');
    }

    public function showDetail(): void
    {
        $jobId = sanitizeInput($_GET['id'] ?? '');
        $job = $this->jobs->getJobById($jobId);
        if (!$job) {
            setFlash('error', 'Job not found.');
            redirect('/dashboard');
        }

        $userModel = new User();
        $jobArray = (array) $job;
        $jobArray['parent'] = $userModel->findUserById((string) $job['parent_id']);
        if (isset($job['selected_provider_id'])) {
            $jobArray['provider'] = $userModel->findUserById((string) $job['selected_provider_id']);
        }

        $paymentModel = new Payment();
        $jobArray['payment'] = $paymentModel->getPaymentByJobId($jobId);
        $reviewModel = new Review();
        $jobArray['review'] = $reviewModel->getReviewByJobId($jobId);

        renderView('jobs/detail', [
            'title' => 'Job Details',
            'job' => $jobArray,
            'user' => authUser(),
        ]);
    }

    public function showAvailableJobs(): void
    {
        requireRole('provider');
        $jobs = $this->jobs->getOpenJobs();

        renderView('jobs/index', [
            'title' => 'Available Jobs',
            'subtitle' => 'Browse and apply for opportunities near you.',
            'jobs' => $jobs,
            'user' => authUser()
        ]);
    }

    public function showParentJobs(): void
    {
        requireRole('parent');
        $parentId = (string) ($_SESSION['user_id'] ?? '');
        $jobs = $this->jobs->getJobsByParentId($parentId);

        renderView('jobs/index', [
            'title' => 'My Posted Jobs',
            'subtitle' => 'Track and manage the requirements you have posted.',
            'jobs' => $jobs,
            'user' => authUser()
        ]);
    }

    public function showProviderJobs(): void
    {
        requireRole('provider');
        $providerId = (string) ($_SESSION['user_id'] ?? '');
        $jobs = $this->jobs->getJobsByProviderId($providerId);

        renderView('jobs/index', [
            'title' => 'My Work History',
            'subtitle' => 'Track your active assignments and completed work.',
            'jobs' => $jobs,
            'user' => authUser()
        ]);
    }

    public function showProviderApplications(): void
    {
        requireRole('provider');
        $providerId = (string) ($_SESSION['user_id'] ?? '');
        $applicationsRaw = $this->applications->getApplicationsByProvider($providerId);

        // Enrich applications with job data
        $enrichedApplications = [];
        foreach ($applicationsRaw as $app) {
            $appArray = (array) $app;
            $appArray['job_data'] = (array) $this->jobs->getJobById((string) $app['job_id']);
            $enrichedApplications[] = $appArray;
        }

        renderView('jobs/index', [
            'title' => 'My Applications',
            'subtitle' => 'Track the status of your job applications.',
            'applications' => $enrichedApplications,
            'user' => authUser()
        ]);
    }
}
