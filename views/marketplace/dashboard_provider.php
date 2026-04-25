<div class="grid grid-cols-4 mb-2">
    <div class="card stat-card">
        <p class="stat-label">My Rating</p>
        <p class="stat-value text-warning">
            <?= isset($profile['rating']) && $profile['rating'] > 0 ? number_format($profile['rating'], 1) . ' ★' : 'N/A'; ?>
        </p>
    </div>
    <div class="card stat-card">
        <p class="stat-label">Active Jobs</p>
        <p class="stat-value"><?= count($activeJobs ?? []); ?></p>
    </div>
    <div class="card stat-card">
        <p class="stat-label">Earnings</p>
        <p class="stat-value text-success">0 BDT</p>
    </div>
    <div class="card stat-card">
        <p class="stat-label">Status</p>
        <div class="mt-1">
            <span class="badge badge-<?= (string)($profile['verification_status'] ?? '') === 'approved' ? 'success' : 'warning'; ?>">
                <?= ServantProfile::verificationStatusLabel($profile['verification_status'] ?? ''); ?>
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <!-- Active Assignments -->
    <div id="active-jobs" class="flex flex-col gap-4">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">My Active Assignments</h2>
            </div>
            
            <?php if (empty($activeJobs)): ?>
                <p class="text-muted text-center py-4">No active jobs right now.</p>
            <?php else: ?>
                <div class="flex flex-col gap-3">
                    <?php foreach ($activeJobs as $job): ?>
                        <div class="card border job-card active" style="margin-bottom: 0;">
                            <h3 class="font-600 mb-1"><?= escape($job['service_type']); ?></h3>
                            <p class="text-sm mb-4"><?= nl2br(escape($job['instructions'] ?? '')); ?></p>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-600 text-info"><?= escape($job['total_cost']); ?> BDT</span>
                                <div class="flex gap-2">
                                    <form action="/jobs/confirm" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                        <button type="submit" class="btn btn-primary btn-sm" <?= ($job['provider_confirmed'] ?? false) ? 'disabled' : ''; ?>>
                                            <?= ($job['provider_confirmed'] ?? false) ? 'Waiting for Parent' : 'Mark as Finished'; ?>
                                        </button>
                                    </form>
                                    <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm">Chat</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Application Tracking -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Applications</h2>
            </div>
            <div class="flex flex-col gap-2">
                <?php if (empty($applicationsList)): ?>
                    <p class="text-muted text-center py-4">You haven't applied to any jobs recently.</p>
                <?php else: ?>
                    <?php foreach (array_slice($applicationsList, 0, 5) as $app): ?>
                        <div class="flex justify-between items-center p-3 border rounded-lg">
                            <div>
                                <p class="font-500"><?= escape($app['job']['service_type'] ?? 'Job'); ?></p>
                                <p class="text-sm text-muted"><?= escape($app['job']['location'] ?? ''); ?></p>
                            </div>
                            <span class="badge badge-<?= $app['status'] === 'accepted' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'secondary'); ?>">
                                <?= escape($app['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Available Jobs -->
    <div id="available-jobs" class="card">
        <div class="card-header">
            <h2 class="card-title">Opportunities Near You</h2>
        </div>
        
        <?php if (empty($jobs)): ?>
            <div class="text-center py-8">
                <span class="material-symbols-outlined text-muted" style="font-size: 3rem;">work_off</span>
                <p class="text-muted mt-2">No open jobs at the moment. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="flex flex-col gap-4">
                <?php foreach ($jobs as $job): ?>
                    <?php $alreadyApplied = in_array((string)$job['_id'], $appliedJobIds ?? [], true); ?>
                    <div class="card border p-4" style="margin-bottom: 0;">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-600"><?= escape($job['service_type']); ?></h3>
                            <span class="text-info font-700"><?= escape($job['total_cost']); ?> BDT</span>
                        </div>
                        <p class="text-sm text-muted mb-3">
                            <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">location_on</span>
                            <?= escape($job['location']); ?> • <?= escape($job['duration']); ?> hrs
                        </p>
                        <p class="text-sm mb-4 line-clamp-2"><?= escape($job['instructions']); ?></p>
                        
                        <form action="/jobs/apply" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                            <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                            <button type="submit" class="btn <?= $alreadyApplied ? 'btn-outline' : 'btn-primary'; ?> w-full" <?= $alreadyApplied ? 'disabled' : ''; ?>>
                                <span class="material-symbols-outlined"><?= $alreadyApplied ? 'check_circle' : 'send'; ?></span>
                                <?= $alreadyApplied ? 'Already Applied' : 'Apply Now'; ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
