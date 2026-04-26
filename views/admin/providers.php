<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="card-title" style="font-size: 2rem;">Provider Management</h1>
        <p class="text-sm text-muted">Manage all service providers and their verification status</p>
    </div>
    <div class="badge badge-info"><?= count($providers); ?> Providers registered</div>
</div>

<div class="card p-0 overflow-hidden shadow-xl border-none">
    <div class="card-header bg-white p-6 border-b">
        <h2 class="font-700">All Service Providers</h2>
    </div>
    <div class="p-0 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Provider</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Location</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Hourly Rate</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Verification</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Rating</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($providers as $p): ?>
                    <tr class="hover:bg-slate-50 transition-colors border-b last:border-0">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($p['profile']['profile_photo'])): ?>
                                    <img src="<?= escape($p['profile']['profile_photo']); ?>" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="user-avatar" style="width: 40px; height: 40px; border-radius: 10px; font-size: 14px;"><?= mb_substr(escape($p['name'] ?? 'P'), 0, 1); ?></div>
                                <?php endif; ?>
                                <div class="flex flex-col">
                                    <span class="font-600"><?= escape($p['name'] ?? 'Unnamed'); ?></span>
                                    <span class="text-xs text-muted"><?= escape($p['email'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="text-sm font-500"><?= escape($p['profile']['location'] ?? 'Not set'); ?></span>
                        </td>
                        <td class="p-4">
                            <span class="text-sm font-700 text-slate-800"><?= escape($p['profile']['hourly_rate'] ?? '0'); ?> BDT/hr</span>
                        </td>
                        <td class="p-4">
                            <?php $vStatus = (string)($p['profile']['verification_status'] ?? 'pending'); ?>
                            <span class="badge badge-<?= $vStatus === 'approved' ? 'success' : ($vStatus === 'rejected' ? 'danger' : 'warning'); ?>">
                                <?= escape(ucfirst($vStatus)); ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-warning" style="font-size: 16px;">star</span>
                                <span class="text-sm font-600"><?= number_format((float)($p['profile']['rating'] ?? 0), 1); ?></span>
                            </div>
                        </td>
                        <td class="p-4 text-right">
                             <button type="button" class="btn btn-outline btn-sm" data-open-modal="provider_modal_<?= escape((string)$p['_id']); ?>">
                                <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Provider Details Modals -->
<?php foreach ($providers as $p): ?>
    <div id="provider_modal_<?= escape((string)$p['_id']); ?>" class="modal-overlay" data-modal>
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div class="flex items-center gap-4">
                     <div class="user-avatar-rect" style="width: 64px; height: 64px; background: var(--grad-primary);">
                        <?php if (!empty($p['profile']['profile_photo'])): ?>
                            <img src="<?= escape($p['profile']['profile_photo']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?= mb_substr(escape($p['name'] ?? 'P'), 0, 1); ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 class="card-title" style="margin: 0;"><?= escape($p['name'] ?? 'Provider'); ?></h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="badge badge-<?= (string)($p['profile']['verification_status'] ?? '') === 'approved' ? 'success' : 'warning'; ?>">
                                <?= ServantProfile::verificationStatusLabel($p['profile']['verification_status'] ?? ''); ?>
                            </span>
                            <span class="text-xs text-muted">ID: <?= escape((string)$p['_id']); ?></span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-sm" data-close-modal="provider_modal_<?= escape((string)$p['_id']); ?>" style="border:none; padding: 0.5rem; border-radius: 50%;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="modal-body" style="background: #F8FAFC; padding: 2rem;">
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <span class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-2 block">Skills & Expertise</span>
                        <div class="flex flex-wrap gap-1">
                            <?php if (!empty($p['profile']['skills'])): ?>
                                <?php foreach ($p['profile']['skills'] as $skill): ?>
                                    <span class="badge badge-secondary" style="font-size: 10px;"><?= escape($skill); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-sm text-muted italic">None listed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-2 block">Experience</span>
                        <p class="text-sm font-600"><?= escape($p['profile']['experience'] ?? 'N/A'); ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs bg-white p-4 rounded-xl border border-slate-200 mb-8">
                     <div class="flex flex-col">
                         <span class="text-muted font-600 mb-1">Hourly Rate</span>
                         <span class="font-700 text-sm"><?= escape($p['profile']['hourly_rate'] ?? 'N/A'); ?> BDT</span>
                     </div>
                     <div class="flex flex-col">
                         <span class="text-muted font-600 mb-1">Location</span>
                         <span class="font-700 text-sm"><?= escape($p['profile']['location'] ?? 'Not set'); ?></span>
                     </div>
                </div>

                <div class="space-y-4">
                    <p class="text-xs font-800 uppercase letter-spacing-lg text-muted">Verification Documents</p>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="flex flex-col gap-1">
                            <span class="text-tiny text-muted">Selfie</span>
                            <?php if (!empty($p['profile']['selfie_url'])): ?>
                                <img src="<?= escape($p['profile']['selfie_url']); ?>" class="verification-thumb" style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-base);">
                            <?php else: ?>
                                <div style="height: 100px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><span class="material-symbols-outlined text-muted">no_photography</span></div>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-tiny text-muted">Fayda ID Front</span>
                            <?php if (!empty($p['profile']['fayda_id_front_url'])): ?>
                                <img src="<?= escape($p['profile']['fayda_id_front_url']); ?>" class="verification-thumb" style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-base);">
                            <?php else: ?>
                                <div style="height: 100px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><span class="material-symbols-outlined text-muted">credit_card</span></div>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-tiny text-muted">Fayda ID Back</span>
                            <?php if (!empty($p['profile']['fayda_id_back_url'])): ?>
                                <img src="<?= escape($p['profile']['fayda_id_back_url']); ?>" class="verification-thumb" style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-base);">
                            <?php else: ?>
                                <div style="height: 100px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><span class="material-symbols-outlined text-muted">credit_card</span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline w-full" data-close-modal="provider_modal_<?= escape((string)$p['_id']); ?>">Close</button>
            </div>
        </div>
    </div>
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
