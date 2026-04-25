<div class="card" style="margin-top: 4rem;">
    <div class="card-header" style="flex-direction: column; align-items: flex-start; gap: 0.5rem;">
        <h1 class="card-title" style="font-size: 1.5rem;">Welcome Back</h1>
        <p class="text-muted">Enter your credentials to access your account</p>
    </div>

    <form action="/login" method="POST" class="flex flex-col gap-4">
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <div class="form-group">
            <label for="email" class="label">Email Address</label>
            <input id="email" name="email" type="email" class="input" value="<?= escape(old('email')); ?>" required placeholder="you@example.com">
        </div>

        <div class="form-group">
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="label" style="margin-bottom: 0;">Password</label>
                <a href="/forgot-password" class="text-sm" style="color: var(--primary); text-decoration: none;">Forgot password?</a>
            </div>
            <input id="password" name="password" type="password" class="input" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary w-full" style="padding: 0.875rem;">
            Sign In
        </button>

        <p class="text-center text-sm text-muted mt-4">
            Don't have an account? <a href="/register" style="color: var(--primary); font-weight: 600; text-decoration: none;">Create one here</a>
        </p>
    </form>
</div>
