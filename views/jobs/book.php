<section>
    <h1>Book <?= escape((string) ($provider['full_name'] ?? 'Provider')); ?></h1>
    <p class="muted">Fill out the job details to directly book this service provider.</p>
</section>

<section class="card stack">
    <div class="flex-row gap-medium" style="margin-bottom: 2rem; align-items: center;">
        <?php if (!empty($provider['profile_photo'])): ?>
            <img src="<?= escape((string) $provider['profile_photo']); ?>" alt="<?= escape((string) ($provider['full_name'] ?? 'Provider')); ?>" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #ead9c0;">
        <?php endif; ?>
        <div>
            <h2 style="margin: 0;"><?= escape((string) ($provider['full_name'] ?? 'Unnamed Provider')); ?></h2>
            <p class="muted" style="margin: 0;">Verified Service Provider</p>
            <p class="small" style="margin: 0.2rem 0 0;">Hourly Rate: <strong><?= escape((string) ($provider['hourly_rate'] ?? 'N/A')); ?></strong></p>
        </div>
    </div>

    <form action="/jobs" method="POST" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? ''); ?>">
        <input type="hidden" name="selected_provider_id" value="<?= escape($provider_id ?? ''); ?>">

        <div>
            <label for="job_time">Time</label>
            <input id="job_time" name="time" type="datetime-local" value="<?= escape(old('time')); ?>" required>
        </div>

        <div>
            <label for="job_duration">Duration</label>
            <input id="job_duration" name="duration" type="text" value="<?= escape(old('duration')); ?>" placeholder="e.g. 3 hours" required>
        </div>

        <div>
            <label for="job_service_type">Type of Service</label>
            <input id="job_service_type" name="service_type" type="text" value="<?= escape(old('service_type') ?: (is_array($provider['skills'] ?? null) ? $provider['skills'][0] : '')); ?>" placeholder="e.g. Child care" required>
        </div>

        <div>
            <label for="job_location">Location</label>
            <input id="job_location" name="location" type="text" value="<?= escape(old('location') ?: ($employer_location ?? '')); ?>" placeholder="e.g. Dhaka" required>
        </div>

        <div style="grid-column: 1 / -1;">
            <label for="job_instructions">Special Instructions</label>
            <textarea id="job_instructions" name="instructions" rows="4" placeholder="Share important details for this job" required><?= escape(old('instructions')); ?></textarea>
        </div>

        <div>
            <button type="submit" class="btn">Confirm Booking</button>
            <a href="/servants" class="btn btn-outline" style="margin-left: 0.5rem;">Cancel</a>
        </div>
    </form>
</section>
