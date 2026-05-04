<?php $fullWidthLayout = true; ?>

<div class="auth-page-body">
    <div class="auth-container">
        <!-- Branding Side (Desktop Only) -->
        <div class="auth-brand-side">
            <div class="auth-brand-content">
                <div class="logo-container mb-8">
                    <span class="material-symbols-outlined logo-icon" style="font-size: 2.5rem; padding: 0.75rem;">guardian</span>
                    <span class="sidebar-logo" style="font-size: 2.5rem;">Helperly</span>
                </div>
                <h2 class="hero-title" style="color: white; font-size: 3rem; text-align: left; margin-bottom: 1.5rem;">The easiest way to find trusted help.</h2>
                <p style="font-size: 1.25rem; opacity: 0.9; line-height: 1.6; margin-bottom: 2rem;">Join thousands of families and professionals in our premium marketplace for home services.</p>
                
                <div class="auth-illustration" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="avatar" style="background: white; color: var(--primary);">JD</div>
                        <div>
                            <p class="font-bold m-0">John Doe</p>
                            <p class="text-xs m-0 opacity-70">Verified Professional</p>
                        </div>
                        <div class="ml-auto">
                            <span class="badge badge-success" style="background: rgba(16, 185, 129, 0.2); color: #4ade80; border: none;">Active</span>
                        </div>
                    </div>
                    <div style="height: 8px; background: rgba(255,255,255,0.1); border-radius: 4px; margin-bottom: 8px; width: 80%;"></div>
                    <div style="height: 8px; background: rgba(255,255,255,0.1); border-radius: 4px; width: 60%;"></div>
                </div>
            </div>
        </div>

        <!-- Form Side -->
        <div class="auth-form-side">
            <div class="auth-card">
                <div class="text-center mb-8">
                    <h2 class="card-title" style="font-size: 2.25rem; margin-bottom: 0.5rem;">Welcome Back</h2>
                    <p class="text-muted">Sign in to your Helperly account</p>
                </div>

                <form action="/login" method="POST" class="flex flex-col gap-2" data-auth-form>
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

                    <div class="floating-group">
                        <input id="email" name="email" type="email" class="input-field w-full" 
                               value="<?= escape(old('email')); ?>" required 
                               placeholder=" " autocomplete="email">
                        <label for="email">Email Address</label>
                        <span class="material-symbols-outlined input-icon">mail</span>
                    </div>

                    <div class="floating-group">
                        <input id="password" name="password" type="password" class="input-field w-full" 
                               required placeholder=" " autocomplete="current-password">
                        <label for="password">Password</label>
                        <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Toggle password visibility">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <label class="checkbox-group">
                            <input type="checkbox" name="remember">
                            <span class="text-sm font-semibold">Remember me</span>
                        </label>
                        <a href="/forgot-password" class="text-sm font-bold text-primary hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-full" style="height: 56px; font-size: 1.1rem;">
                        <span>Sign In</span>
                        <span class="material-symbols-outlined">arrow_forward</span>
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
    </div>
</div>


