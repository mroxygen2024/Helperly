<div class="auth-page-wrapper">
    <div class="auth-card">
        <div class="text-center mb-10">
            <div class="auth-logo-icon">
                <span class="material-symbols-outlined">lock_open</span>
            </div>
            <h2 class="auth-title">Set New Password</h2>
            <p class="text-muted">Choose a secure password with at least 8 characters</p>
        </div>

        <?php if (($token ?? '') === ''): ?>
            <div class="alert alert-error">
                <span class="material-symbols-outlined">error</span>
                <p>Invalid or expired reset token.</p>
            </div>
            <div class="text-center mt-4">
                <a href="/forgot-password" class="btn btn-outline w-full">Request New Reset Link</a>
            </div>
        <?php else: ?>
            <form action="/reset-password" method="POST" class="flex flex-col gap-6" novalidate>
                <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                <input type="hidden" name="token" value="<?= escape((string) ($token ?? '')); ?>">

                <div class="floating-group">
                    <input id="password" name="password" type="password" class="input-field" 
                           required placeholder=" ">
                    <label for="password">New Password</label>
                    <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Toggle password visibility">
                        <span class="material-symbols-outlined">visibility</span>
                    </button>
                </div>

                <div class="floating-group">
                    <input id="confirm_password" name="confirm_password" type="password" class="input-field" 
                           required placeholder=" ">
                    <label for="confirm_password">Confirm New Password</label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-full">
                    Update Password
                    <span class="material-symbols-outlined">check_circle</span>
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
