<div class="card" style="margin-top: 4rem; max-width: 480px; margin-inline: auto;">
    <div class="card-header flex-col items-center gap-2 text-center" style="border-bottom: none; padding-bottom: 0;">
        <div class="logo-icon mb-2" style="background: var(--primary-glow); color: var(--primary); padding: 1rem; border-radius: 50%;">
            <span class="material-symbols-outlined" style="font-size: 2.5rem;">lock_reset</span>
        </div>
        <h2 class="card-title" style="font-size: 1.75rem;">Reset Password</h2>
        <p class="text-muted">Enter your email and we'll send you a link to reset your password.</p>
    </div>

    <div class="card-body" style="padding: 2rem 2.5rem 2.5rem;">
        <form action="/forgot-password" method="POST" class="flex flex-col gap-6" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

            <div class="form-group">
                <label for="email" class="label">Email Address</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="email" name="email" type="email" class="input-field" 
                           value="<?= escape(old('email')); ?>" required 
                           placeholder="name@example.com"
                           style="padding-left: 3rem;">
                    <span class="material-symbols-outlined" 
                          style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                        mail
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; height: 52px; font-size: 1rem;">
                Send Reset Link
                <span class="material-symbols-outlined">send</span>
            </button>

            <div class="text-center mt-4">
                <a href="/login" class="text-sm font-700" style="color: var(--text-muted); display: flex; items-center justify-center gap-2;">
                    <span class="material-symbols-outlined" style="font-size: 1.1rem;">arrow_back</span>
                    Back to login
                </a>
            </div>
        </form>
    </div>
</div>

