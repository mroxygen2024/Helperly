<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 2px solid var(--border-light); padding-bottom: 1.5rem;">
        <h2 class="card-title">Account Settings</h2>
        <p class="text-sm text-muted">Update your personal information and contact details</p>
    </div>

    <div class="card-body" style="padding-top: 2rem;">
        <form action="/profile/account" method="POST" class="flex flex-col gap-6">
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

            <div class="grid grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="name" class="label">Full Name</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="name" name="name" type="text" class="input-field" 
                               value="<?= escape(old('name', (string) ($user['name'] ?? ''))); ?>" required
                               placeholder="e.g. John Smith"
                               style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            person
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="label">Phone Number</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="phone" name="phone" type="tel" class="input-field" 
                               value="<?= escape(old('phone', (string) ($user['phone'] ?? ''))); ?>" required
                               placeholder="+1 (555) 000-0000"
                               style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            call
                        </span>
                    </div>
                </div>
            </div>

            <div style="padding: 1.5rem; background: var(--background); border-radius: var(--radius-md); border: 1px solid var(--border-base);">
                <div class="flex items-center gap-3 mb-2">
                    <span class="material-symbols-outlined" style="color: var(--primary);">verified</span>
                    <h3 class="text-sm font-700" style="margin: 0; color: var(--text-main);">Email Address</h3>
                </div>
                <p class="text-sm text-muted mb-2">Your email address is verified and used for account security and notifications.</p>
                <p class="font-700" style="font-size: 1.1rem; color: var(--primary);"><?= escape($user['email'] ?? 'N/A'); ?></p>
            </div>

            <div class="flex justify-end pt-4" style="border-top: 1px solid var(--border-light);">
                <button type="submit" class="btn btn-primary btn-lg">
                    <span class="material-symbols-outlined">save</span>
                    Update Account
                </button>
            </div>
        </form>
    </div>
</div>

