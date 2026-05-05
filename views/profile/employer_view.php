<div class="container py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Sidebar -->
        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-8">
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="user-avatar-rect mb-4" style="width: 120px; height: 120px; background: var(--grad-secondary); font-size: 3rem;">
                        <?= mb_substr(escape($user['name'] ?? 'P'), 0, 1); ?>
                    </div>
                    <h1 class="text-2xl font-800 mb-1"><?= escape($user['name'] ?? 'Employer'); ?></h1>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge badge-info flex items-center gap-1">
                            <span class="material-symbols-outlined" style="font-size: 14px;">verified_user</span> Verified Parent
                        </span>
                    </div>
                    <p class="text-muted text-sm"><?= escape($profile['location'] ?? 'Location not specified'); ?></p>
                </div>

                <div class="flex flex-col gap-4 border-t pt-6">
                    <div class="flex justify-between items-center">
                        <span class="text-muted text-sm">Jobs Posted</span>
                        <span class="font-700"><?= (int)$stats['total_posted']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted text-sm">Jobs Completed</span>
                        <span class="font-700"><?= (int)$stats['total_completed']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted text-sm">Completion Rate</span>
                        <span class="font-700 text-success"><?= number_format((float)$stats['completion_rate'], 0); ?>%</span>
                    </div>
                </div>

                <div class="mt-8">
                    <?php if (($currentUser['role'] ?? '') === 'provider'): ?>
                        <a href="/messages?employer_id=<?= escape((string)$user['_id']); ?>" class="btn btn-primary w-full py-4">
                            <span class="material-symbols-outlined">chat</span> Message Parent
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 flex flex-col gap-8">
            <div class="card p-8">
                <h2 class="text-xl font-800 mb-6">About Parent</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs font-800 uppercase tracking-widest text-muted mb-2">Preferences</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php if (!empty($profile['preferences'])): ?>
                                <?php foreach ($profile['preferences'] as $pref): ?>
                                    <span class="badge badge-outline py-1 px-3"><?= escape($pref); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-sm text-muted">No preferences listed.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-800 uppercase tracking-widest text-muted mb-2">Member Since</h3>
                        <p class="text-sm font-600"><?= isset($user['created_at']) ? $user['created_at']->toDateTime()->format('M Y') : 'N/A'; ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Jobs -->
            <div class="card p-0 overflow-hidden">
                <div class="card-header p-8 border-b">
                    <h2 class="text-xl font-800">Recent Job History</h2>
                </div>
                <div class="p-8">
                    <?php if (empty($recent_jobs)): ?>
                        <p class="text-muted italic">No job history available.</p>
                    <?php else: ?>
                        <div class="flex flex-col gap-4">
                            <?php foreach ($recent_jobs as $job): ?>
                                <div class="flex justify-between items-center p-4 border rounded-xl">
                                    <div>
                                        <h4 class="font-700"><?= escape($job['service_type']); ?></h4>
                                        <p class="text-xs text-muted"><?= escape($job['location']); ?> • <?= (float)($job['duration'] ?? 0); ?> hrs</p>
                                    </div>
                                    <span class="badge badge-<?= $job['status'] === 'completed' ? 'success' : 'info'; ?>">
                                        <?= escape(ucfirst($job['status'])); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
