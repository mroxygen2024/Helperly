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
        <p class="stat-value"><?= number_format($stats['active_jobs'] ?? $stats['total_jobs']); ?></p>
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

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="md:col-span-2">
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b">
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

    <div>
        <div class="card">
            <div class="card-header border-b mb-6 pb-4">
                <h3 class="font-700 text-lg">Quick Actions</h3>
            </div>
            <div class="flex flex-col gap-4">
                <?php foreach ($adminSections ?? [] as $section): ?>
                    <a href="<?= escape($section['link']); ?>" class="flex items-center justify-between p-4 rounded-xl border hover:border-primary hover:bg-primary-soft transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="p-2 rounded-lg bg-gray-100 group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined" style="font-size: 20px;"><?= escape($section['icon_name'] ?? 'link'); ?></span>
                            </div>
                            <span class="font-600 text-sm"><?= escape($section['title']); ?></span>
                        </div>
                        <span class="material-symbols-outlined text-muted group-hover:text-primary transition-colors" style="font-size: 18px;">chevron_right</span>
                    </a>
                <?php endforeach; ?>
                
                <a href="/admin/verifications" class="flex items-center justify-between p-4 rounded-xl border hover:border-warning hover:bg-warning-soft transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="p-2 rounded-lg bg-gray-100 group-hover:bg-warning group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined" style="font-size: 20px;">verified_user</span>
                        </div>
                        <span class="font-600 text-sm">Pending Verifications</span>
                    </div>
                    <span class="material-symbols-outlined text-muted group-hover:text-warning transition-colors" style="font-size: 18px;">chevron_right</span>
                </a>
            </div>
        </div>
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
