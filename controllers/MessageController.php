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
    private Notification $notifications;

    public function __construct()
    {
        $this->messages = new Message();
        $this->jobs = new Job();
        $this->users = new User();
        $this->notifications = new Notification();
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
            $rawJobs = iterator_to_array($cursor, false);
            
            $jobs = [];
            foreach ($rawJobs as $rj) {
                $jobs[] = (array)$rj;
            }

            $conversations = [];
            foreach ($jobs as $job) {
                $parentId = (string)($job['parent_id'] ?? '');
                $providerId = (string)($job['selected_provider_id'] ?? '');
                $otherId = ($userId === $parentId) ? $providerId : $parentId;
                
                if ($otherId) {
                    $otherParty = $this->users->findUserById($otherId);
                    
                    // Get last message
                    $lastMsgDocRaw = $db->selectCollection('messages')->findOne(
                        ['job_id' => $job['_id']],
                        ['sort' => ['created_at' => -1]]
                    );
                    $lastMsgDoc = $lastMsgDocRaw ? (array)$lastMsgDocRaw : null;

                    // Get unread count for this conversation
                    $unreadCount = (int) $db->selectCollection('messages')->countDocuments([
                        'job_id' => $job['_id'],
                        'receiver_id' => new \MongoDB\BSON\ObjectId($userId),
                        'is_read' => false
                    ]);
                    
                    $conversations[] = [
                        'job' => $job,
                        'other_party' => $otherParty,
                        'last_message' => $lastMsgDoc['message'] ?? null,
                        'unread_count' => $unreadCount,
                        'last_message_time' => $lastMsgDoc['created_at'] ?? null
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

        // Mark as read
        $this->messages->markAsRead($jobId, $userId);

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

    public function apiFetch(array $query): void
    {
        requireAuth();
        $userId = (string) ($_SESSION['user_id'] ?? '');
        $jobId = sanitizeInput($query['job_id'] ?? '');

        if (!$jobId) {
            jsonResponse(['error' => 'Job ID required'], 400);
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job || ((string)$job['parent_id'] !== $userId && (string)$job['selected_provider_id'] !== $userId)) {
            jsonResponse(['error' => 'Unauthorized'], 403);
        }

        // Mark as read
        $this->messages->markAsRead($jobId, $userId);

        $messages = $this->messages->getMessagesByJobId($jobId);
        $formattedMessages = [];
        foreach ($messages as $msg) {
            $formattedMessages[] = [
                'id' => (string) $msg['_id'],
                'sender_id' => (string) $msg['sender_id'],
                'message' => $msg['message'],
                'created_at' => $msg['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $msg['created_at']->toDateTime()->format('g:i A') : '',
                'is_sender' => (string)$msg['sender_id'] === $userId
            ];
        }

        jsonResponse(['messages' => $formattedMessages]);
    }

    public function store(array $payload): void
    {
        requireAuth();
        $isAjax = isset($payload['ajax']) && $payload['ajax'] === '1';

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            if ($isAjax) jsonResponse(['error' => 'Invalid CSRF token'], 400);
            setFlash('error', 'Invalid request token.');
            redirect('/dashboard');
        }

        $userId = (string) ($_SESSION['user_id'] ?? '');
        $userName = (string) ($_SESSION['auth_user']['name'] ?? 'Someone');
        $jobId = sanitizeInput($payload['job_id'] ?? '');
        $messageText = sanitizeInput($payload['message'] ?? '');

        if (!$jobId || trim($messageText) === '') {
            if ($isAjax) jsonResponse(['error' => 'Empty message'], 400);
            setFlash('error', 'Message cannot be empty.');
            redirect('/messages?job_id=' . $jobId);
        }

        $job = $this->jobs->getJobById($jobId);
        if (!$job) {
            if ($isAjax) jsonResponse(['error' => 'Job not found'], 404);
            setFlash('error', 'Job not found.');
            redirect('/dashboard');
        }

        $parentId = (string) ($job['parent_id'] ?? '');
        $providerId = (string) ($job['selected_provider_id'] ?? '');

        if ($userId !== $parentId && $userId !== $providerId) {
            if ($isAjax) jsonResponse(['error' => 'Unauthorized'], 403);
            setFlash('error', 'You are not authorized to send messages for this job.');
            redirect('/dashboard');
        }

        $receiverId = ($userId === $parentId) ? $providerId : $parentId;
        if (!$receiverId) {
            if ($isAjax) jsonResponse(['error' => 'No provider selected'], 400);
            setFlash('error', 'A provider has not been selected for this job yet.');
            redirect('/dashboard');
        }
        
        try {
            if ($this->messages->sendMessage($userId, $receiverId, $jobId, $messageText)) {
                // Send Notification
                $this->notifications->create(
                    $receiverId,
                    'message',
                    'New message from ' . $userName,
                    mb_strimwidth($messageText, 0, 50, '...'),
                    '/messages?job_id=' . $jobId
                );

                if ($isAjax) {
                    jsonResponse(['success' => true]);
                }
            }
        } catch (Throwable $exception) {
            error_log('Message send failed: ' . $exception->getMessage());
            if ($isAjax) jsonResponse(['error' => 'Database error: ' . $exception->getMessage()], 500);
            setFlash('error', 'Could not send message: ' . $exception->getMessage());
        }

        redirect('/messages?job_id=' . $jobId);
    }
}
