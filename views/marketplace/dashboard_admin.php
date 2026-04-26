<div id="stats" class="grid grid-cols-4 gap-8 mb-12">
    <div class="card stat-card">
        <span class="material-symbols-outlined stat-icon">group</span>
        <p class="stat-label">Total Users</p>
        <p class="stat-value"><?= number_format($stats['total_users']); ?></p>
    </div>
    
    <div class="card stat-card">
        <span class="material-symbols-outlined stat-icon" style="color: var(--info);">work</span>
        <p class="stat-label">Active Jobs</p>
        <p class="stat-value"><?= number_format($stats['total_jobs']); ?></p>
    </div>

    <div class="card stat-card">
        <span class="material-symbols-outlined stat-icon" style="color: var(--secondary);">badge</span>
        <p class="stat-label">Total Providers</p>
        <p class="stat-value"><?= $stats['total_providers']; ?></p>
    </div>

    <div class="card stat-card">
        <span class="material-symbols-outlined stat-icon" style="color: var(--success);">verified</span>
        <p class="stat-label">Verified</p>
        <p class="stat-value"><?= $stats['verified_providers']; ?></p>
    </div>
</div>

<div class="card md:col-span-2">
    <div class="card-header">
        <h2 class="card-title">Recent Jobs</h2>
    </div>
    
    <?php if (empty($recentJobs)): ?>
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-muted" style="font-size: 3rem;">work</span>
            <p class="text-muted mt-2">No jobs have been posted yet.</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Job ID / Service</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentJobs as $job): ?>
                        <tr>
                            <td>
                                <div class="font-600"><?= escape($job['service_type'] ?? 'General'); ?></div>
                                <div class="text-xs text-muted"><?= escape(substr((string)$job['_id'], -8)); ?></div>
                            </td>
                            <td>
                                <?php
                                $status = $job['status'] ?? 'open';
                                $badgeClass = match ($status) {
                                    'completed' => 'badge-success',
                                    'active' => 'badge-primary',
                                    'cancelled' => 'badge-danger',
                                    default => 'badge-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass; ?>"><?= escape(ucfirst($status)); ?></span>
                            </td>
                            <td class="text-sm text-muted">
                                <?= isset($job['created_at']) ? (is_string($job['created_at']) ? date('M d, Y', strtotime($job['created_at'])) : ($job['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $job['created_at']->toDateTime()->format('M d, Y') : 'N/A')) : 'N/A'; ?>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn btn-outline btn-sm" data-open-modal="job_modal_<?= escape((string)$job['_id']); ?>" title="View Details">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span> Details
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</div>

<?php if (!empty($recentJobs)): ?>
    <?php foreach ($recentJobs as $job): ?>
        <div id="job_modal_<?= escape((string)$job['_id']); ?>" class="modal-overlay" data-modal>
            <div class="modal-content" style="max-width: 600px;">
                <div class="modal-header" style="background: white; border-bottom: 2px solid var(--border-light);">
                    <div class="flex items-center gap-4">
                        <div class="user-avatar-rect" style="width: 48px; height: 48px; background: var(--grad-primary);">
                            <span class="material-symbols-outlined" style="color: white;">work</span>
                        </div>
                        <div>
                            <h2 class="card-title" style="margin: 0;"><?= escape($job['service_type'] ?? 'General Service'); ?> Job</h2>
                            <p class="text-sm text-muted">ID: <?= escape((string)$job['_id']); ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" data-close-modal="job_modal_<?= escape((string)$job['_id']); ?>" style="border:none; padding: 0.5rem; border-radius: 50%;">
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
                            <span class="font-700"><?= escape($job['duration'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-muted font-600">Status</span>
                            <span class="font-700"><?= escape(ucfirst($job['status'] ?? 'Open')); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-muted font-600">Hourly Rate</span>
                            <span class="font-700"><?= number_format((float)($job['hourly_rate'] ?? 0), 2); ?> BDT</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-muted font-600">Total Cost</span>
                            <span class="font-700"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> BDT</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col mb-4">
                        <span class="text-xs text-muted font-600 mb-1">Instructions</span>
                        <div class="p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-700" style="white-space: pre-wrap;">
                            <?= escape($job['instructions'] ?? 'No instructions provided.'); ?>
                        </div>
                    </div>
                    
                    <?php if (isset($job['parent_id'])): ?>
                    <div class="flex flex-col mb-4">
                        <span class="text-xs text-muted font-600 mb-1">Parent Entity ID</span>
                        <span class="font-700 text-sm"><?= escape((string)$job['parent_id']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($job['selected_provider_id'])): ?>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600 mb-1">Selected Provider ID</span>
                        <span class="font-700 text-sm"><?= escape((string)$job['selected_provider_id']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer" style="padding: 1.5rem 2.5rem; background: white; border-top: 1px solid var(--border-base);">
                    <button type="button" class="btn btn-outline w-full" data-close-modal="job_modal_<?= escape((string)$job['_id']); ?>">Close Details</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
(() => {
    const openButtons = document.querySelectorAll('[data-open-modal]');
    const closeButtons = document.querySelectorAll('[data-close-modal]');

    openButtons.forEach(btn => {
        btn.onclick = () => {
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
