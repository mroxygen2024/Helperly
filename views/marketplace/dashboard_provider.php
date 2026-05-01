<!-- Profile Completion/Verification Warning -->
<?php if (!($isProfileComplete ?? true)): ?>
<div class="card p-4 border-none shadow-sm mb-6 bg-danger-soft">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-danger">error</span>
            <div>
                <p class="font-700 text-danger">Complete your profile</p>
                <p class="text-sm opacity-80">You need to complete your profile before you can apply for jobs.</p>
            </div>
        </div>
        <a href="/profile/servant" class="btn btn-primary btn-sm">Complete Now</a>
    </div>
</div>
<?php elseif (!($isVerified ?? false)): ?>
<div class="card p-4 border-none shadow-sm mb-6 bg-warning-soft">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-warning">verified_user</span>
            <div>
                <p class="font-700 text-warning">Get Verified</p>
                <p class="text-sm opacity-80">Verified providers are 5x more likely to be hired. Upload your documents now.</p>
            </div>
        </div>
        <a href="/profile/servant" class="btn btn-warning btn-sm">Start Verification</a>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="md:col-span-2 flex flex-col gap-6">
        <!-- Stats Row -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card p-4 flex flex-col items-center justify-center text-center hover-scale cursor-pointer" onclick="location.href='/jobs/available'">
                <p class="text-xs text-muted font-600 uppercase mb-1">Available</p>
                <p class="text-2xl font-800 text-primary"><?= (int)$stats['available_jobs']; ?></p>
            </div>
            <div class="card p-4 flex flex-col items-center justify-center text-center hover-scale cursor-pointer" onclick="location.href='/provider/jobs'">
                <p class="text-xs text-muted font-600 uppercase mb-1">Active</p>
                <p class="text-2xl font-800 text-warning"><?= (int)$stats['active_assignments']; ?></p>
            </div>
            <div class="card p-4 flex flex-col items-center justify-center text-center hover-scale cursor-pointer" onclick="location.href='/provider/applications'">
                <p class="text-xs text-muted font-600 uppercase mb-1">Applications</p>
                <p class="text-2xl font-800 text-info"><?= (int)$stats['applications']; ?></p>
            </div>
            <div class="card p-4 flex flex-col items-center justify-center text-center hover-scale cursor-pointer" onclick="location.href='/provider/view.php?id=<?= escape((string)$user['id']); ?>#reviews'">
                <p class="text-xs text-muted font-600 uppercase mb-1">Rating</p>
                <div class="flex items-center gap-1">
                    <span class="text-2xl font-800 text-success"><?= number_format((float)$stats['rating'], 1); ?></span>
                    <span class="material-symbols-outlined text-warning" style="font-size: 18px;">star</span>
                </div>
            </div>
        </div>

        <!-- Opportunities Section -->
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b flex justify-between items-center bg-primary-soft">
                <h2 class="card-title">Opportunities Near You</h2>
                <a href="/jobs/available" class="text-xs text-primary font-700 uppercase tracking-wider hover:underline">View All Jobs</a>
            </div>
            <div class="p-6">
                <?php if (empty($availableJobs)): ?>
                    <div class="text-center py-12">
                        <span class="material-symbols-outlined text-muted" style="font-size: 4rem;">search_off</span>
                        <p class="text-muted mt-4 text-lg">No jobs matching your profile right now.</p>
                        <p class="text-sm opacity-60">Try updating your skills or checking back later.</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach (array_slice($availableJobs, 0, 5) as $job): ?>
                            <div class="flex justify-between items-center p-5 border rounded-2xl hover:border-primary hover:shadow-md transition-all cursor-pointer group" onclick="if(event.target.tagName !== 'BUTTON' && event.target.tagName !== 'A' && !event.target.closest('form')) location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
                                <div class="flex items-center gap-5">
                                    <div class="bg-gray-100 p-4 rounded-xl group-hover:bg-primary-soft transition-colors">
                                        <span class="material-symbols-outlined text-slate-500 group-hover:text-primary">work</span>
                                    </div>
                                    <div>
                                        <h3 class="font-700 text-lg mb-1"><?= escape($job['service_type']); ?></h3>
                                        <div class="flex items-center gap-3 text-sm text-muted">
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined" style="font-size: 16px;">location_on</span> <?= escape($job['location']); ?></span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined" style="font-size: 16px;">schedule</span> <?= escape($job['duration']); ?> hrs</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right flex flex-col gap-2">
                                    <p class="font-800 text-lg text-primary"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> ETB</p>
                                    <?php 
                                        $appliedJobIds = array_map(fn($app) => (string)($app['job_id'] ?? ''), $applications ?? []);
                                        $hasApplied = in_array((string)$job['_id'], $appliedJobIds, true);
                                    ?>
                                    <?php if ($hasApplied): ?>
                                        <button class="btn btn-outline btn-sm px-6" disabled>Applied</button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-primary btn-sm px-6" 
                                                <?= !($isProfileComplete ?? true) ? 'disabled' : ''; ?>
                                                data-open-modal="apply_modal_<?= escape((string)$job['_id']); ?>">
                                            Apply Now
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Application Modal -->
                            <?php if (!$hasApplied): ?>
                            <div id="apply_modal_<?= escape((string)$job['_id']); ?>" class="modal-overlay" data-modal>
                                <div class="modal-content" style="max-width: 600px;">
                                    <div class="modal-header">
                                        <h2 class="card-title">Apply for <?= escape($job['service_type']); ?></h2>
                                        <button type="button" class="btn btn-ghost" data-close-modal="apply_modal_<?= escape((string)$job['_id']); ?>">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                    <form action="/jobs/apply" method="POST" class="p-6">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                        
                                        <div class="bg-primary-soft p-4 rounded-xl mb-6">
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="font-600 text-primary">Job Budget: <?= number_format((float)($job['total_cost'] ?? 0), 2); ?> ETB</span>
                                                <span class="text-muted"><?= (float)($job['duration'] ?? 0); ?> hours estimated</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="block text-sm font-700 mb-2">Cover Message (Optional)</label>
                                            <textarea name="cover_letter" class="input-field" placeholder="Tell the client why you're a great fit..." style="height: 120px;"></textarea>
                                            <p class="text-[11px] text-muted mt-1">Briefly explain your experience with this type of work.</p>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="form-group">
                                                <label class="block text-sm font-700 mb-2">Your Availability</label>
                                                <input type="text" name="availability" class="input-field" placeholder="e.g. Can start at 10 AM" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="block text-sm font-700 mb-2">Estimated Timeline</label>
                                                <input type="text" name="timeline" class="input-field" placeholder="e.g. 3-4 hours" required>
                                            </div>
                                        </div>

                                        <div class="mt-8 flex gap-3">
                                            <button type="button" class="btn btn-outline flex-1" data-close-modal="apply_modal_<?= escape((string)$job['_id']); ?>">Cancel</button>
                                            <button type="submit" class="btn btn-primary flex-[2]">Submit Application</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Active Work -->
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b flex justify-between items-center">
                <h2 class="card-title">Active Assignments</h2>
                <a href="/provider/jobs" class="text-xs text-muted hover:text-primary transition-colors">Manage All</a>
            </div>
            <div class="p-6">
                <?php if (empty($activeWork)): ?>
                    <div class="text-center py-8 bg-gray-50 rounded-2xl border-2 border-dashed">
                        <p class="text-muted">No active assignments. Start applying!</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($activeWork as $job): ?>
                            <div class="flex justify-between items-center p-5 border rounded-2xl bg-white hover:shadow-sm transition-all cursor-pointer" onclick="if(event.target.tagName !== 'BUTTON' && event.target.tagName !== 'A' && !event.target.closest('form')) location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
                                <div class="flex items-center gap-4">
                                    <div class="user-avatar" style="width: 48px; height: 48px;">
                                        <?= mb_substr(escape($job['parent']['name'] ?? 'C'), 0, 1); ?>
                                    </div>
                                    <div>
                                        <h3 class="font-700"><?= escape($job['service_type']); ?></h3>
                                        <p class="text-xs text-muted">Client: <?= escape($job['parent']['name'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm" onclick="event.stopPropagation()">Chat</a>
                                    <form action="/jobs/confirm" method="POST" onclick="event.stopPropagation()">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                        <button type="submit" class="btn btn-success btn-sm" <?= ($job['provider_confirmed'] ?? false) ? 'disabled' : ''; ?>>
                                            <?= ($job['provider_confirmed'] ?? false) ? 'Waiting for Client' : 'Finish Job'; ?>
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
    <div class="flex flex-col gap-6">
        <!-- Earnings Card -->
        <div class="card overflow-hidden" style="background: var(--grad-secondary); border: none;">
            <div class="p-8 text-white relative">
                <span class="material-symbols-outlined" style="position: absolute; right: -10px; bottom: -10px; font-size: 6rem; opacity: 0.15; transform: rotate(15deg);">account_balance_wallet</span>
                <p class="text-sm opacity-80 font-600 uppercase tracking-widest mb-1">Total Earnings</p>
                <p class="text-4xl font-900 mb-6"><?= number_format((float)$stats['earnings'], 2); ?> <span class="text-lg font-600">ETB</span></p>
                <a href="/provider/payments" class="btn bg-white text-secondary w-full font-800 border-none hover:bg-opacity-90">Withdraw Funds</a>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="card p-6">
            <h3 class="font-800 text-sm uppercase tracking-widest text-muted mb-6 border-b pb-4">Performance</h3>
            <div class="flex flex-col gap-6">
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-600">Job Success</span>
                        <span class="text-success font-700">100%</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-success h-full" style="width: 100%;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-600">Profile Visibility</span>
                        <span class="text-primary font-700">High</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-primary h-full" style="width: 85%;"></div>
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t flex flex-col gap-4">
                <a href="/profile/servant" class="flex items-center justify-between text-sm font-600 hover:text-primary transition-colors">
                    <span>Manage Profile</span>
                    <span class="material-symbols-outlined" style="font-size: 18px;">chevron_right</span>
                </a>
                <a href="/provider/view.php?id=<?= escape((string)$user['id']); ?>" class="flex items-center justify-between text-sm font-600 hover:text-primary transition-colors">
                    <span>View Public Profile</span>
                    <span class="material-symbols-outlined" style="font-size: 18px;">chevron_right</span>
                </a>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b bg-gray-50">
                <h3 class="font-800 text-sm uppercase tracking-widest">Recent Applications</h3>
            </div>
            <div class="p-6">
                <?php if (empty($applications)): ?>
                    <p class="text-sm text-muted italic text-center">No active applications.</p>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach (array_slice($applications, 0, 3) as $app): ?>
                            <div class="flex flex-col gap-2">
                                <div class="flex justify-between items-start">
                                    <p class="text-sm font-700 m-0"><?= escape($app['job_data']['service_type'] ?? 'Job'); ?></p>
                                    <span class="badge badge-<?= $app['status'] === 'accepted' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'info'); ?> px-2 py-0.5 text-[10px]">
                                        <?= escape(ucfirst($app['status'])); ?>
                                    </span>
                                </div>
                                <p class="text-[11px] text-muted m-0 flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size: 12px;">history</span>
                                    Applied <?= isset($app['created_at']) ? (is_string($app['created_at']) ? date('M d', strtotime($app['created_at'])) : ($app['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $app['created_at']->toDateTime()->format('M d') : 'N/A')) : 'N/A'; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <a href="/provider/applications" class="btn btn-outline btn-sm w-full mt-6">View All Status</a>
            </div>
        </div>
    </div>
</div>
