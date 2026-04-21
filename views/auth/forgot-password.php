<?php
/*
 |--------------------------------------------------------------------------
 | views/auth/forgot-password.php
 |--------------------------------------------------------------------------
 | Request a password reset link without disclosing whether the email exists.
 */
?>
<section class="card">
    <h1>Forgot password</h1>
    <p class="muted">Enter your account email and we will send a reset link if it exists.</p>

    <form action="/forgot-password" method="POST" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= escape(old('email')); ?>" required autocomplete="email">

        <button type="submit" class="btn">Send reset link</button>
    </form>
</section>
