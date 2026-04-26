<div class="card" style="margin-top: 4rem;">
    <div class="card-header flex-col items-start gap-2">
        <h2 class="card-title" style="font-size: 1.75rem;">Welcome Back</h2>
        <p class="text-muted">Enter your credentials to access your account</p>
    </div>

    <form action="/login" method="POST" class="flex flex-col gap-4">
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <div class="form-group">
            <label for="email" class="label">Email Address</label>
            <input id="email" name="email" type="email" class="input-field" value="<?= escape(old('email')); ?>" required placeholder="you@example.com">
        </div>

        <div class="form-group">
            <div class="flex justify-between items-center mb-2">
                <label for="password" class="label" style="margin-bottom: 0;">Password</label>
                <a href="/forgot-password" class="text-sm" style="color: var(--primary); font-weight: 600;">Forgot password?</a>
            </div>
            <input id="password" name="password" type="password" class="input-field" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">
            Sign In
        </button>

        <p class="text-center text-sm text-muted mt-4">
            Don't have an account? <a href="/register" style="color: var(--primary); font-weight: 700;">Create one here</a>
        </p>
    </form>
</div>
