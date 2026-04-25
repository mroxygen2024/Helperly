<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2 class="card-title">Account Settings</h2>
        <p class="text-sm text-muted">Update your basic profile information</p>
    </div>

    <form action="/profile/account" method="POST" class="flex flex-col gap-4">
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <div class="form-group">
            <label for="name" class="label">Full Name</label>
            <input id="name" name="name" type="text" class="input" value="<?= escape(old('name', (string) ($user['name'] ?? ''))); ?>" required>
        </div>

        <div class="form-group">
            <label for="phone" class="label">Phone Number</label>
            <input id="phone" name="phone" type="tel" class="input" value="<?= escape(old('phone', (string) ($user['phone'] ?? ''))); ?>" required>
        </div>

        <div class="p-4 bg-primary-soft rounded-lg mb-2">
            <h3 class="text-sm font-600 mb-1">Email Address</h3>
            <p class="text-sm text-muted">Your email is used for login and notifications.</p>
            <p class="font-500 mt-1"><?= escape($user['email'] ?? 'N/A'); ?></p>
        </div>

        <button type="submit" class="btn btn-primary">
            <span class="material-symbols-outlined">save</span>
            Save Changes
        </button>
    </form>
</div>
