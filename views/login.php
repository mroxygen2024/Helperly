<div class="card" style="margin-top: 4rem; max-width: 480px; margin-inline: auto;">
    <div class="card-header flex-col items-center gap-2 text-center" style="border-bottom: none; padding-bottom: 0;">
        <h2 class="card-title" style="font-size: 2rem;">Welcome Back</h2>
        <p class="text-muted">Enter your credentials to access your account</p>
    </div>

    <div class="card-body" style="padding: 2.5rem;">
        <form action="/login" method="POST" class="flex flex-col gap-5">
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

            <div class="form-group">
                <label for="email" class="label">Email Address</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="email" name="email" type="email" class="input-field" 
                           value="<?= escape(old('email')); ?>" required 
                           placeholder="name@example.com"
                           style="padding-left: 3rem;">
                    <span class="material-symbols-outlined" 
                          style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        mail
                    </span>
                </div>
            </div>

            <div class="form-group">
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="label" style="margin-bottom: 0;">Password</label>
                    <a href="/forgot-password" class="text-sm" style="color: var(--primary); font-weight: 600;">Forgot password?</a>
                </div>
                <div class="input-wrapper" style="position: relative;">
                    <input id="password" name="password" type="password" class="input-field" 
                           required placeholder="••••••••"
                           style="padding-left: 3rem;">
                    <span class="material-symbols-outlined" 
                          style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        lock
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; height: 52px; font-size: 1rem; margin-top: 0.5rem;">
                Sign In
                <span class="material-symbols-outlined">login</span>
            </button>

            <div class="text-center mt-6">
                <p class="text-sm text-muted">
                    Don't have an account? 
                    <a href="/register" style="color: var(--primary); font-weight: 700; text-decoration: underline; text-underline-offset: 4px;">
                        Create one here
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

