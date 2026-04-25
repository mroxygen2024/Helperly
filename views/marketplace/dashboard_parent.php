<div class="grid grid-cols-3 mb-2">
    <div class="card stat-card">
        <p class="stat-label">My Posted Jobs</p>
        <p class="stat-value"><?= count($jobs ?? []); ?></p>
    </div>
    <div class="card stat-card">
        <p class="stat-label">Active Work</p>
        <p class="stat-value"><?= count(array_filter($jobs ?? [], fn($j) => $j['status'] === 'active')); ?></p>
    </div>
    <div class="card stat-card">
        <p class="stat-label">Spend</p>
        <p class="stat-value text-info"><?= array_sum(array_column($jobs ?? [], 'total_cost')); ?> BDT</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-4">
    <!-- Post Job Section -->
    <div class="col-span-1">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Post New Job</h2>
            </div>
            <form action="/jobs" method="POST" class="flex flex-col gap-4">
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                
                <div class="form-group">
                    <label class="label">Service Type</label>
                    <input name="service_type" type="text" class="input" placeholder="e.g. House Cleaning" required>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label class="label">Time</label>
                        <input name="time" type="datetime-local" class="input" required>
                    </div>
                    <div class="form-group">
                        <label class="label">Duration (Hrs)</label>
                        <input name="duration" type="number" step="0.5" class="input" placeholder="3" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label class="label">Rate/Hr</label>
                        <input name="hourly_rate" type="number" class="input" placeholder="500" required>
                    </div>
                    <div class="form-group">
                        <label class="label">Location</label>
                        <input name="location" type="text" class="input" placeholder="Dhaka" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="label">Instructions</label>
                    <textarea name="instructions" class="textarea" rows="3" placeholder="Important details..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    <span class="material-symbols-outlined">add</span>
                    Post Requirement
                </button>
            </form>
        </div>
    </div>

    <!-- My Jobs List -->
    <div style="grid-column: span 2;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">My Recent Jobs</h2>
                <a href="/servants" class="btn btn-outline btn-sm">Find More Servants</a>
            </div>

            <?php if (empty($jobs)): ?>
                <div class="text-center py-8">
                    <p class="text-muted">No jobs posted yet. Get started by posting your first requirement!</p>
                </div>
            <?php else: ?>
                <div class="flex flex-col gap-4">
                    <?php foreach (array_reverse($jobs) as $job): ?>
                        <div class="card border job-card <?= escape($job['status']); ?>" style="margin-bottom: 0;">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-600"><?= escape($job['service_type']); ?></h3>
                                    <p class="text-sm text-muted">
                                        <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">calendar_month</span>
                                        <?= isset($job['time']) && $job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('M d, H:i') : 'N/A'; ?>
                                    </p>
                                </div>
                                <span class="badge badge-<?= $job['status'] === 'active' ? 'warning' : ($job['status'] === 'completed' ? 'success' : 'info'); ?>">
                                    <?= escape($job['status']); ?>
                                </span>
                            </div>

                            <div class="flex gap-4 text-sm mb-4">
                                <span><strong><?= escape($job['duration']); ?></strong> hrs</span>
                                <span><strong><?= escape($job['hourly_rate']); ?></strong>/hr</span>
                                <span class="text-info font-600">Total: <?= escape($job['total_cost'] ?? 0); ?></span>
                            </div>

                            <?php if ($job['status'] === 'active'): ?>
                                <div class="bg-primary-soft p-4 rounded-lg flex justify-between items-center">
                                    <p class="text-sm">Job is in progress. Confirm completion when done.</p>
                                    <div class="flex gap-2">
                                        <form action="/jobs/confirm" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                            <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                            <button type="submit" class="btn btn-primary btn-sm" <?= ($job['parent_confirmed'] ?? false) ? 'disabled' : ''; ?>>
                                                <?= ($job['parent_confirmed'] ?? false) ? 'Waiting for Provider' : 'Confirm Done'; ?>
                                            </button>
                                        </form>
                                        <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm">Chat</a>
                                    </div>
                                </div>
                            <?php elseif ($job['status'] === 'open' && !empty($job['applicants'])): ?>
                                <div class="mt-4 border-t pt-4">
                                    <p class="text-sm font-600 mb-2">Applicants (<?= count($job['applicants']); ?>)</p>
                                    <div class="flex flex-col gap-2">
                                        <?php foreach ($job['applicants'] as $app): ?>
                                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded border">
                                                <div class="flex items-center gap-2">
                                                    <div class="user-avatar" style="width: 24px; height: 24px; font-size: 10px;"><?= mb_substr(escape($app['provider']['name']), 0, 1); ?></div>
                                                    <span class="text-sm font-500"><?= escape($app['provider']['name']); ?></span>
                                                </div>
                                                <div class="flex gap-2">
                                                    <form action="/jobs/accept" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                                        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                                        <input type="hidden" name="provider_id" value="<?= escape((string)$app['provider_id']); ?>">
                                                        <button type="submit" class="btn btn-primary btn-sm">Accept</button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($job['status'] === 'completed' && isset($job['payment']) && $job['payment']['status'] === 'unpaid'): ?>
                                <div class="mt-4 p-4 rounded-lg border border-warning bg-warning" style="background-opacity: 0.1; background: #fffbeb;">
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-600">Pending Payment: <?= escape($job['payment']['amount']); ?> BDT</p>
                                        <form action="/payments/pay" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                            <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Pay Now</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
