<div class="animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-wrap justify-between items-center gap-6 mb-8">
        <div>
            <h1 class="page-title text-2xl font-black text-main mb-1">Pending Verifications</h1>
            <p class="text-neutral-400 font-bold"><?= count($providers); ?> applications awaiting review</p>
        </div>
        <div class="flex items-center gap-2 bg-warning-50 text-warning px-4 py-2 rounded-2xl border border-warning-100">
            <span class="material-symbols-outlined text-lg">priority_high</span>
            <span class="text-xs font-black uppercase tracking-wider">Review Required</span>
        </div>
    </div>

    <div class="dashboard-card p-0 overflow-hidden">
        <?php if (empty($providers)): ?>
            <div class="flex flex-col items-center justify-center py-24 text-center px-6">
                <div class="empty-state-icon bg-success-50 text-success">
                    <span class="material-symbols-outlined" style="font-size: 48px;">verified_user</span>
                </div>
                <h3 class="text-lg font-black text-main mb-2">Inbox is clear!</h3>
                <p class="text-neutral-400 font-bold max-w-sm">No pending provider verifications at the moment. Good job!</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table-modern w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Provider Information</th>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Location</th>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Status</th>
                            <th class="px-6 py-4 text-right text-[10px] uppercase tracking-widest font-black text-neutral-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        <?php foreach ($providers as $provider): ?>
                            <?php $profile = $provider['profile'] ?? []; ?>
                            <?php $uid = (string) ($profile['user_id'] ?? ''); ?>
                            <tr class="hover:bg-neutral-50/50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="user-avatar w-12 h-12 rounded-xl text-lg">
                                            <?= mb_substr(escape($provider['name'] ?? '?'), 0, 1); ?>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-extrabold text-main leading-tight"><?= escape($provider['name'] ?? 'N/A'); ?></span>
                                            <span class="text-xs font-bold text-neutral-400 mt-0.5"><?= escape($provider['phone'] ?? 'N/A'); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2 text-neutral-500">
                                        <span class="material-symbols-outlined text-sm">location_on</span>
                                        <span class="text-sm font-bold"><?= escape((string) ($profile['location'] ?? 'Unknown')); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="badge badge-warning px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                        <?= escape(ServantProfile::verificationStatusLabel((string) ($profile['verification_status'] ?? 'pending'))); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <button type="button" class="btn btn-primary btn-sm px-6 shadow-premium" data-open-modal="verification_modal_<?= escape($uid); ?>">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                        Review
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php foreach ($providers as $provider): ?>
    <?php $profile = $provider['profile'] ?? []; ?>
    <?php $uid = (string) ($profile['user_id'] ?? ''); ?>
    <div id="verification_modal_<?= escape($uid); ?>" class="modal-overlay" data-modal>
        <div class="modal-content" style="max-width: 900px;">
            <div class="modal-header">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined">how_to_reg</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-main leading-tight"><?= escape((string)($provider['name'] ?? 'Provider Review')); ?></h2>
                        <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest mt-0.5">Identity Verification Request</p>
                    </div>
                </div>
                <button type="button" class="btn btn-ghost p-2 rounded-full hover:bg-neutral-50" data-close-modal="verification_modal_<?= escape($uid); ?>">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="modal-body p-8 space-y-10">
                <!-- Document Grid -->
                <div>
                    <h3 class="text-xs font-black text-neutral-400 uppercase tracking-widest mb-4">Verification Documents</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <p class="text-[10px] font-black text-neutral-500 uppercase tracking-tighter">Fayda ID (Front)</p>
                            <div class="group relative aspect-[3/2] bg-neutral-100 rounded-2xl overflow-hidden border-2 border-white shadow-sm hover:shadow-lg transition-all">
                                <?php if (!empty($profile['fayda_id_front_url'])): ?>
                                    <img src="<?= escape($profile['fayda_id_front_url']); ?>" class="w-full h-full object-cover">
                                    <a href="<?= escape($profile['fayda_id_front_url']); ?>" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                        <span class="material-symbols-outlined text-white">open_in_new</span>
                                    </a>
                                <?php else: ?>
                                    <div class="flex flex-col items-center justify-center h-full text-neutral-300">
                                        <span class="material-symbols-outlined" style="font-size: 32px;">image_not_supported</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <p class="text-[10px] font-black text-neutral-500 uppercase tracking-tighter">Fayda ID (Back)</p>
                            <div class="group relative aspect-[3/2] bg-neutral-100 rounded-2xl overflow-hidden border-2 border-white shadow-sm hover:shadow-lg transition-all">
                                <?php if (!empty($profile['fayda_id_back_url'])): ?>
                                    <img src="<?= escape($profile['fayda_id_back_url']); ?>" class="w-full h-full object-cover">
                                    <a href="<?= escape($profile['fayda_id_back_url']); ?>" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                        <span class="material-symbols-outlined text-white">open_in_new</span>
                                    </a>
                                <?php else: ?>
                                    <div class="flex flex-col items-center justify-center h-full text-neutral-300">
                                        <span class="material-symbols-outlined" style="font-size: 32px;">image_not_supported</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-[10px] font-black text-neutral-500 uppercase tracking-tighter">Live Selfie Check</p>
                            <div class="group relative aspect-[3/2] bg-neutral-100 rounded-2xl overflow-hidden border-2 border-white shadow-sm hover:shadow-lg transition-all">
                                <?php if (!empty($profile['selfie_url'])): ?>
                                    <img src="<?= escape($profile['selfie_url']); ?>" class="w-full h-full object-cover">
                                    <a href="<?= escape($profile['selfie_url']); ?>" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                        <span class="material-symbols-outlined text-white">open_in_new</span>
                                    </a>
                                <?php else: ?>
                                    <div class="flex flex-col items-center justify-center h-full text-neutral-300">
                                        <span class="material-symbols-outlined" style="font-size: 32px;">no_photography</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Details -->
                <div class="bg-neutral-50 p-6 rounded-2xl border border-neutral-100">
                    <h3 class="text-xs font-black text-neutral-400 uppercase tracking-widest mb-4">Professional Overview</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-[10px] font-black text-neutral-400 uppercase tracking-wider mb-1">National ID</p>
                            <p class="text-sm font-extrabold text-main"><?= escape($profile['national_id'] ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-neutral-400 uppercase tracking-wider mb-1">Experience</p>
                            <p class="text-sm font-extrabold text-main"><?= escape($profile['experience'] ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-neutral-400 uppercase tracking-wider mb-1">Expectation</p>
                            <p class="text-sm font-extrabold text-main"><?= escape($profile['rate'] ?? 'N/A'); ?> ETB/hr</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-neutral-400 uppercase tracking-wider mb-1">Location</p>
                            <p class="text-sm font-extrabold text-main"><?= escape($profile['location'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Action Form -->
                <form id="action_form_<?= escape($uid); ?>" action="/admin/servant-verification" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                    <input type="hidden" name="servant_user_id" value="<?= escape($uid); ?>">
                    <input type="hidden" id="status_input_<?= escape($uid); ?>" name="verification_status" value="approved">
                    
                    <div class="input-group m-0">
                        <label for="notes_<?= escape($uid); ?>" class="label text-xs font-black uppercase tracking-widest text-neutral-400">Review Notes / Feedback</label>
                        <textarea id="notes_<?= escape($uid); ?>" name="verification_notes" class="input py-4 min-h-[100px] resize-none" placeholder="Provide specific feedback or reasons if rejecting..."></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer flex justify-between gap-4 bg-neutral-50">
                <button type="button" class="btn btn-ghost px-8" data-close-modal="verification_modal_<?= escape($uid); ?>">Cancel</button>
                <div class="flex gap-3">
                    <button type="button" class="btn btn-danger btn-outline px-8 group" onclick="submitVerification('<?= $uid; ?>', 'rejected')">
                        <span class="material-symbols-outlined text-sm group-hover:scale-110 transition-transform">block</span>
                        Reject
                    </button>
                    <button type="button" class="btn btn-primary px-10 shadow-premium group" onclick="submitVerification('<?= $uid; ?>', 'approved')">
                        <span class="material-symbols-outlined text-sm group-hover:rotate-12 transition-transform">verified</span>
                        Approve Provider
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
function submitVerification(uid, status) {
    const notesInput = document.getElementById('notes_' + uid);
    if (status === 'rejected' && notesInput.value.trim() === '') {
        alert('Please provide a reason for rejection.');
        notesInput.focus();
        return;
    }
    document.getElementById('status_input_' + uid).value = status;
    document.getElementById('action_form_' + uid).submit();
}

(() => {
    const openButtons = document.querySelectorAll('[data-open-modal]');
    const closeButtons = document.querySelectorAll('[data-close-modal]');

    openButtons.forEach(btn => {
        btn.onclick = () => {
            const modal = document.getElementById(btn.dataset.openModal);
            if (modal) modal.classList.add('active');
        }
    });

    closeButtons.forEach(btn => {
        btn.onclick = () => {
            const modal = document.getElementById(btn.dataset.closeModal);
            if (modal) modal.classList.remove('active');
        }
    });

    window.onclick = (event) => {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('active');
        }
    };
})();
</script>

