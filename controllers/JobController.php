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

    private function normalizeTextTokens(mixed $value): array
    {
        if ($value instanceof Traversable) {
            $value = iterator_to_array($value, false);
        }

        if (is_array($value)) {
            $parts = [];
            foreach ($value as $item) {
                if (is_iterable($item)) {
                    $parts[] = implode(' ', $this->normalizeTextTokens($item));
                    continue;
                }

                if (is_object($item) && !method_exists($item, '__toString')) {
                    continue;
                }

                $parts[] = trim((string) $item);
            }

            $value = implode(' ', array_filter($parts, static fn(string $part): bool => $part !== ''));
        }

        $text = mb_strtolower(trim((string) $value));
        if ($text === '') {
            return [];
        }

        $parts = preg_split('/[^a-z0-9]+/i', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $tokens = [];

        foreach ($parts as $part) {
            $token = trim((string) $part);
            if (strlen($token) < 3) {
                continue;
            }

            $tokens[$token] = true;
        }

        return array_keys($tokens);
    }

    private function resumeStorageDirectory(): string
    {
        return dirname(__DIR__) . '/storage/resumes';
    }

    private function extractResumeText(?array $profile): string
    {
        if (!is_array($profile)) {
            return '';
        }

        $storageName = trim((string) ($profile['resume_storage_name'] ?? ''));
        $filename = trim((string) ($profile['resume_filename'] ?? ''));
        if ($storageName === '' || $filename === '') {
            return '';
        }

        $filePath = $this->resumeStorageDirectory() . '/' . basename($storageName);
        if (!is_file($filePath)) {
            return $filename;
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $text = '';

        if ($extension === 'docx' && class_exists(ZipArchive::class)) {
            $zip = new ZipArchive();
            if ($zip->open($filePath) === true) {
                $xml = $zip->getFromName('word/document.xml');
                if (is_string($xml) && $xml !== '') {
                    $text = strip_tags(html_entity_decode($xml, ENT_QUOTES | ENT_XML1, 'UTF-8'));
                }
                $zip->close();
            }
        } elseif ($extension === 'pdf') {
            $raw = @file_get_contents($filePath, false, null, 0, 1024 * 1024);
            if ($raw !== false) {
                if (preg_match_all('/[A-Za-z]{3,}/', $raw, $matches)) {
                    $text = implode(' ', array_slice(array_unique($matches[0]), 0, 400));
                }
            }
        } else {
            $raw = @file_get_contents($filePath, false, null, 0, 256 * 1024);
            if ($raw !== false) {
                $text = $raw;
            }
        }

        return trim($filename . ' ' . $text);
    }

    private function scoreAvailableJob(array $job, array $profile, array $profileTokens, array $resumeTokens): int
    {
        $score = 0;
        $jobCategoryTokens = $this->normalizeTextTokens($job['service_type'] ?? '');
        $jobLocationTokens = $this->normalizeTextTokens($job['location'] ?? '');
        $profileLocationTokens = $this->normalizeTextTokens($profile['location'] ?? '');

        foreach ($jobCategoryTokens as $token) {
            if (in_array($token, $profileTokens, true)) {
                $score += 42;
            }
            if (in_array($token, $resumeTokens, true)) {
                $score += 28;
            }
        }

        foreach ($jobLocationTokens as $token) {
            if (in_array($token, $profileLocationTokens, true)) {
                $score += 14;
                break;
            }
        }

        $jobTextTokens = array_merge($jobCategoryTokens, $jobLocationTokens);
        foreach (array_unique($jobTextTokens) as $token) {
            if (in_array($token, $profileTokens, true)) {
                $score += 6;
            }
            if (in_array($token, $resumeTokens, true)) {
                $score += 4;
            }
        }

        return $score;
    }

    private function buildJobCategories(array $jobs): array
    {
        $categories = [];

        foreach ($jobs as $job) {
            $label = trim((string) ($job['service_type'] ?? ''));
            if ($label === '') {
                continue;
            }

            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $label) ?? '');
            $slug = trim($slug, '-');
            if ($slug === '') {
                continue;
            }

            if (!isset($categories[$slug])) {
                $categories[$slug] = [
                    'label' => $label,
                    'slug' => $slug,
                    'count' => 0,
                ];
            }

            $categories[$slug]['count']++;
        }

        usort($categories, static function (array $left, array $right): int {
            return $right['count'] <=> $left['count'] ?: strcasecmp($left['label'], $right['label']);
        });

        return $categories;
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

    public function showApplyForm(): void
    {
        requireRole('provider');
        $jobId = sanitizeInput($_GET['id'] ?? '');
        $job = $this->jobs->getJobById($jobId);
        
        if (!$job) {
            setFlash('error', 'Job not found.');
            redirect('/dashboard');
        }

        $providerId = (string) ($_SESSION['user_id'] ?? '');

        renderView('jobs/apply', [
            'title' => 'Apply for Job',
            'job' => $job,
            'csrfToken' => csrfToken(),
            'user' => authUser()
        ]);
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

        try {
            if ($this->applications->createApplication($jobId, $providerId, $coverLetter, '', '')) {
                $job = $this->jobs->getJobById($jobId);
                if ($job) {
                    // Include provider rating snippet in the notification to the parent
                    $profile = $this->servantProfiles->getProfileByUserId($providerId);
                    $ratingVal = isset($profile['rating']) ? number_format((float)$profile['rating'], 1) : '0.0';
                    $ratingCount = isset($profile['rating_count']) ? (int)$profile['rating_count'] : 0;
                    $ratingSnippet = $ratingCount > 0 ? " ({$ratingVal}★, {$ratingCount} reviews)" : '';

                    $this->notifications->create(
                        (string) $job['parent_id'],
                        'application',
                        'New Application',
                        $providerName . $ratingSnippet . ' has applied for your ' . ($job['service_type'] ?? 'job') . ' post.',
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

        $jobs = array_map(static function (mixed $job): array {
            return is_array($job) ? $job : (array) $job;
        }, $this->jobs->getOpenJobs());
        $userId = (string) ($_SESSION['user_id'] ?? '');
        $profile = $userId !== '' ? $this->servantProfiles->getProfileByUserId($userId) : null;
        $profileTokens = [];
        $resumeTokens = [];

        if (is_array($profile)) {
            $profileTokens = array_values(array_unique(array_merge(
                $this->normalizeTextTokens($profile['skills'] ?? []),
                $this->normalizeTextTokens($profile['location'] ?? ''),
                $this->normalizeTextTokens($profile['experience'] ?? ''),
            )));

            $resumeTokens = $this->normalizeTextTokens($this->extractResumeText($profile));
        }

        foreach ($jobs as &$job) {
            $job['match_score'] = $this->scoreAvailableJob($job, is_array($profile) ? $profile : [], $profileTokens, $resumeTokens);
        }
        unset($job);

        $jobCategories = $this->buildJobCategories($jobs);

        renderView('jobs/index', [
            'title' => 'Available Jobs',
            'subtitle' => 'Browse and apply for opportunities near you.',
            'jobs' => $jobs,
            'jobCategories' => $jobCategories,
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
