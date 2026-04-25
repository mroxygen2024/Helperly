<!-- Top Status Bar -->
<div class="card p-4 border-none shadow-sm mb-6 <?= (string)($profile['verification_status'] ?? '') === 'approved' ? 'bg-success-soft' : 'bg-warning-soft'; ?>">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="p-2 rounded-full <?= (string)($profile['verification_status'] ?? '') === 'approved' ? 'bg-success text-white' : 'bg-warning text-white'; ?>">
                <span class="material-symbols-outlined"><?= (string)($profile['verification_status'] ?? '') === 'approved' ? 'verified' : 'pending'; ?></span>
            </div>
            <div>
                <p class="font-600 text-sm">Account Status: <?= ServantProfile::verificationStatusLabel($profile['verification_status'] ?? ''); ?></p>
                <p class="text-xs opacity-75"><?= (string)($profile['verification_status'] ?? '') === 'approved' ? 'You are a verified provider. Enjoy full access!' : 'Complete your profile to start taking high-value jobs.'; ?></p>
            </div>
        </div>
        <a href="/profile/servant" class="btn btn-outline btn-sm" style="background: white;">Update Profile</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Main Content: Opportunities -->
    <div class="md:col-span-2 flex flex-col gap-6">
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b">
                <div class="flex justify-between items-center w-full">
                    <h2 class="card-title">Opportunities Near You</h2>
                    <span class="badge badge-info"><?= count($jobs ?? []); ?> Available</span>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($jobs)): ?>
                    <div class="text-center py-12">
                        <span class="material-symbols-outlined text-muted" style="font-size: 3rem;">work_off</span>
                        <p class="text-muted mt-2">No open jobs at the moment. Check back soon!</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 gap-4">
                        <?php foreach ($jobs as $job): ?>
                            <?php $alreadyApplied = in_array((string)$job['_id'], $appliedJobIds ?? [], true); ?>
                            <div class="group p-4 border rounded-xl hover:border-primary hover:shadow-md transition-all">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-600 group-hover:text-primary transition-colors"><?= escape($job['service_type']); ?></h3>
                                        <div class="flex items-center gap-2 text-xs text-muted mt-1">
                                            <span class="material-symbols-outlined" style="font-size: 14px;">location_on</span>
                                            <?= escape($job['location']); ?> • <?= escape($job['duration']); ?> hrs
                                        </div>
                                    </div>
                                    <span class="text-lg font-700 text-info"><?= escape($job['total_cost']); ?> BDT</span>
                                </div>
                                <p class="text-sm text-muted mb-4 line-clamp-2"><?= escape($job['instructions']); ?></p>
                                
                                <form action="/jobs/apply" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                    <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                    <button type="submit" class="btn <?= $alreadyApplied ? 'bg-gray-100 text-gray-400' : 'btn-primary'; ?> w-full" <?= $alreadyApplied ? 'disabled' : ''; ?>>
                                        <span class="material-symbols-outlined"><?= $alreadyApplied ? 'check_circle' : 'send'; ?></span>
                                        <?= $alreadyApplied ? 'Applied' : 'Apply Now'; ?>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar: Stats & Active Work -->
    <div class="flex flex-col gap-6">
        <!-- Stats Summary -->
        <div class="grid grid-cols-1 gap-4">
            <div class="card stat-card">
                <p class="stat-label">Active Assignments</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="material-symbols-outlined text-warning">play_circle</span>
                    <p class="stat-value"><?= count($activeJobs ?? []); ?></p>
                </div>
            </div>
            <div class="card stat-card">
                <p class="stat-label">Rating</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="material-symbols-outlined text-warning">star</span>
                    <p class="stat-value"><?= isset($profile['rating']) && $profile['rating'] > 0 ? number_format($profile['rating'], 1) : 'New'; ?></p>
                </div>
            </div>
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="card-header p-4 border-b">
                <h3 class="font-600">Active Work</h3>
            </div>
            <div class="p-4">
                <?php if (empty($activeJobs)): ?>
                    <p class="text-xs text-muted text-center py-4">No active assignments.</p>
                <?php else: ?>
                    <div class="flex flex-col gap-3">
                        <?php foreach ($activeJobs as $job): ?>
                            <div class="p-3 border rounded-lg bg-gray-50">
                                <h4 class="text-sm font-600"><?= escape($job['service_type']); ?></h4>
                                <div class="flex justify-between items-center mt-3">
                                    <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm" style="padding: 0.25rem 0.5rem;">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">chat</span>
                                    </a>
                                    <form action="/jobs/confirm" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                        <button type="submit" class="btn btn-primary btn-sm" <?= ($job['provider_confirmed'] ?? false) ? 'disabled' : ''; ?>>
                                            <?= ($job['provider_confirmed'] ?? false) ? 'Done' : 'Finish'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-b mb-4 pb-4">
                <h3 class="font-600 text-sm text-muted">Applications</h3>
            </div>
            <div class="flex flex-col gap-2">
                <?php if (empty($applicationsList)): ?>
                    <p class="text-xs text-muted text-center py-2">No recent applications.</p>
                <?php else: ?>
                    <?php foreach (array_slice($applicationsList, 0, 3) as $app): ?>
                        <div class="flex justify-between items-center text-xs">
                            <span class="truncate pr-2"><?= escape($app['job']['service_type'] ?? 'Job'); ?></span>
                            <span class="badge badge-<?= $app['status'] === 'accepted' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'secondary'); ?>" style="scale: 0.8; font-size: 10px;">
                                <?= escape($app['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
