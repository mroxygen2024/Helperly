<section class="card">
    <h1>Servant Profile</h1>
    <p class="muted">Create or update your profile details.</p>

    <form action="/profile/servant" method="POST" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <label for="skills">Skills (comma separated)</label>
        <input id="skills" name="skills" type="text" value="<?= escape(old('skills', (string) ($skillsText ?? ''))); ?>" required>

        <label for="experience">Experience</label>
        <input id="experience" name="experience" type="text" value="<?= escape(old('experience', (string) ($profile['experience'] ?? ''))); ?>" required>

        <label for="location">Location</label>
        <input id="location" name="location" type="text" value="<?= escape(old('location', (string) ($profile['location'] ?? ''))); ?>" required>

        <label for="availability">Availability</label>
        <input id="availability" name="availability" type="text" value="<?= escape(old('availability', (string) ($profile['availability'] ?? ''))); ?>" required>

        <button type="submit" class="btn">Save Profile</button>
    </form>
</section>
