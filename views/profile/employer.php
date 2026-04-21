<section class="card">
    <h1>Employer Profile</h1>
    <p class="muted">Create or update your employer profile.</p>

    <form action="/profile/employer" method="POST" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <label for="address">Address</label>
        <input id="address" name="address" type="text" value="<?= escape(old('address', (string) ($profile['address'] ?? ''))); ?>" required>

        <label for="location">Location</label>
        <input id="location" name="location" type="text" value="<?= escape(old('location', (string) ($profile['location'] ?? ''))); ?>" required>

        <button type="submit" class="btn">Save Profile</button>
    </form>
</section>
