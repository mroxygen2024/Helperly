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
            <label for="job_duration">Duration (Hours)</label>
            <input id="job_duration" name="duration" type="number" step="0.5" value="<?= escape(old('duration')); ?>" placeholder="e.g. 3" required>
        </div>

        <div>
            <label for="job_hourly_rate">Expected Hourly Rate</label>
            <input id="job_hourly_rate" name="hourly_rate" type="number" step="1" value="<?= escape(old('hourly_rate')); ?>" placeholder="e.g. 500" required>
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

<section class="card stack">
    <h2>My Jobs & Applicants</h2>
    <p class="muted">Review applicants for your posted jobs. You can accept one provider per job.</p>

    <?php if (empty($jobs)): ?>
        <p class="muted">You haven't posted any jobs yet.</p>
    <?php else: ?>
        <div class="stack">
            <?php foreach ($jobs as $job): ?>
                <article class="card border">
                    <div class="flex-between">
                        <div>
                            <h3 style="margin: 0;"><?= escape((string) ($job['service_type'] ?? 'Job')); ?></h3>
                            <p class="muted" style="margin: 0;">Status: <span class="badge"><?= escape((string) ($job['status'] ?? '')); ?></span></p>
                        </div>
                        <p class="muted"><?= escape(isset($job['time']) && $job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('Y-m-d H:i') : 'N/A'); ?></p>
                    </div>
                    <div class="flex-row gap-medium" style="margin: 0.5rem 0;">
                        <span class="small">Duration: <strong><?= escape((string) ($job['duration'] ?? '0')); ?> hrs</strong></span>
                        <span class="small">Rate: <strong><?= escape((string) ($job['hourly_rate'] ?? '0')); ?>/hr</strong></span>
                        <span class="small">Total: <strong style="color: #2c7ef2;"><?= escape((string) ($job['total_cost'] ?? '0')); ?></strong></span>
                    </div>
                    <p style="margin: 1rem 0;"><?= nl2br(escape((string) ($job['instructions'] ?? ''))); ?></p>

                    <?php if ((string) $job['status'] === 'active'): ?>
                        <div style="margin: 1rem 0; padding: 1rem; background: #eefbff; border: 1px solid #cceeff; border-radius: 8px;">
                            <p style="margin-top: 0;"><strong>Job is Active.</strong> Provider is currently working on this. Please confirm when the job is done.</p>
                            <form action="/jobs/confirm" method="POST" class="inline-form">
                                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                <input type="hidden" name="job_id" value="<?= escape((string) $job['_id']); ?>">
                                <button type="submit" class="btn btn-small" <?= ($job['parent_confirmed'] ?? false) ? 'disabled' : ''; ?>>
                                    <?php if ($job['parent_confirmed'] ?? false): ?>
                                        Confirmed (Waiting for Provider)
                                    <?php else: ?>
                                        Confirm Job Finished
                                    <?php endif; ?>
                                </button>
                                <a href="/messages?job_id=<?= escape((string) $job['_id']); ?>" class="btn btn-outline btn-small" style="margin-left: 0.5rem;">Open Chat</a>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if ((string) $job['status'] === 'completed'): ?>
                        <div style="margin: 1rem 0; padding: 1rem; background: #f6fff6; border: 1px solid #d4ecd4; border-radius: 8px;">
                            <p style="margin-top: 0;"><strong>Job Completed.</strong></p>
                            <?php if (isset($job['payment'])): ?>
                                <p>Payment Status: <span class="badge" style="background: <?= $job['payment']['status'] === 'paid' ? '#28a745' : '#ffc107'; ?>;"><?= escape((string) $job['payment']['status']); ?></span></p>
                                <?php if ($job['payment']['status'] === 'unpaid'): ?>
                                    <form action="/payments/pay" method="POST" class="inline-form">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                        <input type="hidden" name="job_id" value="<?= escape((string) $job['_id']); ?>">
                                        <button type="submit" class="btn btn-small">Pay Now (<?= escape((string) $job['payment']['amount']); ?>)</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($job['selected_provider'])): ?>
                        <div style="margin: 1rem 0; padding: 1rem; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px;">
                            <h4 style="margin: 0 0 0.5rem 0;">Assigned Provider</h4>
                            <div class="flex-between">
                                <div>
                                    <strong><?= escape((string) ($job['selected_provider']['name'] ?? 'Unknown')); ?></strong>
                                    <p class="muted small" style="margin: 0;"><?= escape((string) ($job['selected_provider']['phone'] ?? '')); ?></p>
                                </div>
                                <a href="/messages?job_id=<?= escape((string) $job['_id']); ?>" class="btn btn-small btn-outline">Message</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!isset($job['selected_provider_id'])): ?>
                        <div class="applicants-section" style="margin-top: 1rem; border-top: 1px solid #eee; padding-top: 1rem;">
                            <h4 style="margin-bottom: 0.5rem;">Applicants</h4>
                            <?php if (empty($job['applicants'])): ?>
                                <p class="muted">No applicants yet.</p>
                            <?php else: ?>
                                <div class="grid" style="--grid-cols: 1;">
                                    <?php foreach ($job['applicants'] as $application): ?>
                                        <div class="card" style="background: #fdfdfd; border: 1px solid #f0f0f0;">
                                            <div class="flex-between">
                                                <div>
                                                    <strong style="display: block;"><?= escape((string) ($application['provider']['name'] ?? 'Unknown')); ?></strong>
                                                    <span class="muted small"><?= escape((string) ($application['provider']['phone'] ?? '')); ?></span>
                                                </div>
                                                <span class="badge"><?= escape((string) ($application['status'] ?? 'pending')); ?></span>
                                            </div>

                                            <?php if ((string) $job['status'] === 'open' && (string) $application['status'] === 'pending'): ?>
                                                <div class="flex-row gap-small" style="margin-top: 1rem;">
                                                    <form action="/jobs/accept" method="POST" class="inline-form">
                                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                                        <input type="hidden" name="job_id" value="<?= escape((string) $job['_id']); ?>">
                                                        <input type="hidden" name="provider_id" value="<?= escape((string) $application['provider_id']); ?>">
                                                        <button type="submit" class="btn btn-small">Accept</button>
                                                    </form>
                                                    <form action="/jobs/reject" method="POST" class="inline-form">
                                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                                        <input type="hidden" name="job_id" value="<?= escape((string) $job['_id']); ?>">
                                                        <input type="hidden" name="provider_id" value="<?= escape((string) $application['provider_id']); ?>">
                                                        <button type="submit" class="btn btn-small btn-outline">Reject</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php if (normalizeRole((string) (($user['role'] ?? ''))) === 'service_provider'): ?>
<?php if (!empty($activeJobs)): ?>
<section class="card stack">
    <h2>Active Assignments</h2>
    <p class="muted">These are the jobs you are currently assigned to. Confirm when you have finished.</p>
    <div class="grid">
        <?php foreach ($activeJobs as $job): ?>
            <article class="card border" style="border-left: 5px solid #28a745;">
                <h3><?= escape((string) ($job['service_type'] ?? 'Job')); ?></h3>
                <p><?= nl2br(escape((string) ($job['instructions'] ?? ''))); ?></p>
                <div style="margin-top: 1rem;">
                    <form action="/jobs/confirm" method="POST" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                        <input type="hidden" name="job_id" value="<?= escape((string) $job['_id']); ?>">
                        <button type="submit" class="btn btn-small" <?= ($job['provider_confirmed'] ?? false) ? 'disabled' : ''; ?>>
                            <?php if ($job['provider_confirmed'] ?? false): ?>
                                Confirmed (Waiting for Parent)
                            <?php else: ?>
                                Confirm Job Finished
                            <?php endif; ?>
                        </button>
                        <a href="/messages?job_id=<?= escape((string) $job['_id']); ?>" class="btn btn-outline btn-small" style="margin-left: 0.5rem;">Open Chat</a>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="card stack">
    <h2>Open Jobs</h2>
    <p class="muted">Apply only to jobs you can actually take on. Duplicate applications are blocked.</p>

    <?php if (empty($jobs)): ?>
        <article class="card empty-state">
            <h3>No open jobs</h3>
            <p class="muted">Check back later for new work.</p>
        </article>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($jobs as $job): ?>
                <?php
                $jobId = (string) ($job['_id'] ?? '');
                $alreadyApplied = in_array($jobId, $appliedJobIds ?? [], true);
                ?>
                <article class="card">
                    <h2><?= escape((string) ($job['service_type'] ?? 'Untitled job')); ?></h2>
                    <p><?= nl2br(escape((string) ($job['instructions'] ?? ''))); ?></p>
                    <p class="muted">Location: <?= escape((string) ($job['location'] ?? 'Unknown')); ?></p>
                    <p><strong>Time:</strong> <?= escape(isset($job['time']) && $job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('Y-m-d H:i') : 'N/A'); ?></p>
                    <p><strong>Duration:</strong> <?= escape((string) ($job['duration'] ?? '')); ?> hours</p>
                    <p><strong>Budget:</strong> <?= escape((string) ($job['total_cost'] ?? '0')); ?> (at <?= escape((string) ($job['hourly_rate'] ?? '0')); ?>/hr)</p>

                    <form action="/jobs/apply" method="POST" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                        <input type="hidden" name="job_id" value="<?= escape($jobId); ?>">
                        <button type="submit" class="btn" <?= $alreadyApplied ? 'disabled' : ''; ?>>
                            <?= $alreadyApplied ? 'Applied' : 'Apply'; ?>
                        </button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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
