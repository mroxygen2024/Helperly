<?php
/*
 |--------------------------------------------------------------------------
 | views/auth/reset-password.php
 |--------------------------------------------------------------------------
 | Accept a reset token and securely submit a new password.
 */
$resetToken = (string) ($token ?? '');
?>
<section class="card">
    <h1>Reset password</h1>
    <p class="muted">Choose a new password with at least 8 characters, including letters and numbers.</p>

    <?php if ($resetToken === ''): ?>
        <p class="muted">The reset link is missing a token. Request a new link below.</p>
        <p><a href="/forgot-password">Request new reset link</a></p>
    <?php else: ?>
        <form action="/reset-password" method="POST" class="form-grid" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
            <input type="hidden" name="token" value="<?= escape($resetToken); ?>">

            <label for="password">New password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">

            <label for="confirm_password">Confirm new password</label>
            <input id="confirm_password" name="confirm_password" type="password" required autocomplete="new-password">

            <button type="submit" class="btn">Update password</button>
        </form>
    <?php endif; ?>
</section>
