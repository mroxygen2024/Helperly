<div id="stats" class="grid grid-cols-4 gap-8 mb-12">
    <a href="/admin/users" class="card stat-card stat-card-link" id="stat-total-users">
        <span class="material-symbols-outlined stat-icon">group</span>
        <p class="stat-label">Total Users</p>
        <p class="stat-value"><?= number_format($stats['total_users']); ?></p>
        <span class="stat-link-hint">
            <span class="material-symbols-outlined" style="font-size: 14px;">arrow_forward</span>
            View All Users
        </span>
    </a>
    
    <a href="/admin/jobs?status=active" class="card stat-card stat-card-link" id="stat-active-jobs">
        <span class="material-symbols-outlined stat-icon" style="color: var(--info);">work</span>
        <p class="stat-label">Active Jobs</p>
        <p class="stat-value"><?= number_format($stats['total_jobs']); ?></p>
        <span class="stat-link-hint">
            <span class="material-symbols-outlined" style="font-size: 14px;">arrow_forward</span>
            View Active Jobs
        </span>
    </a>

    <a href="/admin/providers" class="card stat-card stat-card-link" id="stat-total-providers">
        <span class="material-symbols-outlined stat-icon" style="color: var(--secondary);">badge</span>
        <p class="stat-label">Total Providers</p>
        <p class="stat-value"><?= $stats['total_providers']; ?></p>
        <span class="stat-link-hint">
            <span class="material-symbols-outlined" style="font-size: 14px;">arrow_forward</span>
            View All Providers
        </span>
    </a>

    <a href="/admin/providers?verified=approved" class="card stat-card stat-card-link" id="stat-verified-providers">
        <span class="material-symbols-outlined stat-icon" style="color: var(--success);">verified</span>
        <p class="stat-label">Verified</p>
        <p class="stat-value"><?= $stats['verified_providers']; ?></p>
        <span class="stat-link-hint">
            <span class="material-symbols-outlined" style="font-size: 14px;">arrow_forward</span>
            View Verified
        </span>
    </a>
</div>

<div class="card md:col-span-2">
    <div class="card-header">
        <h2 class="card-title">Recent Jobs</h2>
        <a href="/admin/jobs" class="btn btn-outline btn-sm">
            <span class="material-symbols-outlined" style="font-size: 16px;">list</span>
            View All
        </a>
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
                                <a href="/admin/jobs/detail?id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm" title="View Details">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span> Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</div>

<style>
.stat-card-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    position: relative;
}

.stat-card-link:hover {
    border-color: var(--primary);
    box-shadow: 0 12px 28px -8px rgba(79, 70, 229, 0.25);
    transform: translateY(-6px);
}

.stat-card-link:hover .stat-icon {
    transform: scale(1.15);
    transition: transform 0.3s;
}

.stat-card-link:hover .stat-value {
    color: var(--primary);
    transition: color 0.3s;
}

.stat-link-hint {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-top: 1rem;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--primary);
    opacity: 0;
    transform: translateY(6px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card-link:hover .stat-link-hint {
    opacity: 1;
    transform: translateY(0);
}
</style>
