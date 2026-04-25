<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Main Content: Active and Recent Jobs -->
    <div class="md:col-span-2 flex flex-col gap-6">
        <!-- Stats Summary -->
        <div class="grid grid-cols-3 gap-4">
            <div class="card stat-card">
                <p class="stat-label">Active Jobs</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="material-symbols-outlined text-warning">pending_actions</span>
                    <p class="stat-value"><?= count(array_filter($jobs ?? [], fn($j) => $j['status'] === 'active')); ?></p>
                </div>
            </div>
            <div class="card stat-card">
                <p class="stat-label">Total Posted</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="material-symbols-outlined text-info">assignment</span>
                    <p class="stat-value"><?= count($jobs ?? []); ?></p>
                </div>
            </div>
            <div class="card stat-card">
                <p class="stat-label">Total Spend</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="material-symbols-outlined text-success">payments</span>
                    <p class="stat-value"><?= array_sum(array_column($jobs ?? [], 'total_cost')); ?></p>
                </div>
            </div>
        </div>

        <!-- Active Jobs Section -->
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b">
                <h2 class="card-title">Current Active Work</h2>
            </div>
            <div class="p-6">
                <?php $activeJobs = array_filter($jobs ?? [], fn($j) => $j['status'] === 'active'); ?>
                <?php if (empty($activeJobs)): ?>
                    <div class="text-center py-8">
                        <span class="material-symbols-outlined text-muted" style="font-size: 3rem;">work_off</span>
                        <p class="text-muted mt-2">No jobs currently in progress.</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($activeJobs as $job): ?>
                            <div class="flex justify-between items-center p-4 border rounded-xl hover:shadow-sm transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="bg-warning-soft p-3 rounded-lg">
                                        <span class="material-symbols-outlined text-warning">sync</span>
                                    </div>
                                    <div>
                                        <h3 class="font-600"><?= escape($job['service_type']); ?></h3>
                                        <p class="text-sm text-muted"><?= escape($job['selected_provider']['name'] ?? 'Provider assigned'); ?></p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form action="/jobs/confirm" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                        <button type="submit" class="btn btn-primary btn-sm" <?= ($job['parent_confirmed'] ?? false) ? 'disabled' : ''; ?>>
                                            <?= ($job['parent_confirmed'] ?? false) ? 'Finalizing...' : 'Confirm Completion'; ?>
                                        </button>
                                    </form>
                                    <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm">Chat</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity / History -->
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b">
                <h2 class="card-title">Job History</h2>
            </div>
            <div class="p-6">
                <?php if (empty($jobs)): ?>
                    <p class="text-muted text-center py-4">Your job history will appear here.</p>
                <?php else: ?>
                    <div class="flex flex-col gap-3">
                        <?php foreach (array_slice(array_reverse($jobs), 0, 5) as $job): ?>
                            <div class="flex justify-between items-center text-sm">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-muted" style="font-size: 18px;">history</span>
                                    <span><?= escape($job['service_type']); ?></span>
                                </div>
                                <span class="badge badge-<?= $job['status'] === 'completed' ? 'success' : ($job['status'] === 'active' ? 'warning' : 'info'); ?>">
                                    <?= escape($job['status']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar: Quick Post & Actions -->
    <div class="flex flex-col gap-6">
        <div class="card bg-primary text-white border-none shadow-lg">
            <h2 class="font-700 text-lg mb-2">Need help?</h2>
            <p class="text-sm opacity-90 mb-6">Post a new job requirement and find verified providers in minutes.</p>
            
            <form action="/jobs" method="POST" class="flex flex-col gap-3">
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                
                <input name="service_type" type="text" class="input" placeholder="What do you need?" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                
                <div class="grid grid-cols-2 gap-2">
                    <input name="hourly_rate" type="number" class="input" placeholder="Rate/Hr" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                    <input name="duration" type="number" step="0.5" class="input" placeholder="Hrs" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                </div>

                <input name="location" type="text" class="input" placeholder="Location" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                <input name="time" type="datetime-local" class="input" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">

                <button type="submit" class="btn btn-outline w-full" style="background: white; color: var(--primary); font-weight: 700; border: none; margin-top: 0.5rem;">
                    <span class="material-symbols-outlined">add_circle</span>
                    Post Job Now
                </button>
            </form>
        </div>

        <div class="card">
            <div class="card-header border-b mb-4 pb-4">
                <h3 class="font-600">Quick Links</h3>
            </div>
            <div class="flex flex-col gap-3">
                <a href="/servants" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors border">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">search</span>
                        <span class="text-sm font-500">Browse Providers</span>
                    </div>
                    <span class="material-symbols-outlined text-muted" style="font-size: 18px;">chevron_right</span>
                </a>
                <a href="/messages" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors border">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">chat</span>
                        <span class="text-sm font-500">Inbox</span>
                    </div>
                    <span class="material-symbols-outlined text-muted" style="font-size: 18px;">chevron_right</span>
                </a>
            </div>
        </div>
    </div>
</div>
