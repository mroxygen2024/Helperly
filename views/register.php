<div class="auth-page-wrapper">
    <div class="auth-card" style="max-width: 600px;">
        <div class="text-center mb-10">
            <div class="auth-logo-icon">
                <span class="material-symbols-outlined">how_to_reg</span>
            </div>
            <h2 class="auth-title">Create Account</h2>
            <p class="text-muted">Join our community of professionals and families</p>
        </div>

        <form action="/register" method="POST" class="flex flex-col gap-2" data-auth-form>
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

            <div class="floating-group">
                <input id="name" name="name" type="text" class="input-field" 
                       value="<?= escape(old('name')); ?>" required 
                       placeholder=" " autocomplete="name">
                <label for="name">Full Name</label>
                <span class="material-symbols-outlined input-icon">person</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
                <div class="floating-group">
                    <input id="email" name="email" type="email" class="input-field" 
                           value="<?= escape(old('email')); ?>" required 
                           placeholder=" " autocomplete="email">
                    <label for="email">Email Address</label>
                    <span class="material-symbols-outlined input-icon">mail</span>
                </div>
                <div class="floating-group">
                    <input id="phone" name="phone" type="tel" class="input-field" 
                           value="<?= escape(old('phone')); ?>" required 
                           placeholder=" " autocomplete="tel">
                    <label for="phone">Phone Number</label>
                    <span class="material-symbols-outlined input-icon">call</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
                <div class="floating-group">
                    <input id="password" name="password" type="password" class="input-field" 
                           required placeholder=" " autocomplete="new-password">
                    <label for="password">Password</label>
                    <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Toggle password visibility">
                        <span class="material-symbols-outlined">visibility</span>
                    </button>
                </div>
                <div class="floating-group">
                    <select id="role" name="role" class="select" required>
                        <option value="" hidden></option>
                        <option value="provider" <?= normalizeRole(old('role')) === 'provider' ? 'selected' : ''; ?>>Service Provider</option>
                        <option value="parent" <?= normalizeRole(old('role')) === 'parent' ? 'selected' : ''; ?>>Customer (Parent)</option>
                    </select>
                    <label for="role">Register as</label>
                    <span class="material-symbols-outlined input-icon">badge</span>
                </div>
            </div>

            <div class="mb-8">
                <label class="checkbox-group">
                    <input type="checkbox" name="terms" required>
                    <span class="text-sm font-semibold">I agree to the <a href="/terms" class="text-primary hover:underline">Terms of Service</a></span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-full">
                Create Your Account
                <span class="material-symbols-outlined">how_to_reg</span>
            </button>

            <div class="text-center mt-8">
                <p class="text-muted">
                    Already have an account? 
                    <a href="/login" class="text-primary font-bold hover:underline">Sign in instead</a>
                </p>
            </div>
        </form>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .grid-cols-1.md\:grid-cols-2 {
        grid-template-columns: 1fr !important;
    }
}
</style>
