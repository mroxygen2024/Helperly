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
    private ServantProfile $servantProfiles;

    public function __construct()
    {
        $this->listings = new Listing();
        $this->jobs = new Job();
        $this->applications = new JobApplication();
        $this->servantProfiles = new ServantProfile();
    }

    public function index(): void
    {
        $this->listings->seedIfEmpty();

        $featuredProviders = $this->servantProfiles->findProfilesByFilters([], 6);

        renderView('marketplace/index', [
            'title' => 'Marketplace',
            'listings' => $this->listings->getLatest(20),
            'featuredProviders' => $featuredProviders,
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
        $paymentModel = new Payment();
        $reviewModel = new Review();

        foreach ($jobs as $job) {
            $jobId = (string) $job['_id'];
            $jobArray = (array) $job;
            $jobArray['applicants'] = $this->applications->getApplicationsForJob($jobId);

            if (isset($job['selected_provider_id'])) {
                $jobArray['selected_provider'] = $userModel->findUserById((string) $job['selected_provider_id']);
            }

            $jobArray['payment'] = $paymentModel->getPaymentByJobId($jobId);
            $jobArray['review'] = $reviewModel->getReviewByJobId($jobId);

            $jobsWithApplicants[] = $jobArray;
        }

        renderView('marketplace/dashboard_parent', [
            'title' => 'Parent Dashboard',
            'jobs' => $jobsWithApplicants,
            'user' => authUser(),
        ]);
    }

    public function servantDashboard(): void
    {
        requireRole('provider');
        $providerId = (string) ($_SESSION['user_id'] ?? '');

        $servantProfileModel = new ServantProfile();
        $profile = $servantProfileModel->getProfileByUserId($providerId);

        renderView('marketplace/dashboard_provider', [
            'title' => 'Provider Dashboard',
            'jobs' => $this->jobs->getOpenJobs(),
            'activeJobs' => $this->jobs->getActiveJobsByProvider($providerId),
            'appliedJobIds' => $this->applications->getAppliedJobIdsByProvider($providerId),
            'applicationsList' => $this->applications->getApplicationsByProvider($providerId),
            'user' => authUser(),
            'profile' => $profile,
        ]);
    }

    public function adminDashboard(): void
    {
        requireRole('administrator');

        $userModel = new User();
        $servantProfileModel = new ServantProfile();

        $stats = [
            'total_users' => $userModel->countUsers(),
            'total_jobs' => $this->jobs->countJobs(),
            'verified_providers' => $servantProfileModel->countVerifiedProviders(),
            'total_providers' => $servantProfileModel->countTotalProviders(),
        ];

        $recentJobs = $this->jobs->getAllJobs(20);
        $enrichedJobs = [];
        foreach ($recentJobs as $job) {
            $jobArray = (array)$job;
            if (isset($job['parent_id'])) {
                $jobArray['parent'] = $userModel->findUserById((string)$job['parent_id']);
            }
            if (isset($job['selected_provider_id'])) {
                $jobArray['provider'] = $userModel->findUserById((string)$job['selected_provider_id']);
            }
            $enrichedJobs[] = $jobArray;
        }

        renderView('marketplace/dashboard_admin', [
            'title' => 'Admin Dashboard',
            'user' => authUser(),
            'stats' => $stats,
            'recentJobs' => $enrichedJobs,
            'adminSections' => [
                ['title' => 'User Management', 'link' => '/admin/users', 'icon_name' => 'group'],
                ['title' => 'Provider Verifications', 'link' => '/admin/verifications', 'icon_name' => 'verified_user'],
                ['title' => 'All Jobs', 'link' => '/admin/jobs', 'icon_name' => 'work'],
            ]
        ]);
    }
}
