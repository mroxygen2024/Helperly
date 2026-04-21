<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/ purpose
 |--------------------------------------------------------------------------
 | Handle HTTP input, coordinate models, then choose the view to render.
 */

class ProfileController
{
    private ServantProfile $servantProfiles;
    private EmployerProfile $employerProfiles;
    private User $users;

    public function __construct()
    {
        $this->servantProfiles = new ServantProfile();
        $this->employerProfiles = new EmployerProfile();
        $this->users = new User();
    }

    public function showServantForm(): void
    {
        requireRole('servant');

        $userId = (string) ($_SESSION['user_id'] ?? '');
        if ($userId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        $profile = $this->servantProfiles->getProfileByUserId($userId);
        $skillsText = '';
        if (is_array($profile) && isset($profile['skills']) && is_iterable($profile['skills'])) {
            $skills = [];
            foreach ($profile['skills'] as $skill) {
                $skills[] = (string) $skill;
            }
            $skillsText = implode(', ', $skills);
        }

        renderView('profile/servant', [
            'title' => 'Servant Profile',
            'csrfToken' => csrfToken(),
            'profile' => $profile,
            'skillsText' => $skillsText,
        ]);
    }

    public function saveServantProfile(array $payload): void
    {
        requireRole('servant');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/profile/servant');
        }

        $userId = (string) ($_SESSION['user_id'] ?? '');
        if ($userId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        $skills = sanitizeInput($payload['skills'] ?? null);
        $experience = sanitizeInput($payload['experience'] ?? null);
        $location = sanitizeInput($payload['location'] ?? null);
        $availability = sanitizeInput($payload['availability'] ?? null);

        $errors = [];
        if (!validateRequired($skills)) {
            $errors[] = 'Skills are required.';
        }
        if (!validateRequired($experience)) {
            $errors[] = 'Experience is required.';
        }
        if (!validateRequired($location)) {
            $errors[] = 'Location is required.';
        }
        if (!validateRequired($availability)) {
            $errors[] = 'Availability is required.';
        }

        if (!empty($errors)) {
            rememberOldInput([
                'skills' => $skills,
                'experience' => $experience,
                'location' => $location,
                'availability' => $availability,
            ]);
            setFlash('error', implode(' ', $errors));
            redirect('/profile/servant');
        }

        try {
            $this->servantProfiles->createOrUpdateProfile(
                $userId,
                $skills,
                $experience,
                $location,
                $availability
            );
        } catch (Throwable $exception) {
            error_log('Servant profile save failed: ' . $exception->getMessage());
            setFlash('error', 'Could not save profile. Please try again.');
            redirect('/profile/servant');
        }

        clearOldInput();
        setFlash('success', 'Servant profile saved successfully.');
        redirect('/profile/servant');
    }

    public function showEmployerForm(): void
    {
        requireRole('employer');

        $userId = (string) ($_SESSION['user_id'] ?? '');
        if ($userId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        $profile = $this->employerProfiles->getProfileByUserId($userId);

        renderView('profile/employer', [
            'title' => 'Employer Profile',
            'csrfToken' => csrfToken(),
            'profile' => $profile,
        ]);
    }

    public function saveEmployerProfile(array $payload): void
    {
        requireRole('employer');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/profile/employer');
        }

        $userId = (string) ($_SESSION['user_id'] ?? '');
        if ($userId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        $address = sanitizeInput($payload['address'] ?? null);
        $location = sanitizeInput($payload['location'] ?? null);

        $errors = [];
        if (!validateRequired($address)) {
            $errors[] = 'Address is required.';
        }
        if (!validateRequired($location)) {
            $errors[] = 'Location is required.';
        }

        if (!empty($errors)) {
            rememberOldInput([
                'address' => $address,
                'location' => $location,
            ]);
            setFlash('error', implode(' ', $errors));
            redirect('/profile/employer');
        }

        try {
            $this->employerProfiles->createOrUpdateProfile($userId, $address, $location);
        } catch (Throwable $exception) {
            error_log('Employer profile save failed: ' . $exception->getMessage());
            setFlash('error', 'Could not save profile. Please try again.');
            redirect('/profile/employer');
        }

        clearOldInput();
        setFlash('success', 'Employer profile saved successfully.');
        redirect('/profile/employer');
    }

    public function listServants(array $query): void
    {
        requireRole('employer');

        $location = sanitizeInput($query['location'] ?? null);
        $skill = sanitizeInput($query['skill'] ?? null);

        $profiles = $this->servantProfiles->findProfilesByFilters($location, $skill, 50);

        $userIds = [];
        foreach ($profiles as $profile) {
            $userId = (string) ($profile['user_id'] ?? '');
            if ($userId !== '') {
                $userIds[] = $userId;
            }
        }

        $usersById = $this->users->findUsersByIds($userIds);

        $servants = [];
        foreach ($profiles as $profile) {
            $userId = (string) ($profile['user_id'] ?? '');
            $user = $usersById[$userId] ?? [];

            $servants[] = [
                'profile' => $profile,
                'user' => $user,
                'name' => (string) ($user['name'] ?? 'Unnamed servant'),
                'phone' => (string) ($user['phone'] ?? 'Not provided'),
            ];
        }

        renderView('servants/index', [
            'title' => 'Servant Directory',
            'csrfToken' => csrfToken(),
            'user' => authUser(),
            'servants' => $servants,
            'filters' => [
                'location' => $location,
                'skill' => $skill,
            ],
        ]);
    }
}
