<div class="auth-page-wrapper">
    <div class="auth-card">
        <div class="text-center mb-10">
            <div class="auth-logo-icon">
                <span class="material-symbols-outlined">home_work</span>
            </div>
            <h2 class="auth-title">Welcome Back</h2>
            <p class="text-muted">Sign in to your Helperly account</p>
        </div>

        <form action="/login" method="POST" class="flex flex-col gap-2" data-auth-form>
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

            <div class="floating-group">
                <input id="email" name="email" type="email" class="input-field" 
                       value="<?= escape(old('email')); ?>" required 
                       placeholder=" " autocomplete="email">
                <label for="email">Email Address</label>
                <span class="material-symbols-outlined input-icon">mail</span>
            </div>

            <div class="floating-group">
                <input id="password" name="password" type="password" class="input-field" 
                       required placeholder=" " autocomplete="current-password">
                <label for="password">Password</label>
                <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Toggle password visibility">
                    <span class="material-symbols-outlined">visibility</span>
                </button>
            </div>

            <div class="flex justify-between items-center mb-8">
                <label class="checkbox-group">
                    <input type="checkbox" name="remember">
                    <span class="text-sm font-semibold">Remember me</span>
                </label>
                <a href="/forgot-password" class="text-sm font-bold text-primary hover:underline">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-full">
                Sign In
                <span class="material-symbols-outlined">login</span>
            </button>

            <div class="text-center mt-8">
                <p class="text-muted">
                    Don't have an account? 
                    <a href="/register" class="text-primary font-bold hover:underline">Create one here</a>
                </p>
            </div>
        </form>
    </div>
</div>
