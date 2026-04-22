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

    public function __construct()
    {
        $this->jobs = new Job();
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
}
