<div class="card" style="margin-top: 4rem; max-width: 480px; margin-inline: auto;">
    <div class="card-header flex-col items-center gap-2 text-center" style="border-bottom: none; padding-bottom: 0;">
        <div class="logo-icon mb-2" style="background: var(--primary-glow); color: var(--primary); padding: 1rem; border-radius: 50%;">
            <span class="material-symbols-outlined" style="font-size: 2.5rem;">lock_open</span>
        </div>
        <h2 class="card-title" style="font-size: 1.75rem;">Set New Password</h2>
        <p class="text-muted">Choose a secure password with at least 8 characters</p>
    </div>

    <div class="card-body" style="padding: 2rem 2.5rem 2.5rem;">
        <?php if (($token ?? '') === ''): ?>
            <div class="alert alert-error">
                <span class="material-symbols-outlined">error</span>
                <p>Invalid or expired reset token.</p>
            </div>
            <div class="text-center mt-4">
                <a href="/forgot-password" class="btn btn-outline" style="width: 100%;">Request New Reset Link</a>
            </div>
        <?php else: ?>
            <form action="/reset-password" method="POST" class="flex flex-col gap-6" novalidate>
                <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                <input type="hidden" name="token" value="<?= escape((string) ($token ?? '')); ?>">

                <div class="form-group">
                    <label for="password" class="label">New Password</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="password" name="password" type="password" class="input-field" 
                               required placeholder="••••••••" style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            key
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="label">Confirm New Password</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="confirm_password" name="confirm_password" type="password" class="input-field" 
                               required placeholder="••••••••" style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            key_vertical
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; height: 52px; font-size: 1rem;">
                    Update Password
                    <span class="material-symbols-outlined">check_circle</span>
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

