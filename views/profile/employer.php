<section class="card">
    <h1>Employer Profile</h1>
    <p class="muted">Create or update your parent profile.</p>

    <form action="/profile/employer" method="POST" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <label for="address">Address</label>
        <input id="address" name="address" type="text" value="<?= escape(old('address', (string) ($profile['address'] ?? ''))); ?>" required>

        <label for="location">Location</label>
        <input id="location" name="location" type="text" value="<?= escape(old('location', (string) ($profile['location'] ?? ''))); ?>" required>

        <label for="emergency_contacts">Emergency Contacts (comma separated)</label>
        <input id="emergency_contacts" name="emergency_contacts" type="text" value="<?= escape(old('emergency_contacts', (string) ($emergencyContactsText ?? ''))); ?>" required>

        <label for="children_ages">Children Ages (comma separated numbers)</label>
        <input id="children_ages" name="children_ages" type="text" value="<?= escape(old('children_ages', (string) ($childrenAgesText ?? ''))); ?>" required>

        <label for="preferences">Preferences (comma separated)</label>
        <input id="preferences" name="preferences" type="text" value="<?= escape(old('preferences', (string) ($preferencesText ?? ''))); ?>" required>

        <button type="submit" class="btn">Save Profile</button>
    </form>
</section>
