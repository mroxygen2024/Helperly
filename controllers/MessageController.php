<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/MessageController.php
 |--------------------------------------------------------------------------
 | Handle messaging interface and interactions.
 */

class MessageController
{
    private Message $messages;
    private Job $jobs;
    private User $users;

    public function __construct()
    {
        $this->messages = new Message();
        $this->jobs = new Job();
        $this->users = new User();
    }

    public function index(array $query): void
    {
        requireAuth();
        $userId = (string) ($_SESSION['user_id'] ?? '');

        $jobId = sanitizeInput($query['job_id'] ?? '');
        if (!$jobId) {
            setFlash('error', 'No job specified for chat.');
            redirect('/dashboard');
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job) {
            setFlash('error', 'Job not found.');
            redirect('/dashboard');
        }

        $parentId = (string) ($job['parent_id'] ?? '');
        $providerId = (string) ($job['selected_provider_id'] ?? '');

        if ($userId !== $parentId && $userId !== $providerId) {
            setFlash('error', 'You are not authorized to view this chat. Chat is only available for active/booked jobs between the parent and provider.');
            redirect('/dashboard');
        }

        $otherPartyId = ($userId === $parentId) ? $providerId : $parentId;
        if (!$otherPartyId) {
            setFlash('error', 'A provider has not been selected for this job yet.');
            redirect('/dashboard');
        }

        $otherParty = $this->users->findUserById($otherPartyId);

        $messages = $this->messages->getMessagesByJobId($jobId);

        renderView('messages/index', [
            'title' => 'Job Conversation',
            'csrfToken' => csrfToken(),
            'job' => $job,
            'messages' => $messages,
            'otherParty' => $otherParty,
            'userId' => $userId,
        ]);
    }

    public function store(array $payload): void
    {
        requireAuth();
        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token.');
            redirect('/dashboard');
        }

        $userId = (string) ($_SESSION['user_id'] ?? '');
        $jobId = sanitizeInput($payload['job_id'] ?? '');
        $messageText = sanitizeInput($payload['message'] ?? '');

        if (!$jobId || trim($messageText) === '') {
            setFlash('error', 'Message cannot be empty.');
            redirect('/messages?job_id=' . $jobId);
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job) {
            setFlash('error', 'Job not found.');
            redirect('/dashboard');
        }

        $parentId = (string) ($job['parent_id'] ?? '');
        $providerId = (string) ($job['selected_provider_id'] ?? '');

        if ($userId !== $parentId && $userId !== $providerId) {
            setFlash('error', 'You are not authorized to send messages for this job.');
            redirect('/dashboard');
        }

        $receiverId = ($userId === $parentId) ? $providerId : $parentId;
        if (!$receiverId) {
            setFlash('error', 'A provider has not been selected for this job yet.');
            redirect('/dashboard');
        }
        
        try {
            $this->messages->sendMessage($userId, $receiverId, $jobId, $messageText);
        } catch (Throwable $exception) {
            error_log('Message send failed: ' . $exception->getMessage());
            setFlash('error', 'Could not send message.');
        }

        redirect('/messages?job_id=' . $jobId);
    }
}
