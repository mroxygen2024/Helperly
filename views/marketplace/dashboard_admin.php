<div class="animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-wrap justify-between items-center gap-6 mb-10">
        <div>
            <h1 class="page-title text-3xl font-black text-main mb-1">System Overview</h1>
            <p class="text-neutral-400 font-bold">Comprehensive marketplace analytics and management</p>
        </div>
        <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-neutral-100">
            <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined">calendar_today</span>
            </div>
            <div class="pr-4">
                <p class="text-[10px] uppercase tracking-widest font-black text-neutral-400 leading-tight">Current Date</p>
                <p class="text-sm font-extrabold text-main leading-tight"><?= date('M d, Y'); ?></p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div id="stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <a href="/admin/users" class="dashboard-card stat-card group overflow-hidden" id="stat-total-users">
            <div class="relative z-10">
                <div class="stat-icon mb-4 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <p class="text-sm font-black text-neutral-400 uppercase tracking-widest mb-1">Total Users</p>
                <p class="text-3xl font-black text-main"><?= number_format($stats['total_users']); ?></p>
                <div class="mt-4 flex items-center gap-2 text-primary text-xs font-black opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0">
                    <span>VIEW MANAGEMENT</span>
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </div>
            </div>
        </a>
        
        <a href="/admin/jobs?status=active" class="dashboard-card stat-card group overflow-hidden" id="stat-active-jobs">
            <div class="relative z-10">
                <div class="stat-icon mb-4 group-hover:scale-110 transition-transform bg-info-50 text-info">
                    <span class="material-symbols-outlined">work</span>
                </div>
                <p class="text-sm font-black text-neutral-400 uppercase tracking-widest mb-1">Active Jobs</p>
                <p class="text-3xl font-black text-main"><?= number_format($stats['active_jobs'] ?? $stats['total_jobs']); ?></p>
                <div class="mt-4 flex items-center gap-2 text-info text-xs font-black opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0">
                    <span>TRACK PROGRESS</span>
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </div>
            </div>
        </a>

        <a href="/admin/providers" class="dashboard-card stat-card group overflow-hidden" id="stat-total-providers">
            <div class="relative z-10">
                <div class="stat-icon mb-4 group-hover:scale-110 transition-transform bg-secondary-50 text-secondary">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <p class="text-sm font-black text-neutral-400 uppercase tracking-widest mb-1">Total Providers</p>
                <p class="text-3xl font-black text-main"><?= $stats['total_providers']; ?></p>
                <div class="mt-4 flex items-center gap-2 text-secondary text-xs font-black opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0">
                    <span>PROVIDER LIST</span>
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </div>
            </div>
        </a>

        <a href="/admin/providers?verified=approved" class="dashboard-card stat-card group overflow-hidden" id="stat-verified-providers">
            <div class="relative z-10">
                <div class="stat-icon mb-4 group-hover:scale-110 transition-transform bg-success-50 text-success">
                    <span class="material-symbols-outlined">verified</span>
                </div>
                <p class="text-sm font-black text-neutral-400 uppercase tracking-widest mb-1">Verified</p>
                <p class="text-3xl font-black text-main"><?= $stats['verified_providers']; ?></p>
                <div class="mt-4 flex items-center gap-2 text-success text-xs font-black opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0">
                    <span>TRUSTED NETWORK</span>
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8">
            <div class="dashboard-card p-0 overflow-hidden">
                <div class="flex flex-wrap justify-between items-center gap-4 p-6 border-b border-neutral-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined">history</span>
                        </div>
                        <h2 class="text-lg font-black text-main">Recent Activity</h2>
                    </div>
                    <a href="/admin/jobs" class="btn btn-outline btn-sm px-6">
                        View All History
                    </a>
                </div>
                
                <?php if (empty($recentJobs)): ?>
                    <div class="flex flex-col items-center justify-center py-20 text-center px-6">
                        <div class="empty-state-icon bg-neutral-50 text-neutral-300">
                            <span class="material-symbols-outlined" style="font-size: 48px;">work_off</span>
                        </div>
                        <h3 class="text-lg font-black text-main mb-2">No jobs posted yet</h3>
                        <p class="text-neutral-400 font-bold max-w-sm">When employers start posting requirements, they will appear here for management.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="table-modern w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Job Reference</th>
                                    <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Participants</th>
                                    <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Status</th>
                                    <th class="px-6 py-4 text-right text-[10px] uppercase tracking-widest font-black text-neutral-400">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-50">
                                <?php foreach ($recentJobs as $job): ?>
                                    <tr class="hover:bg-neutral-50/50 transition-colors group">
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <span class="font-extrabold text-main"><?= escape($job['service_type'] ?? 'General Service'); ?></span>
                                                <span class="text-[10px] font-black text-neutral-400 uppercase mt-1">#<?= escape(substr((string)$job['_id'], -8)); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-neutral-100 flex items-center justify-center text-[10px] font-black text-neutral-500" title="Parent: <?= escape($job['parent']['name'] ?? 'Unknown'); ?>">
                                                    <?= mb_substr(escape($job['parent']['name'] ?? 'P'), 0, 1); ?>
                                                </div>
                                                <span class="material-symbols-outlined text-neutral-300 text-sm">arrow_forward</span>
                                                <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary flex items-center justify-center text-[10px] font-black" title="Provider: <?= escape($job['provider']['name'] ?? 'Unassigned'); ?>">
                                                    <?= mb_substr(escape($job['provider']['name'] ?? 'U'), 0, 1); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <?php
                                            $status = $job['status'] ?? 'open';
                                            $badgeClass = match ($status) {
                                                'completed' => 'success',
                                                'active' => 'info',
                                                'cancelled' => 'danger',
                                                default => 'warning'
                                            };
                                            ?>
                                            <span class="badge badge-<?= $badgeClass; ?> px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                                <?= escape($status); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <a href="/admin/jobs/detail?id=<?= escape((string)$job['_id']); ?>" class="btn btn-ghost btn-sm p-2 hover:bg-primary-50 hover:text-primary transition-all">
                                                <span class="material-symbols-outlined">visibility</span>
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

        <div class="lg:col-span-4 space-y-8">
            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-warning-50 text-warning flex items-center justify-center">
                        <span class="material-symbols-outlined">bolt</span>
                    </div>
                    <h3 class="text-lg font-black text-main">Quick Actions</h3>
                </div>
                
                <div class="space-y-3">
                    <?php foreach ($adminSections ?? [] as $section): ?>
                        <a href="<?= escape($section['link']); ?>" class="flex items-center justify-between p-4 rounded-2xl border-2 border-neutral-50 hover:border-primary-100 hover:bg-primary-50 transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white border border-neutral-100 flex items-center justify-center text-neutral-400 group-hover:text-primary group-hover:bg-white transition-all shadow-sm">
                                    <span class="material-symbols-outlined"><?= escape($section['icon_name'] ?? 'link'); ?></span>
                                </div>
                                <span class="text-sm font-extrabold text-main"><?= escape($section['title']); ?></span>
                            </div>
                            <span class="material-symbols-outlined text-neutral-300 group-hover:text-primary group-hover:translate-x-1 transition-all">chevron_right</span>
                        </a>
                    <?php endforeach; ?>
                    
                    <div class="pt-4 mt-4 border-t border-dashed border-neutral-100">
                        <a href="/admin/verifications" class="flex items-center justify-between p-4 rounded-2xl bg-neutral-900 text-white hover:bg-primary transition-all group shadow-lg shadow-neutral-200">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined">verified_user</span>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-white/60 uppercase tracking-widest leading-tight">Verification</p>
                                    <p class="text-sm font-black leading-tight">Pending Requests</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-white/40 group-hover:text-white group-hover:translate-x-1 transition-all">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="dashboard-card bg-primary-900 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="text-lg font-black mb-4">System Status</h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-primary-200">Database Connection</span>
                            <span class="flex items-center gap-2 text-xs font-black text-success">
                                <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                                ACTIVE
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-primary-200">Storage Usage</span>
                            <span class="text-xs font-black text-white">4.2 GB / 20 GB</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5 mt-2">
                            <div class="bg-white rounded-full h-1.5 w-[21%]"></div>
                        </div>
                    </div>
                </div>
                <!-- Decorative element -->
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            </div>
        </div>
    </div>
</div>
