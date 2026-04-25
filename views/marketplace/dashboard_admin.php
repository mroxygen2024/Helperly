<div class="grid grid-cols-4 mb-2">
    <div class="card stat-card">
        <p class="stat-label">Total Users</p>
        <p class="stat-value text-primary"><?= number_format($stats['total_users']); ?></p>
    </div>
    <div class="card stat-card">
        <p class="stat-label">Active Jobs</p>
        <p class="stat-value"><?= number_format($stats['total_jobs']); ?></p>
    </div>
    <div class="card stat-card">
        <p class="stat-label">Providers</p>
        <p class="stat-value text-info"><?= $stats['total_providers']; ?></p>
    </div>
    <div class="card stat-card">
        <p class="stat-label">Verified</p>
        <p class="stat-value text-success"><?= $stats['verified_providers']; ?></p>
    </div>
</div>

<div class="grid grid-cols-2 gap-6">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Platform Management</h2>
        </div>
        <div class="grid grid-cols-1 gap-3">
            <?php foreach ($adminSections ?? [] as $section): ?>
                <a href="<?= escape($section['link']); ?>" class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition no-underline text-main">
                    <div class="flex items-center gap-4">
                        <div class="bg-primary-soft p-3 rounded-lg text-primary">
                            <span class="material-symbols-outlined"><?= $section['icon_name'] ?? 'settings'; ?></span>
                        </div>
                        <div>
                            <p class="font-600"><?= escape($section['title']); ?></p>
                            <p class="text-sm text-muted">Manage marketplace <?= strtolower(escape($section['title'])); ?></p>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-muted">chevron_right</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Activity</h2>
        </div>
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-muted" style="font-size: 3rem;">monitoring</span>
            <p class="text-muted mt-2">Activity tracking logs will appear here.</p>
        </div>
    </div>
</div>
