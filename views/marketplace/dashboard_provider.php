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
                                
                                <div class="flex gap-2">
                                    <button type="button" class="btn btn-outline w-1/3" data-open-modal="job_modal_<?= escape((string)$job['_id']); ?>">
                                        Details
                                    </button>
                                    <form action="/jobs/apply" method="POST" class="w-2/3">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                        <button type="submit" class="btn <?= $alreadyApplied ? 'bg-gray-100 text-gray-400' : 'btn-primary'; ?> w-full" <?= $alreadyApplied ? 'disabled' : ''; ?>>
                                            <span class="material-symbols-outlined"><?= $alreadyApplied ? 'check_circle' : 'send'; ?></span>
                                            <?= $alreadyApplied ? 'Applied' : 'Apply'; ?>
                                        </button>
                                    </form>
                                </div>
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
                                    <div class="flex gap-1">
                                        <button type="button" class="btn btn-outline btn-sm" style="padding: 0.25rem 0.5rem;" data-open-modal="job_modal_<?= escape((string)$job['_id']); ?>">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">info</span>
                                        </button>
                                        <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm" style="padding: 0.25rem 0.5rem;">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">chat</span>
                                        </a>
                                    </div>
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

<!-- Job Details Modals -->
<?php 
$allJobsForModals = array_merge($jobs ?? [], $activeJobs ?? []);
$seenJobIds = [];
?>
<?php foreach ($allJobsForModals as $job): ?>
    <?php 
    $jobId = (string)$job['_id'];
    if (in_array($jobId, $seenJobIds)) continue;
    $seenJobIds[] = $jobId;
    ?>
    <div id="job_modal_<?= escape($jobId); ?>" class="modal-overlay" data-modal>
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div class="flex items-center gap-4">
                    <div class="user-avatar-rect" style="background: var(--grad-primary);">
                        <span class="material-symbols-outlined" style="color: white;">work</span>
                    </div>
                    <div>
                        <h2 class="card-title" style="margin: 0;"><?= escape($job['service_type']); ?></h2>
                        <span class="badge badge-info"><?= escape(ucfirst($job['status'] ?? 'Open')); ?></span>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-sm" data-close-modal="job_modal_<?= escape($jobId); ?>" style="border:none; padding: 0.5rem; border-radius: 50%;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="modal-body" style="background: #F8FAFC; padding: 2rem;">
                <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Location</span>
                        <span class="font-700"><?= escape($job['location'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Time</span>
                        <span class="font-700"><?= isset($job['time']) ? (is_string($job['time']) ? date('M d, Y h:i A', strtotime($job['time'])) : ($job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('M d, Y h:i A') : 'N/A')) : 'N/A'; ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Duration</span>
                        <span class="font-700"><?= escape($job['duration'] ?? 'N/A'); ?> Hours</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Earnings</span>
                        <span class="font-700 text-info"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> BDT</span>
                    </div>
                </div>
                <div class="flex flex-col mb-4">
                    <span class="text-xs text-muted font-600 mb-1">Full Instructions</span>
                    <div class="p-4 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 shadow-sm" style="white-space: pre-wrap;"><?= escape($job['instructions'] ?? 'No special instructions.'); ?></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline w-full" data-close-modal="job_modal_<?= escape($jobId); ?>">Close</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
(() => {
    const openButtons = document.querySelectorAll('[data-open-modal]');
    const closeButtons = document.querySelectorAll('[data-close-modal]');

    openButtons.forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const modal = document.getElementById(btn.dataset.openModal);
            if (modal) modal.classList.add('open');
        }
    });

    closeButtons.forEach(btn => {
        btn.onclick = () => {
            const modal = document.getElementById(btn.dataset.closeModal);
            if (modal) modal.classList.remove('open');
        }
    });

    window.onclick = (event) => {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('open');
        }
    };
})();
</script>
