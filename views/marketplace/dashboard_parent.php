<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Main Content: Active and Recent Jobs -->
    <div class="md:col-span-2 flex flex-col gap-6">
        <div class="grid grid-cols-3 gap-6">
            <div class="card stat-card cursor-pointer hover-scale" onclick="location.href='/parent/jobs.php?status=active'">
                <span class="material-symbols-outlined stat-icon" style="color: var(--warning);">pending_actions</span>
                <p class="stat-label">Active Jobs</p>
                <p class="stat-value"><?= count(array_filter($jobs ?? [], fn($j) => $j['status'] === 'active')); ?></p>
            </div>
            <div class="card stat-card cursor-pointer hover-scale" onclick="location.href='/parent/jobs.php'">
                <span class="material-symbols-outlined stat-icon" style="color: var(--info);">assignment</span>
                <p class="stat-label">Total Posted</p>
                <p class="stat-value"><?= count($jobs ?? []); ?></p>
            </div>
            <div class="card stat-card cursor-pointer hover-scale" onclick="location.href='/parent/payments.php'">
                <span class="material-symbols-outlined stat-icon" style="color: var(--success);">payments</span>
                <p class="stat-label">Total Spend</p>
                <p class="stat-value"><?= number_format(array_sum(array_column($jobs ?? [], 'total_cost'))); ?></p>
            </div>
        </div>

        <!-- Active Jobs Section -->
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-6 border-b flex justify-between items-center">
                <h2 class="card-title">Current Active Work</h2>
                <a href="/parent/jobs.php?status=active" class="text-xs text-primary font-600 hover:underline">View All</a>
            </div>
            <div class="p-6">
                <?php $activeJobs = array_filter($jobs ?? [], fn($j) => $j['status'] === 'active'); ?>
                <?php if (empty($activeJobs)): ?>
                    <div class="text-center py-10 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-sm mb-4 text-slate-300">
                            <span class="material-symbols-outlined" style="font-size: 2rem;">rocket</span>
                        </div>
                        <p class="text-slate-500 font-600">No jobs currently in progress.</p>
                        <p class="text-[11px] text-muted mt-1">Hire a provider to see them here.</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($activeJobs as $job): ?>
                            <div class="flex justify-between items-center p-4 border rounded-xl hover:shadow-md transition-all cursor-pointer" onclick="if(event.target.tagName !== 'BUTTON' && event.target.tagName !== 'A' && !event.target.closest('form')) location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
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
            <div class="card-header p-6 border-b bg-primary-soft">
                <div class="flex justify-between items-center">
                    <h2 class="card-title">Open Jobs & Applicants</h2>
                    <span class="badge badge-primary"><?= count(array_filter($jobs ?? [], fn($j) => $j['status'] === 'open')); ?> Open</span>
                </div>
            </div>
            <div class="p-6">
                <?php $openJobs = array_filter($jobs ?? [], fn($j) => $j['status'] === 'open'); ?>
                <?php if (empty($openJobs)): ?>
                    <div class="text-center py-10 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-sm mb-4 text-slate-300">
                            <span class="material-symbols-outlined" style="font-size: 2rem;">assignment_add</span>
                        </div>
                        <p class="text-slate-500 font-600">No open jobs pending applicants.</p>
                        <p class="text-[11px] text-muted mt-1">Post a requirement to get started.</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($openJobs as $job): ?>
                            <div class="border rounded-xl p-4 hover:border-primary transition-all cursor-pointer" onclick="if(event.target.tagName !== 'BUTTON' && event.target.tagName !== 'A' && !event.target.closest('form')) location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-600 text-lg"><?= escape($job['service_type']); ?></h3>
                                        <p class="text-sm text-muted">Cost: <?= escape($job['total_cost'] ?? 0); ?> • Rate: $<?= escape($job['rate'] ?? 0); ?>/hr</p>
                                    </div>
                                    <span class="badge badge-info">Open</span>
                                </div>
                                <div class="border-t pt-4" onclick="event.stopPropagation()">
                                    <h4 class="font-700 text-sm mb-4 text-slate-800">Applicants:</h4>
                                    <?php if (empty($job['applicants'])): ?>
                                        <p class="text-sm text-muted italic">No applicants yet.</p>
                                    <?php else: ?>
                                        <div class="flex flex-col gap-4">
                                            <?php foreach ($job['applicants'] as $applicant): ?>
                                                <?php if ($applicant['status'] === 'pending'): ?>
                                                    <div class="bg-gray-50 border rounded-2xl p-5 hover:bg-white hover:shadow-lg transition-all border-slate-100 group">
                                                        <div class="flex justify-between items-start mb-4">
                                                            <div class="flex items-center gap-4">
                                                                <div class="user-avatar-rect" style="width: 56px; height: 56px; border-radius: 14px;">
                                                                    <?php if (!empty($applicant['profile_data']['profile_photo'])): ?>
                                                                        <img src="<?= escape($applicant['profile_data']['profile_photo']); ?>" style="width:100%; height:100%; object-fit: cover;">
                                                                    <?php else: ?>
                                                                        <?= mb_substr(escape($applicant['user_data']['name'] ?? 'P'), 0, 1); ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div>
                                                                    <a href="/provider/view.php?id=<?= escape((string)$applicant['provider_id']); ?>" class="font-800 text-lg hover:text-primary transition-colors block leading-tight"><?= escape($applicant['user_data']['name'] ?? 'Provider'); ?></a>
                                                                    <div class="flex items-center gap-3 mt-1.5">
                                                                        <div class="flex items-center gap-1 bg-warning-soft px-2 py-0.5 rounded text-warning font-700 text-xs">
                                                                            <span class="material-symbols-outlined" style="font-size: 14px;">star</span>
                                                                            <?= number_format((float)($applicant['profile_data']['rating'] ?? 0), 1); ?>
                                                                        </div>
                                                                        <span class="text-xs text-muted font-600 flex items-center gap-1">
                                                                            <span class="material-symbols-outlined" style="font-size: 14px;">history</span>
                                                                            <?= escape($applicant['profile_data']['experience'] ?? 'N/A'); ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-lg font-800 text-primary mb-0"><?= escape($applicant['profile_data']['rate'] ?? '0'); ?> <span class="text-[10px] font-600 uppercase">ETB/hr</span></p>
                                                                <p class="text-[10px] text-muted font-700 uppercase tracking-wider"><?= escape($applicant['profile_data']['location'] ?? 'Unknown'); ?></p>
                                                            </div>
                                                        </div>

                                                        <?php if (!empty($applicant['cover_letter'])): ?>
                                                            <div class="bg-white border rounded-xl p-4 mb-4 text-sm text-slate-600 italic leading-relaxed relative">
                                                                <span class="material-symbols-outlined absolute -top-2 -left-2 bg-white text-primary rounded-full shadow-sm" style="font-size: 20px;">format_quote</span>
                                                                <?= nl2br(escape($applicant['cover_letter'])); ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($applicant['availability']) || !empty($applicant['timeline'])): ?>
                                                            <div class="flex gap-4 mb-5 text-xs">
                                                                <?php if (!empty($applicant['availability'])): ?>
                                                                    <div class="flex items-center gap-1.5 text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">
                                                                        <span class="material-symbols-outlined" style="font-size: 14px;">schedule</span>
                                                                        <span class="font-600"><?= escape($applicant['availability']); ?></span>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($applicant['timeline'])): ?>
                                                                    <div class="flex items-center gap-1.5 text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">
                                                                        <span class="material-symbols-outlined" style="font-size: 14px;">timer</span>
                                                                        <span class="font-600"><?= escape($applicant['timeline']); ?></span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="flex gap-3 mt-4">
                                                            <a href="/provider/view.php?id=<?= escape((string)$applicant['provider_id']); ?>" class="btn btn-outline btn-sm flex-1 font-700">View Profile</a>
                                                            <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-ghost btn-sm flex-1 font-700 gap-2">
                                                                <span class="material-symbols-outlined" style="font-size: 16px;">chat</span> Message
                                                            </a>
                                                            <form action="/jobs/accept" method="POST" class="flex-[1.5]">
                                                                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                                                <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                                                <input type="hidden" name="provider_id" value="<?= escape($applicant['provider_id']); ?>">
                                                                <button type="submit" class="btn btn-success btn-sm w-full font-800 gap-2">
                                                                    <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span> Hire Now
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
            <div class="card-header p-6 border-b flex justify-between items-center">
                <h2 class="card-title">Job History</h2>
                <a href="/parent/jobs.php" class="text-xs text-primary font-600 hover:underline">View All</a>
            </div>
            <div class="p-6">
                <?php if (empty($jobs)): ?>
                    <p class="text-muted text-center py-4">Your job history will appear here.</p>
                <?php else: ?>
                    <div class="flex flex-col gap-3">
                        <?php foreach (array_slice(array_reverse($jobs), 0, 5) as $job): ?>
                            <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors" onclick="location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-muted" style="font-size: 18px;">history</span>
                                    <span class="font-500"><?= escape($job['service_type']); ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-<?= $job['status'] === 'completed' ? 'success' : ($job['status'] === 'active' ? 'warning' : 'info'); ?>">
                                        <?= escape($job['status']); ?>
                                    </span>
                                    <span class="material-symbols-outlined text-muted" style="font-size: 18px;">chevron_right</span>
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
        <div class="card p-0 overflow-hidden border-none shadow-xl">
            <div style="background: var(--grad-primary); padding: 2rem; position: relative; overflow: hidden;">
                <span class="material-symbols-outlined" style="position: absolute; right: -20px; top: -10px; font-size: 8rem; opacity: 0.1; color: white; transform: rotate(-15deg);">rocket_launch</span>
                <h2 class="text-2xl font-900 text-white mb-1 relative">Post a Job</h2>
                <p class="text-sm text-white/80 font-500 relative">Get help in minutes</p>
            </div>
            
            <form action="/jobs" method="POST" class="p-8 flex flex-col gap-6 bg-white">
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                
                <!-- Section 1: Basics -->
                <div>
                    <h3 class="text-[10px] font-800 uppercase tracking-widest text-slate-400 mb-3">Service Details</h3>
                    <div class="form-group mb-4">
                        <label class="text-xs font-700 text-slate-700 mb-1.5 block">Service Type</label>
                        <input name="service_type" type="text" class="input-field h-11 text-sm" placeholder="e.g. Baby Sitting, Cooking" required value="<?= escape(old('service_type')); ?>">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group mb-0">
                            <label class="text-xs font-700 text-slate-700 mb-1.5 block">Hourly Rate (ETB)</label>
                            <input name="rate" type="number" step="0.01" class="input-field h-11 text-sm" placeholder="0.00" required value="<?= escape(old('rate')); ?>">
                        </div>
                        <div class="form-group mb-0">
                            <label class="text-xs font-700 text-slate-700 mb-1.5 block">Hours Needed</label>
                            <input name="duration" type="number" step="0.5" id="post_duration" class="input-field h-11 text-sm" placeholder="1.0" required value="<?= escape(old('duration')); ?>">
                        </div>
                    </div>
                </div>

                <!-- Live Estimate -->
                <div id="cost_estimate" class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-4 text-center" style="display: none;">
                    <p class="text-[10px] font-800 uppercase tracking-widest text-slate-400 mb-1">Estimated Total Cost</p>
                    <p class="text-2xl font-900 text-primary"><span id="estimate_val">0</span> <span class="text-xs font-700 text-slate-400">ETB</span></p>
                </div>

                <!-- Section 2: Logistics -->
                <div>
                    <h3 class="text-[10px] font-800 uppercase tracking-widest text-slate-400 mb-3">Logistics & Timing</h3>
                    <div class="form-group mb-4">
                        <label class="text-xs font-700 text-slate-700 mb-1.5 block">Location</label>
                        <input name="location" type="text" class="input-field h-11 text-sm" placeholder="Where is the work?" required value="<?= escape(old('location')); ?>">
                    </div>
                    <div class="form-group mb-4">
                        <label class="text-xs font-700 text-slate-700 mb-1.5 block">Scheduled Time</label>
                        <input name="time" type="datetime-local" class="input-field h-11 text-sm" required value="<?= escape(old('time')); ?>">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-700 text-slate-700 mb-1.5 block">Payment Method</label>
                        <select name="payment_method" class="input-field h-11 text-sm" required>
                            <option value="" disabled selected>Choose payment...</option>
                            <option value="cash">Cash</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                        </select>
                    </div>
                </div>

                <!-- Section 3: Instructions -->
                <div>
                    <h3 class="text-[10px] font-800 uppercase tracking-widest text-slate-400 mb-3">Requirements</h3>
                    <div class="form-group mb-0">
                        <label class="text-xs font-700 text-slate-700 mb-1.5 block">Special Instructions</label>
                        <textarea name="instructions" class="input-field text-sm p-4" placeholder="Any special needs or details?" required style="height: 100px; resize: none;"><?= escape(old('instructions')); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full py-4 font-900 text-lg shadow-lg hover:shadow-primary/20 transition-all gap-2 mt-2">
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

        <?php if (!empty($recommended)): ?>
        <div class="card p-0 overflow-hidden">
            <div class="card-header p-5 border-b bg-gray-50/50">
                <h3 class="font-800 text-xs uppercase tracking-widest text-slate-500">Recommended for You</h3>
            </div>
            <div class="p-5">
                <div class="flex flex-col gap-4">
                    <?php foreach ($recommended as $rec): ?>
                        <div class="flex items-center gap-4 group cursor-pointer" onclick="location.href='/provider/view.php?id=<?= escape((string)$rec['user_id']); ?>'">
                            <div class="user-avatar-rect" style="width: 50px; height: 50px; border-radius: 12px; flex-shrink: 0;">
                                <?php if (!empty($rec['profile_photo'])): ?>
                                    <img src="<?= escape($rec['profile_photo']); ?>" style="width:100%; height:100%; object-fit: cover;">
                                <?php else: ?>
                                    <?= mb_substr(escape($rec['full_name'] ?? 'P'), 0, 1); ?>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-width-0">
                                <p class="text-sm font-700 m-0 group-hover:text-primary transition-colors truncate"><?= escape($rec['full_name'] ?? 'Provider'); ?></p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <div class="flex items-center gap-0.5 text-warning">
                                        <span class="material-symbols-outlined" style="font-size: 14px;">star</span>
                                        <span class="text-[11px] font-800"><?= number_format((float)($rec['rating'] ?? 0), 1); ?></span>
                                    </div>
                                    <span class="text-[10px] text-muted font-600"><?= escape($rec['location'] ?? 'Unknown'); ?></span>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-slate-300 group-hover:translate-x-1 transition-transform" style="font-size: 20px;">chevron_right</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="/servants" class="btn btn-outline btn-sm w-full mt-6 font-700">Explore More</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

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
