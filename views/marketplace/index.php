<section>
    <h1>Marketplace</h1>
    <p class="muted">Find trusted servants or post employer needs.</p>
</section>

<?php if (normalizeRole((string) (($user['role'] ?? ''))) === 'parent'): ?>
<section class="card stack">
    <h2>Create Job</h2>
    <p class="muted">Post your requirements so service providers can review and respond.</p>

    <form action="/jobs" method="POST" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">

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
            <button type="submit" class="btn">Post Job</button>
        </div>
    </form>
</section>
<?php endif; ?>

<section class="grid">
    <?php if (empty($listings)): ?>
        <article class="card">
            <h2>No listings yet</h2>
            <p>Listings will appear here once available.</p>
        </article>
    <?php else: ?>
        <?php foreach ($listings as $item): ?>
            <article class="card">
                <h2><?= escape((string) ($item['title'] ?? 'Untitled')); ?></h2>
                <p><?= escape((string) ($item['description'] ?? '')); ?></p>
                <p class="muted">Location: <?= escape((string) ($item['location'] ?? 'Unknown')); ?></p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
