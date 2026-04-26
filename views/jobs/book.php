<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 2px solid var(--border-light); padding-bottom: 2rem; margin-bottom: 2rem;">
        <div class="flex items-center gap-6">
            <div class="provider-avatar">
                <?php if (!empty($provider['profile_photo'])): ?>
                    <img src="<?= escape((string) $provider['profile_photo']); ?>" alt="<?= escape((string) ($provider['full_name'] ?? 'Provider')); ?>" 
                         style="width: 100px; height: 100px; border-radius: var(--radius-lg); object-fit: cover; box-shadow: var(--shadow-md); border: 3px solid white;">
                <?php else: ?>
                    <div style="width: 100px; height: 100px; border-radius: var(--radius-lg); background: var(--grad-primary); display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; font-weight: 800;">
                        <?= mb_substr(escape($provider['full_name'] ?? 'P'), 0, 1); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <h1 style="font-size: 2rem; margin: 0 0 0.5rem;"><?= escape((string) ($provider['full_name'] ?? 'Unnamed Provider')); ?></h1>
                <div class="flex items-center gap-4">
                    <span class="badge badge-success flex-center gap-1">
                        <span class="material-symbols-outlined" style="font-size: 1rem;">verified</span> Verified
                    </span>
                    <span class="text-muted font-600 text-sm">
                         Hourly Rate: <span style="color: var(--primary); font-weight: 800; font-size: 1.1rem;"><?= escape((string) ($provider['hourly_rate'] ?? 'N/A')); ?></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form action="/jobs" method="POST" class="flex flex-col gap-6" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? ''); ?>">
            <input type="hidden" name="selected_provider_id" value="<?= escape($provider_id ?? ''); ?>">

            <div class="grid grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="job_time" class="label">Date & Time</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="job_time" name="time" type="datetime-local" class="input-field" 
                               value="<?= escape(old('time')); ?>" required style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            calendar_today
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="job_duration" class="label">Estimated Duration</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="job_duration" name="duration" type="text" class="input-field" 
                               value="<?= escape(old('duration')); ?>" placeholder="e.g. 3 hours" required style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            schedule
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="job_service_type" class="label">Service Type</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="job_service_type" name="service_type" type="text" class="input-field" 
                               value="<?= escape(old('service_type') ?: (is_array($provider['skills'] ?? null) ? $provider['skills'][0] : '')); ?>" 
                               placeholder="e.g. Child care" required style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            category
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="job_location" class="label">Work Location</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input id="job_location" name="location" type="text" class="input-field" 
                               value="<?= escape(old('location') ?: ($employer_location ?? '')); ?>" 
                               placeholder="e.g. Home Address" required style="padding-left: 3rem;">
                        <span class="material-symbols-outlined" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                            location_on
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="job_instructions" class="label">Special Instructions & Details</label>
                <textarea id="job_instructions" name="instructions" class="textarea" rows="5" 
                        placeholder="Please provide any specific details about the job, house rules, or special care requirements..." required><?= escape(old('instructions')); ?></textarea>
            </div>

            <div class="flex gap-4 pt-6" style="border-top: 1px solid var(--border-light);">
                <button type="submit" class="btn btn-primary" style="flex: 2; height: 56px; font-size: 1.1rem;">
                    <span class="material-symbols-outlined">check_circle</span>
                    Confirm & Send Booking
                </button>
                <a href="/servants" class="btn btn-outline" style="flex: 1; height: 56px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

