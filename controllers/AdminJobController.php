<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/AdminJobController.php
 |--------------------------------------------------------------------------
 | Handles administrative job management tasks.
 */

class AdminJobController
{
    private Job $jobs;
    private User $users;

    public function __construct()
    {
        $this->jobs = new Job();
        $this->users = new User();
    }

    public function index(): void
    {
        requireRole('administrator');

        $statusFilter = sanitizeInput($_GET['status'] ?? '');
        
        $jobs = $this->jobs->getAllJobs(200);
        $enrichedJobs = [];

        foreach ($jobs as $job) {
            $jobArray = (array)$job;
            
            // Apply status filter if provided
            if ($statusFilter !== '' && ($jobArray['status'] ?? 'open') !== $statusFilter) {
                continue;
            }

            if (isset($job['parent_id'])) {
                $jobArray['parent'] = $this->users->findUserById((string)$job['parent_id']);
            }
            if (isset($job['selected_provider_id'])) {
                $jobArray['provider'] = $this->users->findUserById((string)$job['selected_provider_id']);
            }
            $enrichedJobs[] = $jobArray;
        }

        renderView('admin/jobs', [
            'title' => 'Job Management',
            'jobs' => $enrichedJobs,
            'statusFilter' => $statusFilter,
            'currentUser' => authUser(),
        ]);
    }

    public function showDetail(): void
    {
        requireRole('administrator');

        $jobId = sanitizeInput($_GET['id'] ?? '');
        if (!$jobId) {
            setFlash('error', 'Job ID is required.');
            redirect('/admin/jobs');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job) {
            setFlash('error', 'Job not found.');
            redirect('/admin/jobs');
        }

        $jobArray = (array)$job;
        if (isset($job['parent_id'])) {
            $jobArray['parent'] = $this->users->findUserById((string)$job['parent_id']);
        }
        if (isset($job['selected_provider_id'])) {
            $jobArray['provider'] = $this->users->findUserById((string)$job['selected_provider_id']);
        }

        renderView('admin/job_detail', [
            'title' => 'Job Details',
            'job' => $jobArray,
            'currentUser' => authUser(),
        ]);
    }
}

// NOTE: We convert job objects to arrays and enrich them with related user data (parent & provider)
// so the view can directly access all required information without additional queries.