<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 2px solid var(--border-light); padding-bottom: 1.5rem;">
        <h2 class="card-title">Parent Profile</h2>
        <p class="text-sm text-muted">Complete your profile to help service providers understand your needs</p>
    </div>

    <div class="card-body" style="padding-top: 2rem;">
        <form action="/profile/employer" method="POST" class="flex flex-col gap-6" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

            <div class="grid grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="address" class="label">Home Address</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="address" name="address" type="text" class="input-field" 
                               value="<?= escape(old('address', (string) ($profile['address'] ?? ''))); ?>" required
                               placeholder="123 Main St, Apartment 4B"
                               style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            home
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location" class="label">Neighborhood / Area</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="location" name="location" type="text" class="input-field" 
                               value="<?= escape(old('location', (string) ($profile['location'] ?? ''))); ?>" required
                               placeholder="e.g. Downtown, West End"
                               style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            location_on
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="emergency_contacts" class="label">Emergency Contacts</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="emergency_contacts" name="emergency_contacts" type="text" class="input-field" 
                           value="<?= escape(old('emergency_contacts', (string) ($emergencyContactsText ?? ''))); ?>" required
                           placeholder="Spouse: 555-0199, Grandma: 555-0188"
                           style="padding-left: 3rem;">
                    <span class="material-symbols-outlined" 
                          style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                        contact_emergency
                    </span>
                </div>
                <p class="text-sm text-muted mt-2">Enter names and phone numbers separated by commas.</p>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="children_ages" class="label">Children Ages</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="children_ages" name="children_ages" type="text" class="input-field" 
                               value="<?= escape(old('children_ages', (string) ($childrenAgesText ?? ''))); ?>" required
                               placeholder="e.g. 2, 5, 8"
                               style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            child_care
                        </span>
                    </div>
                    <p class="text-sm text-muted mt-2">Separate ages by commas.</p>
                </div>

                <div class="form-group">
                    <label for="preferences" class="label">Special Preferences</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="preferences" name="preferences" type="text" class="input-field" 
                               value="<?= escape(old('preferences', (string) ($preferencesText ?? ''))); ?>" required
                               placeholder="e.g. Non-smoker, Pet friendly"
                               style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" 
                              style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            favorite
                        </span>
                    </div>
                    <p class="text-sm text-muted mt-2">Any specific requirements or preferences.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4" style="border-top: 1px solid var(--border-light);">
                <button type="submit" class="btn btn-primary btn-lg">
                    <span class="material-symbols-outlined">check_circle</span>
                    Verify & Save Profile
                </button>
            </div>
        </form>
    </div>
</div>

