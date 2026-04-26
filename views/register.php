<div class="card" style="margin-top: 2rem; max-width: 600px; margin-inline: auto;">
    <div class="card-header flex-col items-center gap-2 text-center" style="border-bottom: none; padding-bottom: 0;">
        <h2 class="card-title" style="font-size: 2rem;">Start Your Journey</h2>
        <p class="text-muted">Create an account to join our growing marketplace</p>
    </div>

    <div class="card-body" style="padding: 2.5rem;">
        <form action="/register" method="POST" class="flex flex-col gap-5">
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

            <div class="form-group">
                <label for="name" class="label">Full Name</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="name" name="name" type="text" class="input-field" 
                           value="<?= escape(old('name')); ?>" required 
                           placeholder="John Doe"
                           style="padding-left: 3rem;">
                    <span class="material-symbols-outlined" 
                          style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        person
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="email" class="label">Email Address</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="email" name="email" type="email" class="input-field" 
                               value="<?= escape(old('email')); ?>" required 
                               placeholder="john@example.com"
                               style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                            mail
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone" class="label">Phone Number</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="phone" name="phone" type="tel" class="input-field" 
                               value="<?= escape(old('phone')); ?>" required 
                               placeholder="+1234567890"
                               style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                            call
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="password" class="label">Password</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="password" name="password" type="password" class="input-field" 
                               required placeholder="••••••••"
                               style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                            lock
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="role" class="label">Register as</label>
                    <div class="input-wrapper" style="position: relative;">
                        <select id="role" name="role" class="select" required style="padding-left: 3rem;">
                            <option value="">Select your role</option>
                            <option value="service_provider" <?= normalizeRole(old('role')) === 'service_provider' ? 'selected' : ''; ?>>Service Provider</option>
                            <option value="parent" <?= normalizeRole(old('role')) === 'parent' ? 'selected' : ''; ?>>Customer (Parent)</option>
                        </select>
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                            badge
                        </span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; height: 52px; font-size: 1rem; margin-top: 0.5rem;">
                Create Your Account
                <span class="material-symbols-outlined">how_to_reg</span>
            </button>

            <div class="text-center mt-6">
                <p class="text-sm text-muted">
                    Already have an account? 
                    <a href="/login" style="color: var(--primary); font-weight: 700; text-decoration: underline; text-underline-offset: 4px;">
                        Sign in instead
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

