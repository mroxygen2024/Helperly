<div class="auth-page-wrapper">
    <div class="auth-card">
        <div class="text-center mb-10">
            <div class="auth-logo-icon">
                <span class="material-symbols-outlined">lock_reset</span>
            </div>
            <h2 class="auth-title">Reset Password</h2>
            <p class="text-muted">Enter your email and we'll send you a link to reset your password.</p>
        </div>

        <form action="/forgot-password" method="POST" class="flex flex-col gap-6" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

            <div class="floating-group">
                <input id="email" name="email" type="email" class="input-field" 
                       value="<?= escape(old('email')); ?>" required 
                       placeholder=" " autocomplete="email">
                <label for="email">Email Address</label>
                <span class="material-symbols-outlined input-icon">mail</span>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-full">
                Send Reset Link
                <span class="material-symbols-outlined">send</span>
            </button>

            <div class="text-center mt-4">
                <a href="/login" class="text-sm font-bold text-muted flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 1.1rem;">arrow_back</span>
                    Back to login
                </a>
            </div>
        </form>
    </div>
</div>
