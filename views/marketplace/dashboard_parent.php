<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Main Content: Active and Recent Jobs -->
    <div class="lg:col-span-8 flex flex-col gap-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="stat-card cursor-pointer" onclick="location.href='/parent/jobs.php?status=active'">
                <div class="stat-icon" style="background: var(--warning-light); color: var(--warning-dark);">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
                <p class="stat-value"><?= count(array_filter($jobs ?? [], fn($j) => $j['status'] === 'active')); ?></p>
                <p class="stat-label">Active Jobs</p>
            </div>
            <div class="stat-card cursor-pointer" onclick="location.href='/parent/jobs.php'">
                <div class="stat-icon" style="background: var(--info-light); color: var(--info-dark);">
                    <span class="material-symbols-outlined">assignment</span>
                </div>
                <p class="stat-value"><?= count($jobs ?? []); ?></p>
                <p class="stat-label">Total Posted</p>
            </div>
            <div class="stat-card cursor-pointer" onclick="location.href='/parent/payments.php'">
                <div class="stat-icon" style="background: var(--success-light); color: var(--success-dark);">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <p class="stat-value"><?= number_format(array_sum(array_column($jobs ?? [], 'total_cost'))); ?></p>
                <p class="stat-label">Total Spend (ETB)</p>
            </div>
        </div>

        <!-- Active Jobs Section -->
        <div class="dashboard-card card p-0">
            <div class="p-6 border-b flex justify-between items-center bg-neutral-50/50">
                <h2 class="text-lg font-extrabold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">bolt</span>
                    Current Active Work
                </h2>
                <a href="/parent/jobs.php?status=active" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="p-6">
                <?php $activeJobs = array_filter($jobs ?? [], fn($j) => $j['status'] === 'active'); ?>
                <?php if (empty($activeJobs)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <span class="material-symbols-outlined">rocket_launch</span>
                        </div>
                        <h3 class="text-lg font-bold text-main">No jobs in progress</h3>
                        <p class="text-neutral-500 max-w-xs mx-auto mb-6">Once you hire a provider, their progress will appear here in real-time.</p>
                        <a href="/servants" class="btn btn-primary">Find Providers</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 gap-4">
                        <?php foreach ($activeJobs as $job): ?>
                            <div class="flex flex-wrap justify-between items-center p-5 border-2 border-neutral-50 rounded-2xl hover:border-primary-100 hover:bg-primary-50/30 transition-all cursor-pointer group" onclick="if(event.target.tagName !== 'BUTTON' && event.target.tagName !== 'A' && !event.target.closest('form')) location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 bg-white rounded-2xl shadow-sm border flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-warning" style="font-size: 32px;">sync</span>
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-lg leading-tight mb-1"><?= escape($job['service_type']); ?></h3>
                                        <div class="flex items-center gap-2">
                                            <div class="user-avatar" style="width: 24px; height: 24px; font-size: 10px; border: none;">
                                                <?= mb_substr(escape($job['selected_provider']['name'] ?? 'P'), 0, 1); ?>
                                            </div>
                                            <p class="text-sm font-bold text-neutral-500"><?= escape($job['selected_provider']['name'] ?? 'Assigning...'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm px-4">
                                        <span class="material-symbols-outlined text-sm">chat</span>
                                        Chat
                                    </a>
                                    <form action="/jobs/confirm" method="POST" onclick="event.stopPropagation()">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                        <button type="submit" class="btn btn-primary btn-sm px-6" <?= ($job['parent_confirmed'] ?? false) ? 'disabled' : ''; ?>>
                                            <?= ($job['parent_confirmed'] ?? false) ? 'Finalizing...' : 'Confirm'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Open Jobs & Applications -->
        <div class="dashboard-card card p-0 overflow-hidden">
            <div class="p-6 border-b bg-primary-600 text-white flex justify-between items-center">
                <h2 class="text-lg font-extrabold flex items-center gap-2">
                    <span class="material-symbols-outlined">assignment_ind</span>
                    Open Jobs & Applicants
                </h2>
                <span class="badge bg-white/20 text-white border-none"><?= count(array_filter($jobs ?? [], fn($j) => $j['status'] === 'open')); ?> Active Posts</span>
            </div>
            <div class="p-6">
                <?php $openJobs = array_filter($jobs ?? [], fn($j) => $j['status'] === 'open'); ?>
                <?php if (empty($openJobs)): ?>
                    <div class="empty-state bg-white">
                        <div class="empty-state-icon">
                            <span class="material-symbols-outlined">post_add</span>
                        </div>
                        <h3 class="text-lg font-bold">No open job posts</h3>
                        <p class="text-neutral-500 mb-6">Need help? Post a new job requirement to start receiving applications.</p>
                        <button class="btn btn-primary" onclick="document.querySelector('input[name=\'service_type\']').focus()">Post Now</button>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-8">
                        <?php foreach ($openJobs as $job): ?>
                            <div class="border-2 border-neutral-50 rounded-2xl p-6 hover:border-primary-200 transition-all bg-neutral-50/20">
                                <div class="flex justify-between items-start mb-6">
                                    <div>
                                        <h3 class="font-extrabold text-xl mb-1"><?= escape($job['service_type']); ?></h3>
                                        <div class="flex items-center gap-4 text-sm text-neutral-500 font-bold">
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">payments</span> <?= escape($job['total_cost'] ?? 0); ?> ETB</span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span> $<?= escape($job['rate'] ?? 0); ?>/hr</span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">location_on</span> <?= escape($job['location'] ?? 'Addis Ababa'); ?></span>
                                        </div>
                                    </div>
                                    <span class="badge badge-primary px-4 py-1">Accepting Apps</span>
                                </div>
                                
                                <div class="space-y-4">
                                    <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Top Applicants (<?= count($job['applicants'] ?? []); ?>)</h4>
                                    <?php if (empty($job['applicants'])): ?>
                                        <div class="p-8 border-2 border-dashed border-neutral-200 rounded-2xl text-center bg-white">
                                            <p class="text-sm text-neutral-400 font-bold">Waiting for providers to apply...</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="grid grid-cols-1 gap-4">
                                            <?php foreach ($job['applicants'] as $applicant): ?>
                                                <?php if ($applicant['status'] === 'pending'): ?>
                                                    <div class="bg-white border border-neutral-100 rounded-2xl p-5 hover:shadow-xl transition-all group relative overflow-hidden">
                                                        <div class="flex flex-wrap justify-between items-center gap-4 relative z-10">
                                                            <div class="flex items-center gap-4">
                                                                <?php if (!empty($applicant['profile_data']['profile_photo'])): ?>
                                                                    <img src="<?= escape($applicant['profile_data']['profile_photo']); ?>" class="user-avatar w-16 h-16 rounded-2xl">
                                                                <?php else: ?>
                                                                    <div class="user-avatar w-16 h-16 rounded-2xl text-xl">
                                                                        <?= mb_substr(escape($applicant['user_data']['name'] ?? 'P'), 0, 1); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <a href="/provider/view.php?id=<?= escape((string)$applicant['provider_id']); ?>" class="font-extrabold text-lg hover:text-primary transition-colors block mb-1"><?= escape($applicant['user_data']['name'] ?? 'Provider'); ?></a>
                                                                    <div class="flex items-center gap-3">
                                                                        <div class="flex items-center gap-1 bg-warning-light text-warning-dark px-2 py-0.5 rounded-lg text-xs font-black">
                                                                            <span class="material-symbols-outlined" style="font-size: 14px;">star</span>
                                                                            <?= number_format((float)($applicant['profile_data']['rating'] ?? 0), 1); ?>
                                                                        </div>
                                                                        <span class="text-xs font-bold text-neutral-400 flex items-center gap-1">
                                                                            <span class="material-symbols-outlined" style="font-size: 14px;">verified</span>
                                                                            <?= escape($applicant['profile_data']['experience'] ?? 'N/A'); ?> Exp
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-2xl font-black text-primary m-0"><?= escape($applicant['profile_data']['rate'] ?? '0'); ?><span class="text-xs ml-1 font-bold">ETB/hr</span></p>
                                                                <button class="btn btn-primary btn-sm mt-3 px-8" onclick="event.stopPropagation(); document.getElementById('hire_form_<?= $applicant['_id'] ?>').submit()">Hire Now</button>
                                                                <form id="hire_form_<?= $applicant['_id'] ?>" action="/jobs/accept" method="POST" class="hidden">
                                                                    <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                                                    <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                                                    <input type="hidden" name="provider_id" value="<?= escape($applicant['provider_id']); ?>">
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <?php if (!empty($applicant['cover_letter'])): ?>
                                                            <div class="mt-4 p-4 bg-neutral-50 rounded-xl text-sm text-neutral-600 font-medium leading-relaxed border-l-4 border-primary-200 italic">
                                                                "<?= nl2br(escape($applicant['cover_letter'])); ?>"
                                                            </div>
                                                        <?php endif; ?>
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
    <div class="lg:col-span-4 flex flex-col gap-8">
        <div class="dashboard-card card p-0 overflow-hidden border-none shadow-premium">
            <div class="p-8 text-white relative" style="background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-800) 100%);">
                <span class="material-symbols-outlined absolute right-[-20px] top-[-20px] opacity-10 text-[120px] rotate-12">add_task</span>
                <h2 class="text-2xl font-black mb-1">Post a New Job</h2>
                <p class="text-sm opacity-80 font-bold">Get connected with top helpers in Addis.</p>
            </div>
            
            <form action="/jobs" method="POST" class="p-8 bg-white space-y-6">
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                
                <div class="space-y-4">
                    <div class="input-group m-0">
                        <label class="label text-xs uppercase tracking-widest text-neutral-400">What do you need help with?</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400">work</span>
                            <input name="service_type" type="text" class="input pl-12" placeholder="e.g. Home Cleaning, Nanny" required value="<?= escape(old('service_type')); ?>">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="input-group m-0">
                            <label class="label text-xs uppercase tracking-widest text-neutral-400">Budget (ETB/hr)</label>
                            <input name="rate" type="number" step="0.01" class="input" placeholder="0.00" required value="<?= escape(old('rate')); ?>">
                        </div>
                        <div class="input-group m-0">
                            <label class="label text-xs uppercase tracking-widest text-neutral-400">Hours</label>
                            <input name="duration" type="number" step="0.5" id="post_duration" class="input" placeholder="1.0" required value="<?= escape(old('duration')); ?>">
                        </div>
                    </div>

                    <div id="cost_estimate" class="bg-primary-50 border-2 border-primary-100 rounded-2xl p-4 text-center hidden animate-bounce-in">
                        <p class="text-[10px] font-black uppercase tracking-widest text-primary-400 mb-1">Total Estimated Investment</p>
                        <p class="text-2xl font-black text-primary"><span id="estimate_val">0</span> <span class="text-xs font-bold">ETB</span></p>
                    </div>

                    <div class="input-group m-0">
                        <label class="label text-xs uppercase tracking-widest text-neutral-400">Location</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400">location_on</span>
                            <input name="location" type="text" class="input pl-12" placeholder="Sub-city, Neighborhood" required value="<?= escape(old('location')); ?>">
                        </div>
                    </div>
                    
                    <div class="input-group m-0">
                        <label class="label text-xs uppercase tracking-widest text-neutral-400">Start Time</label>
                        <input name="time" type="datetime-local" class="input" required value="<?= escape(old('time')); ?>">
                    </div>

                    <div class="input-group m-0">
                        <label class="label text-xs uppercase tracking-widest text-neutral-400">Special Instructions</label>
                        <textarea name="instructions" class="textarea w-full" placeholder="e.g. Please bring cleaning supplies, be mindful of the cat..."><?= escape(old('instructions')); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full py-4 text-lg shadow-premium group">
                    Post Requirement
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </form>
        </div>

        <div class="dashboard-card card p-6">
            <h3 class="font-black text-sm uppercase tracking-widest text-neutral-400 mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">explore</span>
                Marketplace Links
            </h3>
            <div class="space-y-3">
                <a href="/servants" class="flex items-center justify-between p-4 rounded-2xl hover:bg-neutral-50 border-2 border-neutral-50 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">person_search</span>
                        </div>
                        <span class="text-sm font-extrabold text-main">Browse Providers</span>
                    </div>
                    <span class="material-symbols-outlined text-neutral-300">chevron_right</span>
                </a>
                <a href="/messages" class="flex items-center justify-between p-4 rounded-2xl hover:bg-neutral-50 border-2 border-neutral-50 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-info-50 text-info flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">forum</span>
                        </div>
                        <span class="text-sm font-extrabold text-main">Check Messages</span>
                    </div>
                    <span class="material-symbols-outlined text-neutral-300">chevron_right</span>
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
