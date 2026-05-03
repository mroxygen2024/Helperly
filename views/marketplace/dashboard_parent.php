<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Main Content: Active and Recent Jobs -->
    <div class="md:col-span-2 flex flex-col gap-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="card stat-card cursor-pointer" onclick="location.href='/parent/jobs.php?status=active'">
                <span class="material-symbols-outlined stat-icon text-warning">pending_actions</span>
                <p class="stat-label">Active Jobs</p>
                <p class="stat-value"><?= count(array_filter($jobs ?? [], fn($j) => $j['status'] === 'active')); ?></p>
            </div>
            <div class="card stat-card cursor-pointer" onclick="location.href='/parent/jobs.php'">
                <span class="material-symbols-outlined stat-icon text-info">assignment</span>
                <p class="stat-label">Total Posted</p>
                <p class="stat-value"><?= count($jobs ?? []); ?></p>
            </div>
            <div class="card stat-card cursor-pointer" onclick="location.href='/parent/payments.php'">
                <span class="material-symbols-outlined stat-icon text-success">payments</span>
                <p class="stat-label">Total Spend</p>
                <p class="stat-value"><?= number_format(array_sum(array_column($jobs ?? [], 'total_cost'))); ?></p>
            </div>
        </div>

        <!-- Active Jobs Section -->
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b flex justify-between items-center mb-0">
                <h2 class="card-title">Current Active Work</h2>
                <a href="/parent/jobs.php?status=active" class="text-xs text-primary font-bold hover:underline">View All</a>
            </div>
            <div class="p-6">
                <?php $activeJobs = array_filter($jobs ?? [], fn($j) => $j['status'] === 'active'); ?>
                <?php if (empty($activeJobs)): ?>
                    <div class="text-center py-10 bg-neutral-50 rounded-xl border-2 border-dashed border-neutral-200">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-sm mb-4 text-neutral-300">
                            <span class="material-symbols-outlined text-3xl">rocket</span>
                        </div>
                        <p class="text-neutral-500 font-bold">No jobs currently in progress.</p>
                        <p class="text-xs text-muted mt-1">Hire a provider to see them here.</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($activeJobs as $job): ?>
                            <div class="flex flex-wrap justify-between items-center p-4 border rounded-xl hover:shadow-md transition-all cursor-pointer gap-4" onclick="if(event.target.tagName !== 'BUTTON' && event.target.tagName !== 'A' && !event.target.closest('form')) location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
                                <div class="flex items-center gap-4">
                                    <div class="bg-warning-light p-3 rounded-lg">
                                        <span class="material-symbols-outlined text-warning">sync</span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold"><?= escape($job['service_type']); ?></h3>
                                        <p class="text-sm text-muted"><?= escape($job['selected_provider']['name'] ?? 'Provider assigned'); ?></p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form action="/jobs/confirm" method="POST" onclick="event.stopPropagation()">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                        <button type="submit" class="btn btn-primary btn-sm" <?= ($job['parent_confirmed'] ?? false) ? 'disabled' : ''; ?>>
                                            <?= ($job['parent_confirmed'] ?? false) ? 'Finalizing...' : 'Confirm Completion'; ?>
                                        </button>
                                    </form>
                                    <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm" onclick="event.stopPropagation()">Chat</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Open Jobs & Applications -->
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b bg-primary-50 mb-0">
                <div class="flex justify-between items-center">
                    <h2 class="card-title">Open Jobs & Applicants</h2>
                    <span class="badge badge-primary"><?= count(array_filter($jobs ?? [], fn($j) => $j['status'] === 'open')); ?> Open</span>
                </div>
            </div>
            <div class="p-6">
                <?php $openJobs = array_filter($jobs ?? [], fn($j) => $j['status'] === 'open'); ?>
                <?php if (empty($openJobs)): ?>
                    <div class="text-center py-10 bg-neutral-50 rounded-xl border-2 border-dashed border-neutral-200">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-sm mb-4 text-neutral-300">
                            <span class="material-symbols-outlined text-3xl">assignment_add</span>
                        </div>
                        <p class="text-neutral-500 font-bold">No open jobs pending applicants.</p>
                        <p class="text-xs text-muted mt-1">Post a requirement to get started.</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-6">
                        <?php foreach ($openJobs as $job): ?>
                            <div class="border rounded-xl p-4 hover:border-primary transition-all cursor-pointer" onclick="if(event.target.tagName !== 'BUTTON' && event.target.tagName !== 'A' && !event.target.closest('form')) location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold text-lg"><?= escape($job['service_type']); ?></h3>
                                        <p class="text-sm text-muted">Cost: <?= escape($job['total_cost'] ?? 0); ?> • Rate: $<?= escape($job['rate'] ?? 0); ?>/hr</p>
                                    </div>
                                    <span class="badge badge-info">Open</span>
                                </div>
                                <div class="border-t pt-4" onclick="event.stopPropagation()">
                                    <h4 class="font-bold text-sm mb-4 text-neutral-800">Applicants:</h4>
                                    <?php if (empty($job['applicants'])): ?>
                                        <p class="text-sm text-muted italic">No applicants yet.</p>
                                    <?php else: ?>
                                        <div class="flex flex-col gap-4">
                                            <?php foreach ($job['applicants'] as $applicant): ?>
                                                <?php if ($applicant['status'] === 'pending'): ?>
                                                    <div class="bg-neutral-50 border rounded-xl p-5 hover:bg-white hover:shadow-lg transition-all border-neutral-100 group">
                                                        <div class="flex flex-wrap justify-between items-start mb-4 gap-4">
                                                            <div class="flex items-center gap-4">
                                                                <?php if (!empty($applicant['profile_data']['profile_photo'])): ?>
                                                                    <img src="<?= escape($applicant['profile_data']['profile_photo']); ?>" class="avatar avatar-lg rounded-lg">
                                                                <?php else: ?>
                                                                    <div class="avatar avatar-lg rounded-lg">
                                                                        <?= mb_substr(escape($applicant['user_data']['name'] ?? 'P'), 0, 1); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <a href="/provider/view.php?id=<?= escape((string)$applicant['provider_id']); ?>" class="font-extrabold text-lg hover:text-primary transition-colors block leading-tight"><?= escape($applicant['user_data']['name'] ?? 'Provider'); ?></a>
                                                                    <div class="flex items-center gap-3 mt-1.5">
                                                                        <div class="flex items-center gap-1 bg-warning-light px-2 py-0.5 rounded text-warning-dark font-bold text-xs">
                                                                            <span class="material-symbols-outlined" style="font-size: 14px;">star</span>
                                                                            <?= number_format((float)($applicant['profile_data']['rating'] ?? 0), 1); ?>
                                                                        </div>
                                                                        <span class="text-xs text-muted font-semibold flex items-center gap-1">
                                                                            <span class="material-symbols-outlined" style="font-size: 14px;">history</span>
                                                                            <?= escape($applicant['profile_data']['experience'] ?? 'N/A'); ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-xl font-extrabold text-primary mb-0"><?= escape($applicant['profile_data']['rate'] ?? '0'); ?> <span class="text-xs font-semibold uppercase">ETB/hr</span></p>
                                                                <p class="text-xs text-muted font-bold uppercase tracking-wider"><?= escape($applicant['profile_data']['location'] ?? 'Unknown'); ?></p>
                                                            </div>
                                                        </div>

                                                        <?php if (!empty($applicant['cover_letter'])): ?>
                                                            <div class="bg-white border rounded-lg p-4 mb-4 text-sm text-neutral-600 italic leading-relaxed relative">
                                                                <span class="material-symbols-outlined absolute -top-2 -left-2 bg-white text-primary rounded-full shadow-sm" style="font-size: 20px;">format_quote</span>
                                                                <?= nl2br(escape($applicant['cover_letter'])); ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="flex flex-wrap gap-3 mt-4">
                                                            <a href="/provider/view.php?id=<?= escape((string)$applicant['provider_id']); ?>" class="btn btn-outline btn-sm flex-1">View Profile</a>
                                                            <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm flex-1">
                                                                <span class="material-symbols-outlined" style="font-size: 16px;">chat</span> Message
                                                            </a>
                                                            <form action="/jobs/accept" method="POST" class="flex-[1.5]">
                                                                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                                                <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                                                <input type="hidden" name="provider_id" value="<?= escape($applicant['provider_id']); ?>">
                                                                <button type="submit" class="btn btn-primary btn-sm w-full font-extrabold">
                                                                    Hire Now
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
    </div>

    <!-- Sidebar: Quick Post & Actions -->
    <div class="flex flex-col gap-6">
        <div class="card p-0 overflow-hidden shadow-xl border-none">
            <div class="p-8 text-white" style="background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-800) 100%);">
                <h2 class="text-2xl font-extrabold mb-1">Post a Job</h2>
                <p class="text-sm opacity-80 font-medium">Get help in minutes</p>
            </div>
            
            <form action="/jobs" method="POST" class="p-6 flex flex-col gap-6 bg-white">
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                
                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-neutral-400 mb-4">Service Details</h3>
                    <div class="input-group">
                        <label class="label">Service Type</label>
                        <input name="service_type" type="text" class="input" placeholder="e.g. Baby Sitting, Cooking" required value="<?= escape(old('service_type')); ?>">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="input-group m-0">
                            <label class="label">Rate (ETB)</label>
                            <input name="rate" type="number" step="0.01" class="input" placeholder="0.00" required value="<?= escape(old('rate')); ?>">
                        </div>
                        <div class="input-group m-0">
                            <label class="label">Hours</label>
                            <input name="duration" type="number" step="0.5" id="post_duration" class="input" placeholder="1.0" required value="<?= escape(old('duration')); ?>">
                        </div>
                    </div>
                </div>

                <div id="cost_estimate" class="bg-neutral-50 border-2 border-dashed border-neutral-200 rounded-xl p-4 text-center hidden">
                    <p class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-1">Estimated Total</p>
                    <p class="text-2xl font-extrabold text-primary"><span id="estimate_val">0</span> <span class="text-xs font-bold text-neutral-400">ETB</span></p>
                </div>

                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-neutral-400 mb-4">Logistics</h3>
                    <div class="input-group">
                        <label class="label">Location</label>
                        <input name="location" type="text" class="input" placeholder="Where is the work?" required value="<?= escape(old('location')); ?>">
                    </div>
                    <div class="input-group">
                        <label class="label">Scheduled Time</label>
                        <input name="time" type="datetime-local" class="input" required value="<?= escape(old('time')); ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full py-4 text-lg">
                    <span class="material-symbols-outlined">bolt</span>
                    Post Job Now
                </button>
            </form>
        </div>

        <div class="card">
            <div class="card-header border-b mb-4 pb-4">
                <h3 class="font-bold">Quick Links</h3>
            </div>
            <div class="flex flex-col gap-3">
                <a href="/servants" class="flex items-center justify-between p-3 rounded-lg hover:bg-neutral-50 border">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">search</span>
                        <span class="text-sm font-semibold">Browse Providers</span>
                    </div>
                    <span class="material-symbols-outlined text-muted text-lg">chevron_right</span>
                </a>
                <a href="/messages" class="flex items-center justify-between p-3 rounded-lg hover:bg-neutral-50 border">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">chat</span>
                        <span class="text-sm font-semibold">Inbox</span>
                    </div>
                    <span class="material-symbols-outlined text-muted text-lg">chevron_right</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    const rateInput = document.querySelector('input[name="rate"]');
    const durationInput = document.getElementById('post_duration');
    const estimateDiv = document.getElementById('cost_estimate');
    const estimateVal = document.getElementById('estimate_val');

    const updateEstimate = () => {
        const rate = parseFloat(rateInput.value) || 0;
        const duration = parseFloat(durationInput.value) || 0;
        if (rate > 0 && duration > 0) {
            estimateVal.textContent = (rate * duration).toFixed(2);
            estimateDiv.classList.remove('hidden');
        } else {
            estimateDiv.classList.add('hidden');
        }
    };

    if (rateInput && durationInput) {
        rateInput.addEventListener('input', updateEstimate);
        durationInput.addEventListener('input', updateEstimate);
    }
})();
</script>

<script>
(() => {
    // Cost Calculation Logic
    const rateInput = document.querySelector('input[name="rate"]');
    const durationInput = document.getElementById('post_duration');
    const estimateDiv = document.getElementById('cost_estimate');
    const estimateVal = document.getElementById('estimate_val');

    const updateEstimate = () => {
        const rate = parseFloat(rateInput.value) || 0;
        const duration = parseFloat(durationInput.value) || 0;
        if (rate > 0 && duration > 0) {
            estimateVal.textContent = (rate * duration).toFixed(2);
            estimateDiv.style.display = 'block';
        } else {
            estimateDiv.style.display = 'none';
        }
    };

    if (rateInput && durationInput) {
        rateInput.addEventListener('input', updateEstimate);
        durationInput.addEventListener('input', updateEstimate);
    }
})();
</script>
