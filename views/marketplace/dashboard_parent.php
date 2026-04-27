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
                                                <?php if ($applicant['status'] === 'pending'): ?>
                                                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                                        <div class="flex items-center gap-3">
                                                            <div class="bg-white p-2 rounded-full border shadow-sm flex items-center justify-center">
                                                                <span class="material-symbols-outlined text-primary" style="font-size: 20px;">person</span>
                                                            </div>
                                                            <div>
                                                                <p class="font-600 text-sm m-0"><?= escape($applicant['user_data']['name'] ?? 'Provider'); ?></p>
                                                                <div class="flex items-center gap-1 mt-0.5">
                                                                    <span class="material-symbols-outlined text-warning" style="font-size: 14px;">star</span>
                                                                    <span class="text-xs text-muted"><?= number_format((float)($applicant['profile_data']['rating'] ?? 0), 1); ?> (<?= (int)($applicant['profile_data']['rating_count'] ?? 0); ?> reviews)</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <button type="button" class="btn btn-outline btn-sm" data-open-modal="applicant_modal_<?= escape((string)$applicant['_id']); ?>" title="View Profile">
                                                                <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                                            </button>
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
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-<?= $job['status'] === 'completed' ? 'success' : ($job['status'] === 'active' ? 'warning' : 'info'); ?>">
                                        <?= escape($job['status']); ?>
                                    </span>
                                    <button type="button" class="btn btn-ghost btn-sm" data-open-modal="job_modal_<?= escape((string)$job['_id']); ?>" title="View Details">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">info</span>
                                    </button>
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

<!-- Job Details Modals -->
<?php foreach ($jobs as $job): ?>
    <div id="job_modal_<?= escape((string)$job['_id']); ?>" class="modal-overlay" data-modal>
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div class="flex items-center gap-4">
                    <div class="user-avatar-rect" style="background: var(--grad-primary);">
                        <span class="material-symbols-outlined" style="color: white;">work</span>
                    </div>
                    <div>
                        <h2 class="card-title" style="margin: 0;"><?= escape($job['service_type']); ?> Details</h2>
                        <span class="badge badge-<?= $job['status'] === 'completed' ? 'success' : ($job['status'] === 'active' ? 'warning' : 'info'); ?>"><?= escape(ucfirst($job['status'])); ?></span>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-sm" data-close-modal="job_modal_<?= escape((string)$job['_id']); ?>" style="border:none; padding: 0.5rem; border-radius: 50%;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="modal-body" style="background: #F8FAFC; padding: 2rem;">
                <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Location</span>
                        <span class="font-700"><?= escape($job['location'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Start Time</span>
                        <span class="font-700"><?= isset($job['time']) ? (is_string($job['time']) ? date('M d, Y h:i A', strtotime($job['time'])) : ($job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('M d, Y h:i A') : 'N/A')) : 'N/A'; ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Duration</span>
                        <span class="font-700"><?= escape($job['duration'] ?? 'N/A'); ?> Hours</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Total Cost</span>
                        <span class="font-700 text-primary"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> BDT</span>
                    </div>
                </div>
                <div class="flex flex-col mb-4">
                    <span class="text-xs text-muted font-600 mb-1">Instructions</span>
                    <div class="p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-700" style="white-space: pre-wrap;"><?= escape($job['instructions'] ?? 'No special instructions.'); ?></div>
                </div>
                <?php if (isset($job['selected_provider'])): ?>
                <div class="p-4 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                         <div class="user-avatar" style="width: 32px; height: 32px;"><?= mb_substr(escape($job['selected_provider']['name'] ?? 'P'), 0, 1); ?></div>
                         <div>
                             <p class="text-xs text-muted font-600">Assigned Provider</p>
                             <p class="text-sm font-700"><?= escape($job['selected_provider']['name'] ?? 'N/A'); ?></p>
                         </div>
                    </div>
                    <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm">Chat</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline w-full" data-close-modal="job_modal_<?= escape((string)$job['_id']); ?>">Close</button>
            </div>
        </div>
    </div>
    
    <!-- Applicant Profile Modals -->
    <?php if (!empty($job['applicants'])): ?>
        <?php foreach ($job['applicants'] as $applicant): ?>
            <div id="applicant_modal_<?= escape((string)$applicant['_id']); ?>" class="modal-overlay" data-modal>
                <div class="modal-content" style="max-width: 600px;">
                    <div class="modal-header">
                        <div class="flex items-center gap-4">
                            <div class="user-avatar-rect" style="width: 64px; height: 64px; background: var(--grad-primary);">
                                <?php if (!empty($applicant['profile_data']['profile_photo'])): ?>
                                    <img src="<?= escape($applicant['profile_data']['profile_photo']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <?= mb_substr(escape($applicant['user_data']['name'] ?? 'P'), 0, 1); ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h2 class="card-title" style="margin: 0;"><?= escape($applicant['user_data']['name'] ?? 'Provider'); ?></h2>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="badge badge-success">Verified</span>
                                    <div class="flex items-center gap-1 text-sm">
                                        <span class="material-symbols-outlined text-warning" style="font-size: 16px;">star</span>
                                        <span class="font-600"><?= number_format((float)($applicant['profile_data']['rating'] ?? 0), 1); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline btn-sm" data-close-modal="applicant_modal_<?= escape((string)$applicant['_id']); ?>" style="border:none; padding: 0.5rem; border-radius: 50%;">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="modal-body" style="background: #F8FAFC; padding: 2rem;">
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <span class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-2 block">Skills</span>
                                <div class="flex flex-wrap gap-2">
                                    <?php if (!empty($applicant['profile_data']['skills'])): ?>
                                        <?php foreach ($applicant['profile_data']['skills'] as $skill): ?>
                                            <span class="badge badge-secondary"><?= escape($skill); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-sm text-muted italic">No skills listed</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-2 block">Experience</span>
                                <p class="text-sm font-600"><?= escape($applicant['profile_data']['experience'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 text-xs bg-white p-4 rounded-xl border border-slate-200">
                             <div class="flex flex-col">
                                 <span class="text-muted font-600 mb-1">Hourly Rate</span>
                                 <span class="font-700"><?= escape($applicant['profile_data']['hourly_rate'] ?? 'N/A'); ?> BDT</span>
                             </div>
                             <div class="flex flex-col">
                                 <span class="text-muted font-600 mb-1">Location</span>
                                 <span class="font-700"><?= escape($applicant['profile_data']['location'] ?? 'N/A'); ?></span>
                             </div>
                             <div class="flex flex-col">
                                 <span class="text-muted font-600 mb-1">Availability</span>
                                 <span class="font-700"><?= escape($applicant['profile_data']['availability'] ?? 'N/A'); ?></span>
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding: 1.5rem 2.5rem; background: white; border-top: 1px solid var(--border-base);">
                        <button type="button" class="btn btn-outline" data-close-modal="applicant_modal_<?= escape((string)$applicant['_id']); ?>">Close</button>
                        <form action="/jobs/accept" method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                            <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                            <input type="hidden" name="provider_id" value="<?= escape((string)$applicant['provider_id']); ?>">
                            <button type="submit" class="btn btn-success" style="padding-inline: 2rem;">
                                <span class="material-symbols-outlined">check</span> Accept Applicant
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endforeach; ?>

<script>
(() => {
    const openButtons = document.querySelectorAll('[data-open-modal]');
    const closeButtons = document.querySelectorAll('[data-close-modal]');

    openButtons.forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const modal = document.getElementById(btn.dataset.openModal);
            if (modal) modal.classList.add('open');
        }
    });

    closeButtons.forEach(btn => {
        btn.onclick = () => {
            const modal = document.getElementById(btn.dataset.closeModal);
            if (modal) modal.classList.remove('open');
        }
    });

    window.onclick = (event) => {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('open');
        }
    };
})();
</script>
