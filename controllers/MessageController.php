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
        $role = normalizeRole((string) ($_SESSION['role'] ?? ''));

        $jobId = sanitizeInput($query['job_id'] ?? '');
        
        // If no job_id is specified, show the Inbox (list of conversations)
        if (!$jobId) {
            $db = getMongoDatabase();
            
            // Query jobs where user is parent or selected provider
            $queryCriteria = [
                '$or' => [
                    ['parent_id' => new \MongoDB\BSON\ObjectId($userId)],
                    ['selected_provider_id' => new \MongoDB\BSON\ObjectId($userId)]
                ],
                // Typically messages are for active or completed jobs
                'status' => ['$in' => ['active', 'completed']]
            ];
            
            $cursor = $db->selectCollection('jobs')->find($queryCriteria, ['sort' => ['updated_at' => -1]]);
            $jobs = iterator_to_array($cursor, false);
            
            $conversations = [];
            foreach ($jobs as $job) {
                $parentId = (string)($job['parent_id'] ?? '');
                $providerId = (string)($job['selected_provider_id'] ?? '');
                $otherId = ($userId === $parentId) ? $providerId : $parentId;
                
                if ($otherId) {
                    $otherParty = $this->users->findUserById($otherId);
                    
                    // Get last message
                    $lastMsgDoc = $db->selectCollection('messages')->findOne(
                        ['job_id' => $job['_id']],
                        ['sort' => ['created_at' => -1]]
                    );
                    
                    $conversations[] = [
                        'job' => $job,
                        'other_party' => $otherParty,
                        'last_message' => $lastMsgDoc['message'] ?? null
                    ];
                }
            }

            renderView('messages/inbox', [
                'title' => 'Inbox',
                'conversations' => $conversations,
                'userId' => $userId
            ]);
            return;
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job) {
            setFlash('error', 'Job not found.');
            redirect('/dashboard');
        }

        $parentId = (string) ($job['parent_id'] ?? '');
        $providerId = (string) ($job['selected_provider_id'] ?? '');

        if ($userId !== $parentId && $userId !== $providerId) {
            setFlash('error', 'You are not authorized to view this chat.');
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
