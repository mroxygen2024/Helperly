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

    private function iterableToCsv(mixed $values): string
    {
        if (!is_iterable($values)) {
            return '';
        }

        $items = [];
        foreach ($values as $value) {
            $text = trim((string) $value);
            if ($text !== '') {
                $items[] = $text;
            }
        }

        return implode(', ', $items);
    }

    public function showAccountForm(): void
    {
        requireAuth();

        $userId = (string) ($_SESSION['user_id'] ?? '');
        if ($userId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        $user = $this->users->findUserById($userId);
        if (!$user) {
            setFlash('error', 'Account not found. Please login again.');
            redirect('/login');
        }

        renderView('profile/account', [
            'title' => 'Account Profile',
            'csrfToken' => csrfToken(),
            'user' => $user,
        ]);
    }

    public function saveAccountProfile(array $payload): void
    {
        requireAuth();

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/profile/account');
        }

        $userId = (string) ($_SESSION['user_id'] ?? '');
        if ($userId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        $name = sanitizeInput($payload['name'] ?? null);
        $phone = sanitizePhone($payload['phone'] ?? null);

        $errors = [];
        if (!validateRequired($name)) {
            $errors[] = 'Name is required.';
        }
        if (!validatePhone($phone)) {
            $errors[] = 'Valid phone number is required.';
        }

        if (!empty($errors)) {
            rememberOldInput([
                'name' => $name,
                'phone' => $phone,
            ]);
            setFlash('error', implode(' ', $errors));
            redirect('/profile/account');
        }

        try {
            $this->users->updateBasicProfile($userId, $name, $phone);
        } catch (Throwable $exception) {
            error_log('Account profile save failed: ' . $exception->getMessage());
            setFlash('error', 'Could not save account profile. Please try again.');
            redirect('/profile/account');
        }

        if (isset($_SESSION['auth_user']) && is_array($_SESSION['auth_user'])) {
            $_SESSION['auth_user']['name'] = $name;
        }

        clearOldInput();
        setFlash('success', 'Account profile saved successfully.');
        redirect('/profile/account');
    }

    public function showServantForm(): void
    {
        requireRole('service_provider');

        $userId = (string) ($_SESSION['user_id'] ?? '');
        if ($userId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        $profile = $this->servantProfiles->getProfileByUserId($userId);
        $skillsText = is_array($profile) ? $this->iterableToCsv($profile['skills'] ?? []) : '';

        renderView('profile/servant', [
            'title' => 'Servant Profile',
            'csrfToken' => csrfToken(),
            'profile' => $profile,
            'skillsText' => $skillsText,
        ]);
    }

    public function saveServantProfile(array $payload): void
    {
        requireRole('service_provider');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/profile/servant');
        }

        $userId = (string) ($_SESSION['user_id'] ?? '');
        if ($userId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        $fullName = sanitizeInput($payload['full_name'] ?? null);
        $nationalId = sanitizeInput($payload['national_id'] ?? null);
        $age = (int) sanitizeInput($payload['age'] ?? null);
        $gender = strtolower(sanitizeInput($payload['gender'] ?? null));
        $skills = sanitizeInput($payload['skills'] ?? null);
        $experience = sanitizeInput($payload['experience'] ?? null);
        $location = sanitizeInput($payload['location'] ?? null);
        $availability = sanitizeInput($payload['availability'] ?? null);
        $hourlyRate = sanitizeInput($payload['hourly_rate'] ?? null);
        $profilePhoto = sanitizeInput($payload['profile_photo'] ?? null);

        $errors = [];
        if (!validateRequired($fullName)) {
            $errors[] = 'Full name is required.';
        }
        if (!validateRequired($nationalId)) {
            $errors[] = 'National ID is required.';
        }
        if ($age < 18 || $age > 80) {
            $errors[] = 'Age must be between 18 and 80.';
        }
        if (!in_array($gender, ['male', 'female', 'other'], true)) {
            $errors[] = 'Gender must be male, female, or other.';
        }
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
        if (!validateRequired($hourlyRate)) {
            $errors[] = 'Hourly rate is required.';
        }
        if (!validateRequired($profilePhoto)) {
            $errors[] = 'Profile photo URL is required.';
        }

        if (!empty($errors)) {
            rememberOldInput([
                'full_name' => $fullName,
                'national_id' => $nationalId,
                'age' => (string) $age,
                'gender' => $gender,
                'skills' => $skills,
                'experience' => $experience,
                'location' => $location,
                'availability' => $availability,
                'hourly_rate' => $hourlyRate,
                'profile_photo' => $profilePhoto,
            ]);
            setFlash('error', implode(' ', $errors));
            redirect('/profile/servant');
        }

        try {
            $this->servantProfiles->createOrUpdateProfile(
                $userId,
                $fullName,
                $nationalId,
                $age,
                $gender,
                $skills,
                $experience,
                $location,
                $availability,
                $hourlyRate,
                $profilePhoto
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
        requireRole('parent');

        $userId = (string) ($_SESSION['user_id'] ?? '');
        if ($userId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        $profile = $this->employerProfiles->getProfileByUserId($userId);

        $emergencyContactsText = is_array($profile) ? $this->iterableToCsv($profile['emergency_contacts'] ?? []) : '';
        $childrenAgesText = is_array($profile) ? $this->iterableToCsv($profile['children_ages'] ?? []) : '';
        $preferencesText = is_array($profile) ? $this->iterableToCsv($profile['preferences'] ?? []) : '';

        renderView('profile/employer', [
            'title' => 'Employer Profile',
            'csrfToken' => csrfToken(),
            'profile' => $profile,
            'emergencyContactsText' => $emergencyContactsText,
            'childrenAgesText' => $childrenAgesText,
            'preferencesText' => $preferencesText,
        ]);
    }

    public function saveEmployerProfile(array $payload): void
    {
        requireRole('parent');

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
        if (!validateRequired($emergencyContacts)) {
            $errors[] = 'Emergency contacts are required.';
        }
        if (!validateRequired($childrenAges)) {
            $errors[] = 'Children ages are required.';
        }
        if (!validateRequired($preferences)) {
            $errors[] = 'Preferences are required.';
        }

        if (!empty($errors)) {
            rememberOldInput([
                'address' => $address,
                'location' => $location,
                'emergency_contacts' => $emergencyContacts,
                'children_ages' => $childrenAges,
                'preferences' => $preferences,
            ]);
            setFlash('error', implode(' ', $errors));
            redirect('/profile/employer');
        }

        try {
            $this->employerProfiles->createOrUpdateProfile(
                $userId,
                $address,
                $location,
                $emergencyContacts,
                $childrenAges,
                $preferences
            );
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
        requireRole(['parent', 'administrator']);

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
                'name' => (string) (($profile['full_name'] ?? '') !== '' ? $profile['full_name'] : ($user['name'] ?? 'Unnamed servant')),
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
