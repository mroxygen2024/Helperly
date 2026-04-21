<?php

declare(strict_types=1);

use MongoDB\BSON\UTCDateTime;

/*
 |--------------------------------------------------------------------------
 | controllers/ purpose
 |--------------------------------------------------------------------------
 | Handle HTTP input, coordinate models, then choose the view to render.
 */

class HireRequestController
{
    private HireRequest $hireRequests;
    private User $users;
    private ServantProfile $servantProfiles;

    public function __construct()
    {
        $this->hireRequests = new HireRequest();
        $this->users = new User();
        $this->servantProfiles = new ServantProfile();
    }

    public function createRequest(array $payload): void
    {
        requireRole('parent');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/servants');
        }

        $employerId = (string) ($_SESSION['user_id'] ?? '');
        $servantId = sanitizeInput($payload['servant_id'] ?? null);

        if ($employerId === '' || $servantId === '') {
            setFlash('error', 'Missing employer or servant id.');
            redirect('/servants');
        }

        $servant = $this->users->findUserById($servantId);
        if (!$servant || normalizeRole((string) ($servant['role'] ?? '')) !== 'service_provider') {
            setFlash('error', 'Selected servant account was not found.');
            redirect('/servants');
        }

        if (!$this->servantProfiles->isApprovedByUserId($servantId)) {
            setFlash('error', 'Only approved service providers can be booked.');
            redirect('/servants');
        }

        try {
            $this->hireRequests->createRequest($employerId, $servantId);
            setFlash('success', 'Hire request sent successfully.');
        } catch (RuntimeException $exception) {
            setFlash('error', $exception->getMessage());
        } catch (Throwable $exception) {
            error_log('Hire request creation failed: ' . $exception->getMessage());
            setFlash('error', 'Could not create hire request. Please try again.');
        }

        redirect('/servants');
    }

    public function showIncomingRequests(): void
    {
        requireRole(['service_provider', 'administrator']);

        $servantId = (string) ($_SESSION['user_id'] ?? '');
        if ($servantId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        if (!$this->servantProfiles->isApprovedByUserId($servantId)) {
            setFlash('error', 'Only approved service providers can access hire requests.');
            redirect('/profile/servant');
        }

        $requests = $this->hireRequests->getRequestsForServant($servantId);

        $employerIds = [];
        foreach ($requests as $request) {
            $employerId = (string) ($request['employer_id'] ?? '');
            if ($employerId !== '') {
                $employerIds[] = $employerId;
            }
        }

        $employersById = $this->users->findUsersByIds($employerIds);

        $incomingRequests = [];
        foreach ($requests as $request) {
            $employerId = (string) ($request['employer_id'] ?? '');
            $createdAt = $request['created_at'] ?? null;
            $createdAtText = 'Unknown';

            if ($createdAt instanceof UTCDateTime) {
                $dateTime = $createdAt->toDateTime();
                $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get() ?: 'UTC'));
                $createdAtText = $dateTime->format('Y-m-d H:i');
            }

            $incomingRequests[] = [
                'request' => $request,
                'employer' => $employersById[$employerId] ?? [],
                'created_at_text' => $createdAtText,
            ];
        }

        renderView('servants/requests', [
            'title' => 'Incoming Hire Requests',
            'csrfToken' => csrfToken(),
            'incomingRequests' => $incomingRequests,
        ]);
    }

    public function updateRequestStatus(array $payload): void
    {
        requireRole('service_provider');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/servant/requests');
        }

        $servantId = (string) ($_SESSION['user_id'] ?? '');
        $requestId = sanitizeInput($payload['request_id'] ?? null);
        $status = sanitizeInput($payload['status'] ?? null);

        if ($servantId === '' || $requestId === '') {
            setFlash('error', 'Request data is missing.');
            redirect('/servant/requests');
        }

        if (!in_array($status, ['accepted', 'rejected'], true)) {
            setFlash('error', 'Invalid status update.');
            redirect('/servant/requests');
        }

        if (!$this->servantProfiles->isApprovedByUserId($servantId)) {
            setFlash('error', 'Only approved service providers can process hire requests.');
            redirect('/profile/servant');
        }

        $request = $this->hireRequests->getRequestById($requestId);
        if (!$request || (string) ($request['servant_id'] ?? '') !== $servantId) {
            setFlash('error', 'You are not allowed to update this request.');
            redirect('/servant/requests');
        }

        try {
            $updated = $this->hireRequests->updateRequestStatus($requestId, $status);
            if ($updated) {
                setFlash('success', 'Request status updated to ' . $status . '.');
            } else {
                setFlash('error', 'Request status could not be updated. It may already be processed.');
            }
        } catch (Throwable $exception) {
            error_log('Hire request status update failed: ' . $exception->getMessage());
            setFlash('error', 'Could not update request status. Please try again.');
        }

        redirect('/servant/requests');
    }
}
