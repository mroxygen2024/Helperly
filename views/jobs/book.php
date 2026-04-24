<section>
    <h1>Book Provider</h1>
    <p class="muted">Fill out the job details to directly book the selected service provider.</p>
</section>

<section class="card stack">
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
            <input id="job_service_type" name="service_type" type="text" value="<?= escape(old('service_type')); ?>" placeholder="e.g. Child care" required>
        </div>

        <div>
            <label for="job_location">Location</label>
            <input id="job_location" name="location" type="text" value="<?= escape(old('location')); ?>" placeholder="e.g. Dhaka" required>
        </div>

        <div>
            <label for="job_instructions">Special Instructions</label>
            <textarea id="job_instructions" name="instructions" rows="4" placeholder="Share important details for this job" required><?= escape(old('instructions')); ?></textarea>
        </div>

        <div>
            <button type="submit" class="btn">Confirm Booking</button>
        </div>
    </form>
</section>
