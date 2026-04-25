<div id="stats" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="card stat-card border-none shadow-sm bg-white">
        <p class="stat-label">Total Platform Users</p>
        <div class="flex items-center gap-3 mt-2">
            <div class="p-2 bg-primary-soft text-primary rounded-lg">
                <span class="material-symbols-outlined">group</span>
            </div>
            <p class="stat-value"><?= number_format($stats['total_users']); ?></p>
        </div>
    </div>
    
    <div class="card stat-card border-none shadow-sm bg-white">
        <p class="stat-label">Active Job Postings</p>
        <div class="flex items-center gap-3 mt-2">
            <div class="p-2 bg-info-soft text-info rounded-lg">
                <span class="material-symbols-outlined">work</span>
            </div>
            <p class="stat-value"><?= number_format($stats['total_jobs']); ?></p>
        </div>
    </div>

    <div class="card stat-card border-none shadow-sm bg-white">
        <p class="stat-label">Total Providers</p>
        <div class="flex items-center gap-3 mt-2">
            <div class="p-2 bg-secondary-soft text-secondary rounded-lg">
                <span class="material-symbols-outlined">badge</span>
            </div>
            <p class="stat-value"><?= $stats['total_providers']; ?></p>
        </div>
    </div>

    <div class="card stat-card border-none shadow-sm bg-white">
        <p class="stat-label">Verified Providers</p>
        <div class="flex items-center gap-3 mt-2">
            <div class="p-2 bg-success-soft text-success rounded-lg">
                <span class="material-symbols-outlined">verified</span>
            </div>
            <p class="stat-value"><?= $stats['verified_providers']; ?></p>
        </div>
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
