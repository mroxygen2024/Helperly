<section class="card">
    <h1>Servant Profile</h1>
    <p class="muted">Create or update your service provider profile details.</p>

    <form action="/profile/servant" method="POST" enctype="multipart/form-data" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <label for="full_name">Full Name</label>
        <input id="full_name" name="full_name" type="text" value="<?= escape(old('full_name', (string) ($profile['full_name'] ?? ''))); ?>" required>

        <label for="national_id">National ID</label>
        <input id="national_id" name="national_id" type="text" value="<?= escape(old('national_id', (string) ($profile['national_id'] ?? ''))); ?>" required>

        <label for="age">Age</label>
        <input id="age" name="age" type="number" min="18" max="80" value="<?= escape(old('age', (string) ($profile['age'] ?? ''))); ?>" required>

        <label for="gender">Gender</label>
        <?php $selectedGender = old('gender', (string) ($profile['gender'] ?? '')); ?>
        <select id="gender" name="gender" required>
            <option value="">Select gender</option>
            <option value="male" <?= $selectedGender === 'male' ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?= $selectedGender === 'female' ? 'selected' : ''; ?>>Female</option>
            <option value="other" <?= $selectedGender === 'other' ? 'selected' : ''; ?>>Other</option>
        </select>

        <label for="skills">Skills (comma separated)</label>
        <input id="skills" name="skills" type="text" value="<?= escape(old('skills', (string) ($skillsText ?? ''))); ?>" required>

        <label for="experience">Experience</label>
        <input id="experience" name="experience" type="text" value="<?= escape(old('experience', (string) ($profile['experience'] ?? ''))); ?>" required>

        <label for="location">Location</label>
        <input id="location" name="location" type="text" value="<?= escape(old('location', (string) ($profile['location'] ?? ''))); ?>" required>

        <label for="availability">Availability</label>
        <input id="availability" name="availability" type="text" value="<?= escape(old('availability', (string) ($profile['availability'] ?? ''))); ?>" required>

        <label for="hourly_rate">Hourly Rate</label>
        <input id="hourly_rate" name="hourly_rate" type="text" value="<?= escape(old('hourly_rate', (string) ($profile['hourly_rate'] ?? ''))); ?>" placeholder="e.g. 500 BDT/hour" required>

        <label for="profile_photo">Profile Photo URL</label>
        <input id="profile_photo" name="profile_photo" type="url" value="<?= escape(old('profile_photo', (string) ($profile['profile_photo'] ?? ''))); ?>" placeholder="https://..." required>

        <label for="fayda_id_front">Fayda ID Front (JPG/PNG, max 5MB)</label>
        <input id="fayda_id_front" name="fayda_id_front" type="file" accept="image/jpeg,image/png" required>
        <?php if (!empty($profile['fayda_id_front_url'])): ?>
            <p class="muted">Current: <a href="<?= escape((string) $profile['fayda_id_front_url']); ?>" target="_blank" rel="noopener noreferrer">View uploaded front image</a></p>
        <?php endif; ?>

        <label for="fayda_id_back">Fayda ID Back (JPG/PNG, max 5MB)</label>
        <input id="fayda_id_back" name="fayda_id_back" type="file" accept="image/jpeg,image/png" required>
        <?php if (!empty($profile['fayda_id_back_url'])): ?>
            <p class="muted">Current: <a href="<?= escape((string) $profile['fayda_id_back_url']); ?>" target="_blank" rel="noopener noreferrer">View uploaded back image</a></p>
        <?php endif; ?>

        <label for="selfie">Selfie (JPG/PNG, max 5MB)</label>
        <input id="selfie" name="selfie" type="file" accept="image/jpeg,image/png" required>
        <?php if (!empty($profile['selfie_url'])): ?>
            <p class="muted">Current: <a href="<?= escape((string) $profile['selfie_url']); ?>" target="_blank" rel="noopener noreferrer">View uploaded selfie</a></p>
        <?php endif; ?>

        <button type="submit" class="btn">Save Profile</button>
    </form>
</section>
