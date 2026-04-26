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

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div id="all-jobs" class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Activity</h2>
        </div>
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-muted" style="font-size: 3rem;">monitoring</span>
            <p class="text-muted mt-2">Activity tracking logs will appear here.</p>
        </div>
    </div>
</div>
