<section class="card">
    <h1>Account Profile</h1>
    <p class="muted">All users can update their basic account details here.</p>

    <form action="/profile/account" method="POST" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <label for="name">Full Name</label>
        <input id="name" name="name" type="text" value="<?= escape(old('name', (string) ($user['name'] ?? ''))); ?>" required>

        <label for="phone">Phone</label>
        <input id="phone" name="phone" type="tel" value="<?= escape(old('phone', (string) ($user['phone'] ?? ''))); ?>" required>

        <button type="submit" class="btn">Save Account</button>
    </form>
</section>
