<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/ReviewController.php
 |--------------------------------------------------------------------------
 | Handles job reviews and ratings.
 */

class ReviewController
{
    private Review $reviews;
    private Job $jobs;
    private Notification $notifications;

    public function __construct()
    {
        $this->reviews = new Review();
        $this->jobs = new Job();
        $this->notifications = new Notification();
    }

    public function store(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/dashboard');
        }

        $jobId = sanitizeInput($payload['job_id'] ?? null);
        $rating = (int) ($payload['rating'] ?? 0);
        $reviewText = sanitizeInput($payload['review_text'] ?? '');
        $parentId = (string) ($_SESSION['user_id'] ?? '');
        $parentName = (string) ($_SESSION['auth_user']['name'] ?? 'A parent');

        if (!$jobId || $rating < 1 || $rating > 5) {
            setFlash('error', 'Please provide a valid rating between 1 and 5.');
            redirect('/dashboard');
        }

        if (empty($reviewText)) {
            setFlash('error', 'Review text cannot be empty.');
            redirect('/dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || (string) $job['parent_id'] !== $parentId) {
            setFlash('error', 'Unauthorized access.');
            redirect('/dashboard');
        }

        if ((string) ($job['status'] ?? '') !== 'completed') {
            setFlash('error', 'Reviews can only be submitted for completed jobs.');
            redirect('/dashboard');
        }

        $providerId = (string) ($job['selected_provider_id'] ?? '');

        try {
            $success = $this->reviews->createReview($jobId, $providerId, $parentId, $rating, $reviewText);
            if ($success) {
                // Recalculate and update cached rating in profile
                $agg = $this->reviews->calculateAverageRating($providerId);
                $servantProfile = new ServantProfile();
                $servantProfile->updateCachedRating($providerId, (float)$agg['average'], (int)$agg['count']);

                // Notify provider
                $this->notifications->create(
                    $providerId,
                    'review',
                    'New Review Received!',
                    $parentName . ' gave you ' . $rating . ' stars for ' . ($job['service_type'] ?? 'the job') . '.',
                    '/profile/servant'
                );

                setFlash('success', 'Thank you for your feedback!');
            } else {
                setFlash('error', 'You have already reviewed this job.');
            }
        } catch (Throwable $exception) {
            error_log('Review creation failed: ' . $exception->getMessage());
            setFlash('error', 'Could not save review. Please try again.');
        }

        redirect('/dashboard');
    }
}
