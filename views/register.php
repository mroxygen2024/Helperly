<div class="card" style="margin-top: 2rem;">
    <div class="card-header" style="flex-direction: column; align-items: flex-start; gap: 0.5rem;">
        <h1 class="card-title" style="font-size: 1.5rem;">Start Your Journey</h1>
        <p class="text-muted">Create an account to join the marketplace</p>
    </div>

    <form action="/register" method="POST" class="flex flex-col gap-4">
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <div class="form-group">
            <label for="name" class="label">Full Name</label>
            <input id="name" name="name" type="text" class="input" value="<?= escape(old('name')); ?>" required placeholder="John Doe">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
                <label for="email" class="label">Email Address</label>
                <input id="email" name="email" type="email" class="input" value="<?= escape(old('email')); ?>" required placeholder="john@example.com">
            </div>
            <div class="form-group">
                <label for="phone" class="label">Phone Number</label>
                <input id="phone" name="phone" type="tel" class="input" value="<?= escape(old('phone')); ?>" required placeholder="+1234567890">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
                <label for="password" class="label">Password</label>
                <input id="password" name="password" type="password" class="input" required placeholder="••••••••">
            </div>
            <div class="form-group">
                <label for="role" class="label">I am a</label>
                <select id="role" name="role" class="select" required>
                    <option value="">Select your role</option>
                    <option value="service_provider" <?= normalizeRole(old('role')) === 'service_provider' ? 'selected' : ''; ?>>Service Provider</option>
                    <option value="parent" <?= normalizeRole(old('role')) === 'parent' ? 'selected' : ''; ?>>Parent (Customer)</option>
                    <option value="administrator" <?= normalizeRole(old('role')) === 'administrator' ? 'selected' : ''; ?>>Administrator</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-full" style="padding: 0.875rem; margin-top: 0.5rem;">
            Create Account
        </button>

        <p class="text-center text-sm text-muted mt-4">
            Already have an account? <a href="/login" style="color: var(--primary); font-weight: 600; text-decoration: none;">Sign in instead</a>
        </p>
    </form>
</div>
