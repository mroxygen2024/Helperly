<?php
/*
 |--------------------------------------------------------------------------
 | views/login.php
 |--------------------------------------------------------------------------
 | Login page template only. Authentication logic stays in AuthController.
 */
?>
<section class="card">
    <h1>Login</h1>
    <p class="muted">Access your servant or employer account.</p>

    <form action="/login" method="POST" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= escape(old('email')); ?>" required autocomplete="email">

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required autocomplete="current-password">

        <p class="muted"><a href="/forgot-password">Forgot your password?</a></p>

        <button type="submit" class="btn">Login</button>
    </form>
</section>
