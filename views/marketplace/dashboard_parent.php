<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Main Content: Active and Recent Jobs -->
    <div class="md:col-span-2 flex flex-col gap-6">
        <div class="grid grid-cols-3 gap-6">
            <div class="card stat-card">
                <span class="material-symbols-outlined stat-icon" style="color: var(--warning);">pending_actions</span>
                <p class="stat-label">Active Jobs</p>
                <p class="stat-value"><?= count(array_filter($jobs ?? [], fn($j) => $j['status'] === 'active')); ?></p>
            </div>
            <div class="card stat-card">
                <span class="material-symbols-outlined stat-icon" style="color: var(--info);">assignment</span>
                <p class="stat-label">Total Posted</p>
                <p class="stat-value"><?= count($jobs ?? []); ?></p>
            </div>
            <div class="card stat-card">
                <span class="material-symbols-outlined stat-icon" style="color: var(--success);">payments</span>
                <p class="stat-label">Total Spend</p>
                <p class="stat-value"><?= number_format(array_sum(array_column($jobs ?? [], 'total_cost'))); ?></p>
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

        <!-- Open Jobs & Applications -->
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b bg-primary-soft">
                <div class="flex justify-between items-center">
                    <h2 class="card-title">Open Jobs & Applicants</h2>
                    <span class="badge badge-primary"><?= count(array_filter($jobs ?? [], fn($j) => $j['status'] === 'open')); ?> Open</span>
                </div>
            </div>
            <div class="p-6">
                <?php $openJobs = array_filter($jobs ?? [], fn($j) => $j['status'] === 'open'); ?>
                <?php if (empty($openJobs)): ?>
                    <div class="text-center py-6">
                        <span class="material-symbols-outlined text-muted" style="font-size: 2.5rem;">assignment_add</span>
                        <p class="text-muted mt-2">No open jobs pending applicants.</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($openJobs as $job): ?>
                            <div class="border rounded-xl p-4">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-600 text-lg"><?= escape($job['service_type']); ?></h3>
                                        <p class="text-sm text-muted">Cost: <?= escape($job['total_cost'] ?? 0); ?> • Rate: $<?= escape($job['hourly_rate'] ?? 0); ?>/hr</p>
                                    </div>
                                    <span class="badge badge-info">Open</span>
                                </div>
                                <div class="border-t pt-4">
                                    <h4 class="font-600 text-sm mb-3">Applicants:</h4>
                                    <?php if (empty($job['applicants'])): ?>
                                        <p class="text-sm text-muted italic">No applicants yet.</p>
                                    <?php else: ?>
                                        <div class="flex flex-col gap-3">
                                            <?php foreach ($job['applicants'] as $applicant): ?>
                                                <?php if ($applicant['status'] === 'applied'): ?>
                                                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                                        <div class="flex items-center gap-3">
                                                            <div class="bg-white p-2 rounded-full border shadow-sm flex items-center justify-center">
                                                                <span class="material-symbols-outlined text-primary" style="font-size: 20px;">person</span>
                                                            </div>
                                                            <div>
                                                                <p class="font-500 text-sm m-0">Provider ID: <?= escape(substr($applicant['provider_id'], 0, 8)); ?>...</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <!-- Accept Form -->
                                                            <form action="/jobs/accept" method="POST" class="inline">
                                                                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                                                <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                                                <input type="hidden" name="provider_id" value="<?= escape($applicant['provider_id']); ?>">
                                                                <button type="submit" class="btn btn-success btn-sm flex items-center gap-1" title="Accept">
                                                                    <span class="material-symbols-outlined" style="font-size: 16px;">check</span> Accept
                                                                </button>
                                                            </form>
                                                            <!-- Reject Form -->
                                                            <form action="/jobs/reject" method="POST" class="inline">
                                                                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                                                <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                                                <input type="hidden" name="provider_id" value="<?= escape($applicant['provider_id']); ?>">
                                                                <button type="submit" class="btn btn-outline btn-sm hover-error flex items-center gap-1" title="Reject">
                                                                    <span class="material-symbols-outlined" style="font-size: 16px;">close</span> Reject
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
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
        <div class="card" style="background: var(--grad-primary); border: none; padding: 2rem; position: relative; overflow: hidden;">
            <!-- Background Decorative Element -->
            <span class="material-symbols-outlined" style="position: absolute; right: -20px; top: -10px; font-size: 8rem; opacity: 0.1; color: white; transform: rotate(-15deg);">rocket_launch</span>
            
            <h2 class="card-title" style="color: white; margin-bottom: 0.5rem; font-size: 1.5rem; position: relative;">Post a Requirement</h2>
            <p class="text-sm opacity-90 mb-6" style="color: rgba(255,255,255,0.8); position: relative;">Find the perfect helper for your needs in minutes.</p>
            
            <form action="/jobs" method="POST" class="flex flex-col gap-4" style="position: relative;">
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                
                <div class="form-group mb-0">
                    <input name="service_type" type="text" class="input-field" placeholder="What do you need? (e.g. Cooking)" required 
                           style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; height: 48px;">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group mb-0">
                        <input name="hourly_rate" type="number" class="input-field" placeholder="Rate/Hr" required 
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; height: 48px;">
                    </div>
                    <div class="form-group mb-0">
                        <input name="duration" type="number" step="0.5" class="input-field" placeholder="Hrs" required 
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; height: 48px;">
                    </div>
                </div>

                <div class="form-group mb-0">
                    <input name="location" type="text" class="input-field" placeholder="Work Location" required 
                           style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; height: 48px;">
                </div>

                <div class="form-group mb-0">
                    <input name="time" type="datetime-local" class="input-field" required 
                           style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; height: 48px;">
                </div>

                <button type="submit" class="btn" style="background: white; color: var(--primary); font-weight: 800; border: none; padding: 1rem; margin-top: 0.5rem; transition: transform 0.2s;">
                    <span class="material-symbols-outlined">bolt</span>
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
