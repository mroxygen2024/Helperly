<!-- Profile Completion/Verification Warning -->
<?php if (!($isProfileComplete ?? true)): ?>
<div class="dashboard-card card p-5 border-none shadow-premium mb-8 bg-gradient-to-r from-danger/10 to-danger/5 border-l-4 border-danger">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-danger text-white flex items-center justify-center shadow-lg shadow-danger/20">
                <span class="material-symbols-outlined">person_add</span>
            </div>
            <div>
                <h3 class="font-black text-danger text-lg m-0">Complete Your Profile</h3>
                <p class="text-sm font-bold text-danger/70 m-0">You need to provide your skills and rate before applying for jobs.</p>
            </div>
        </div>
        <a href="/profile/servant" class="btn btn-danger btn-sm px-8 shadow-lg shadow-danger/20">Complete Now</a>
    </div>
</div>
<?php elseif (!($isVerified ?? false)): ?>
<div class="dashboard-card card p-5 border-none shadow-premium mb-8 bg-gradient-to-r from-warning/10 to-warning/5 border-l-4 border-warning">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-warning text-white flex items-center justify-center shadow-lg shadow-warning/20">
                <span class="material-symbols-outlined">verified_user</span>
            </div>
            <div>
                <h3 class="font-black text-warning-dark text-lg m-0">Get Verified</h3>
                <p class="text-sm font-bold text-warning-dark/70 m-0">Verified providers get 5x more job visibility and trust.</p>
            </div>
        </div>
        <a href="/profile/servant" class="btn btn-warning btn-sm px-8 shadow-lg shadow-warning/20">Start Verification</a>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-8 flex flex-col gap-8">
        <!-- Stats Row -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card cursor-pointer" onclick="location.href='/jobs/available'">
                <div class="stat-icon" style="background: var(--primary-50); color: var(--primary);">
                    <span class="material-symbols-outlined">work</span>
                </div>
                <p class="stat-value"><?= (int)$stats['available_jobs']; ?></p>
                <p class="stat-label">Available</p>
            </div>
            <div class="stat-card cursor-pointer" onclick="location.href='/provider/jobs'">
                <div class="stat-icon" style="background: var(--warning-light); color: var(--warning-dark);">
                    <span class="material-symbols-outlined">sync</span>
                </div>
                <p class="stat-value"><?= (int)$stats['active_assignments']; ?></p>
                <p class="stat-label">Active</p>
            </div>
            <div class="stat-card cursor-pointer" onclick="location.href='/provider/applications'">
                <div class="stat-icon" style="background: var(--info-light); color: var(--info-dark);">
                    <span class="material-symbols-outlined">assignment</span>
                </div>
                <p class="stat-value"><?= (int)$stats['applications']; ?></p>
                <p class="stat-label">Applications</p>
            </div>
            <div class="stat-card cursor-pointer" onclick="location.href='/provider/view.php?id=<?= escape((string)$user['id']); ?>#reviews'">
                <div class="stat-icon" style="background: var(--success-light); color: var(--success-dark);">
                    <span class="material-symbols-outlined">star</span>
                </div>
                <p class="stat-value"><?= number_format((float)$stats['rating'], 1); ?></p>
                <p class="stat-label">Rating</p>
            </div>
        </div>

        <!-- Opportunities Section -->
        <div class="dashboard-card card p-0 overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center bg-neutral-50/50">
                <h2 class="text-lg font-extrabold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">explore</span>
                    Opportunities Near You
                </h2>
                <a href="/jobs/available" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="p-6">
                <?php if (empty($availableJobs)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <span class="material-symbols-outlined">search_off</span>
                        </div>
                        <h3 class="text-lg font-bold">No jobs matching right now</h3>
                        <p class="text-neutral-500 mb-6">Try updating your skills or location to see more jobs.</p>
                        <a href="/profile/servant" class="btn btn-primary">Update Profile</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 gap-4">
                        <?php foreach (array_slice($availableJobs, 0, 5) as $job): ?>
                            <div class="flex flex-wrap justify-between items-center p-5 border-2 border-neutral-50 rounded-2xl hover:border-primary-100 hover:bg-primary-50/30 transition-all cursor-pointer group" onclick="if(!event.target.closest('button') && !event.target.closest('a')) location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 bg-white rounded-2xl shadow-sm border flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-primary" style="font-size: 32px;">work_outline</span>
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-lg leading-tight mb-1"><?= escape($job['service_type']); ?></h3>
                                        <div class="flex items-center gap-4 text-xs font-bold text-neutral-400">
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">location_on</span> <?= escape($job['location']); ?></span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span> <?= escape($job['duration']); ?> hrs</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right flex items-center gap-6">
                                    <div>
                                        <p class="text-xl font-black text-primary m-0"><?= number_format((float)($job['total_cost'] ?? 0), 0); ?> <span class="text-xs">ETB</span></p>
                                        <p class="text-[10px] font-black uppercase text-neutral-400 tracking-tighter">Fixed Budget</p>
                                    </div>
                                    <?php 
                                        $appliedJobIds = array_map(fn($app) => (string)($app['job_id'] ?? ''), $applications ?? []);
                                        $hasApplied = in_array((string)$job['_id'], $appliedJobIds, true);
                                    ?>
                                    <?php if ($hasApplied): ?>
                                        <button class="btn btn-outline btn-sm px-6" disabled>Applied</button>
                                    <?php else: ?>
                                        <a href="/jobs/apply?id=<?= escape((string)$job['_id']); ?>" 
                                           class="btn btn-primary btn-sm px-6 shadow-lg shadow-primary/20 <?= !($isProfileComplete ?? true) ? 'pointer-events-none opacity-50' : ''; ?>"
                                           onclick="event.stopPropagation();">
                                            Apply Now
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Active Work -->
        <div class="dashboard-card card p-0 overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center bg-neutral-50/50">
                <h2 class="text-lg font-extrabold flex items-center gap-2">
                    <span class="material-symbols-outlined text-warning">pending_actions</span>
                    Active Assignments
                </h2>
                <a href="/provider/jobs" class="btn btn-outline btn-sm">Manage All</a>
            </div>
            <div class="p-6">
                <?php if (empty($activeWork)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <span class="material-symbols-outlined">work_history</span>
                        </div>
                        <h3 class="text-lg font-bold">No active assignments</h3>
                        <p class="text-neutral-500">Your hired jobs will appear here for management.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 gap-4">
                        <?php foreach ($activeWork as $job): ?>
                            <div class="flex flex-wrap justify-between items-center p-5 border-2 border-neutral-50 rounded-2xl hover:border-warning-100 transition-all bg-white shadow-sm">
                                <div class="flex items-center gap-5">
                                    <div class="user-avatar w-14 h-14 text-xl border-none">
                                        <?= mb_substr(escape($job['parent']['name'] ?? 'C'), 0, 1); ?>
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-lg leading-tight mb-1"><?= escape($job['service_type']); ?></h3>
                                        <p class="text-sm font-bold text-neutral-400 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">person</span>
                                            Client: <?= escape($job['parent']['name'] ?? 'N/A'); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm px-6">Chat</a>
                                    <form action="/jobs/confirm" method="POST" onclick="event.stopPropagation()">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                        <button type="submit" class="btn btn-success btn-sm px-6 shadow-lg shadow-success/20" <?= ($job['provider_confirmed'] ?? false) ? 'disabled' : ''; ?>>
                                            <?= ($job['provider_confirmed'] ?? false) ? 'Finalizing...' : 'Finish Job'; ?>
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

    <!-- Sidebar -->
    <div class="lg:col-span-4 flex flex-col gap-8">
        <!-- Earnings Card -->
        <div class="dashboard-card card p-0 overflow-hidden border-none shadow-premium bg-gradient-to-br from-primary-600 to-primary-800 text-white">
            <div class="p-8 relative">
                <span class="material-symbols-outlined absolute right-[-20px] bottom-[-20px] opacity-10 text-[120px] rotate-12">account_balance_wallet</span>
                <p class="text-[10px] font-black uppercase tracking-widest text-primary-200 mb-1">Wallet Balance</p>
                <div class="flex items-baseline gap-2 mb-8">
                    <p class="text-4xl font-black"><?= number_format((float)$stats['earnings'], 2); ?></p>
                    <p class="text-sm font-bold opacity-80 uppercase">ETB</p>
                </div>
                <a href="/provider/payments" class="btn bg-white text-primary-700 w-full font-black py-4 border-none hover:scale-[1.02] transition-transform">Withdraw Funds</a>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="dashboard-card card p-8">
            <h3 class="font-black text-sm uppercase tracking-widest text-neutral-400 mb-8 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">analytics</span>
                Performance
            </h3>
            <div class="space-y-8">
                <div>
                    <div class="flex justify-between items-center text-sm mb-3">
                        <span class="font-bold text-neutral-500">Job Success</span>
                        <span class="text-success font-black">100%</span>
                    </div>
                    <div class="w-full bg-neutral-100 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-success/60 to-success h-full" style="width: 100%;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center text-sm mb-3">
                        <span class="font-bold text-neutral-500">Profile Visibility</span>
                        <span class="text-primary font-black">High</span>
                    </div>
                    <div class="w-full bg-neutral-100 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-primary/60 to-primary h-full" style="width: 85%;"></div>
                    </div>
                </div>
            </div>
            <div class="mt-10 pt-8 border-t flex flex-col gap-4">
                <a href="/profile/servant" class="flex items-center justify-between p-3 rounded-xl hover:bg-primary-50 transition-all group">
                    <span class="text-sm font-extrabold text-neutral-600 group-hover:text-primary transition-colors">Manage Portfolio</span>
                    <span class="material-symbols-outlined text-neutral-300 group-hover:text-primary group-hover:translate-x-1 transition-all">chevron_right</span>
                </a>
                <a href="/provider/view.php?id=<?= escape((string)$user['id']); ?>" class="flex items-center justify-between p-3 rounded-xl hover:bg-primary-50 transition-all group">
                    <span class="text-sm font-extrabold text-neutral-600 group-hover:text-primary transition-colors">Public Preview</span>
                    <span class="material-symbols-outlined text-neutral-300 group-hover:text-primary group-hover:translate-x-1 transition-all">chevron_right</span>
                </a>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="dashboard-card card p-0 overflow-hidden">
            <div class="p-6 border-b bg-neutral-50/50">
                <h3 class="font-black text-sm uppercase tracking-widest text-neutral-400">Applications</h3>
            </div>
            <div class="p-6">
                <?php if (empty($applications)): ?>
                    <p class="text-sm text-neutral-400 font-bold text-center py-4">No active applications.</p>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach (array_slice($applications, 0, 3) as $app): ?>
                            <div class="group cursor-pointer">
                                <div class="flex justify-between items-start mb-2">
                                    <p class="text-sm font-extrabold text-main leading-tight"><?= escape($app['job_data']['service_type'] ?? 'Job'); ?></p>
                                    <span class="badge badge-<?= $app['status'] === 'accepted' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'info'); ?> px-2 py-0.5 text-[10px] font-black">
                                        <?= escape(ucfirst($app['status'])); ?>
                                    </span>
                                </div>
                                <p class="text-[10px] text-neutral-400 font-bold flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">history</span>
                                    Sent <?= isset($app['created_at']) ? (is_string($app['created_at']) ? date('M d', strtotime($app['created_at'])) : ($app['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $app['created_at']->toDateTime()->format('M d') : 'N/A')) : 'N/A'; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <a href="/provider/applications" class="btn btn-outline btn-sm w-full mt-8">Full Status</a>
            </div>
        </div>
    </div>
</div>
