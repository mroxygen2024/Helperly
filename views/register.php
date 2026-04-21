<?php
/*
 |--------------------------------------------------------------------------
 | views/register.php
 |--------------------------------------------------------------------------
 | Registration page template only. Validation and persistence stay in
 | controllers/AuthController.php and models/User.php.
 */
?>
<section class="card">
    <h1>Create account</h1>
    <p class="muted">Join as a service provider, parent, or administrator.</p>

    <form action="/register" method="POST" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <label for="name">Full name</label>
        <input id="name" name="name" type="text" value="<?= escape(old('name')); ?>" required autocomplete="name">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= escape(old('email')); ?>" required autocomplete="email">

        <label for="phone">Phone number</label>
        <input id="phone" name="phone" type="tel" value="<?= escape(old('phone')); ?>" required autocomplete="tel" placeholder="+1234567890">

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required autocomplete="new-password">

        <label for="role">I am a</label>
        <select id="role" name="role" required>
            <option value="">Select role</option>
            <option value="service_provider" <?= normalizeRole(old('role')) === 'service_provider' ? 'selected' : ''; ?>>Service provider</option>
            <option value="parent" <?= normalizeRole(old('role')) === 'parent' ? 'selected' : ''; ?>>Parent</option>
            <option value="administrator" <?= normalizeRole(old('role')) === 'administrator' ? 'selected' : ''; ?>>Administrator</option>
        </select>

        <button type="submit" class="btn">Register</button>
    </form>
</section>
