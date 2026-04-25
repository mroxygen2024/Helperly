<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/ purpose
 |--------------------------------------------------------------------------
 | Handle HTTP input, coordinate models, then choose the view to render.
 */

class MarketplaceController
{
    private Listing $listings;
    private Job $jobs;
    private JobApplication $applications;

    public function __construct()
    {
        $this->listings = new Listing();
        $this->jobs = new Job();
        $this->applications = new JobApplication();
    }

    public function index(): void
    {
        $this->listings->seedIfEmpty();

        renderView('marketplace/index', [
            'title' => 'Marketplace',
            'listings' => $this->listings->getLatest(20),
            'user' => authUser(),
        ]);
    }

    public function employerDashboard(): void
    {
        requireRole('parent');

        $parentId = (string) ($_SESSION['user_id'] ?? '');
        $jobs = $this->jobs->getJobsByParentId($parentId);

        $jobsWithApplicants = [];
        $userModel = new User();
        foreach ($jobs as $job) {
            $jobId = (string) $job['_id'];
            $jobArray = (array) $job;
            $jobArray['applicants'] = $this->applications->getApplicationsForJob($jobId);

            if (isset($job['selected_provider_id'])) {
                $jobArray['selected_provider'] = $userModel->findUserById((string) $job['selected_provider_id']);
            }

            $jobsWithApplicants[] = $jobArray;
        }

        renderView('marketplace/index', [
            'title' => 'Employer Dashboard',
            'listings' => $this->listings->getLatest(20),
            'jobs' => $jobsWithApplicants,
            'user' => authUser(),
        ]);
    }

    public function servantDashboard(): void
    {
        requireRole('service_provider');
        $providerId = (string) ($_SESSION['user_id'] ?? '');

        renderView('marketplace/index', [
            'title' => 'Servant Dashboard',
            'listings' => $this->listings->getLatest(20),
            'jobs' => $this->jobs->getOpenJobs(),
            'activeJobs' => $this->jobs->getActiveJobsByProvider($providerId),
            'appliedJobIds' => $this->applications->getAppliedJobIdsByProvider($providerId),
            'user' => authUser(),
        ]);
    }

    public function adminDashboard(): void
    {
        requireRole('administrator');

        renderView('marketplace/index', [
            'title' => 'Administrator Dashboard',
            'listings' => $this->listings->getLatest(20),
            'user' => authUser(),
        ]);
    }
}
