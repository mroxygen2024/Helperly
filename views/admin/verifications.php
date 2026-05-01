<div class="card">
    <div class="card-header">
        <h2 class="card-title">Pending Verifications</h2>
        <p class="text-sm text-muted"><?= count($providers); ?> applications awaiting review</p>
    </div>

    <?php if (empty($providers)): ?>
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-success" style="font-size: 3rem;">verified_user</span>
            <p class="text-muted mt-2">Inbox is clear! No pending verifications.</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Provider</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($providers as $provider): ?>
                        <?php $profile = $provider['profile'] ?? []; ?>
                        <?php $uid = (string) ($profile['user_id'] ?? ''); ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="user-avatar"><?= mb_substr(escape($provider['name'] ?? '?'), 0, 1); ?></div>
                                    <div class="flex flex-col">
                                        <span class="font-600"><?= escape($provider['name'] ?? 'N/A'); ?></span>
                                        <span class="text-sm text-muted"><?= escape($provider['phone'] ?? 'N/A'); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?= escape((string) ($profile['location'] ?? 'Unknown')); ?></td>
                            <td>
                                <span class="badge badge-warning">
                                    <?= escape(ServantProfile::verificationStatusLabel((string) ($profile['verification_status'] ?? 'pending'))); ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn btn-outline btn-sm" data-open-modal="verification_modal_<?= escape($uid); ?>">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                    Review Docs
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php foreach ($providers as $provider): ?>
    <?php $profile = $provider['profile'] ?? []; ?>
    <?php $uid = (string) ($profile['user_id'] ?? ''); ?>
    <div id="verification_modal_<?= escape($uid); ?>" class="modal-overlay" data-modal>
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header" style="background: white; border-bottom: 2px solid var(--border-light);">
                <div class="flex items-center gap-4">
                    <div class="user-avatar-rect" style="width: 48px; height: 48px; background: var(--grad-primary);">
                        <?= mb_substr(escape($provider['name'] ?? 'P'), 0, 1); ?>
                    </div>
                    <div>
                        <h2 class="card-title" style="margin: 0;"><?= escape((string)($provider['name'] ?? 'Provider')); ?></h2>
                        <p class="text-sm text-muted">Awaiting Identity Verification</p>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-sm" data-close-modal="verification_modal_<?= escape($uid); ?>" style="border:none; padding: 0.5rem; border-radius: 50%;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="modal-body" style="background: #F8FAFC; padding: 2rem;">
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-800 uppercase letter-spacing-lg text-muted">ID Front</p>
                        <div class="identity-card-preview" style="aspect-ratio: 3/2; overflow: hidden; border-radius: 12px; border: 2px solid white; box-shadow: var(--shadow-md);">
                            <?php if (!empty($profile['fayda_id_front_url'])): ?>
                                <a href="<?= escape($profile['fayda_id_front_url']); ?>" target="_blank">
                                    <img src="<?= escape($profile['fayda_id_front_url']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </a>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center h-full bg-slate-100 text-muted">
                                    <span class="material-symbols-outlined">image_not_supported</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-800 uppercase letter-spacing-lg text-muted">ID Back</p>
                        <div class="identity-card-preview" style="aspect-ratio: 3/2; overflow: hidden; border-radius: 12px; border: 2px solid white; box-shadow: var(--shadow-md);">
                            <?php if (!empty($profile['fayda_id_back_url'])): ?>
                                <a href="<?= escape($profile['fayda_id_back_url']); ?>" target="_blank">
                                    <img src="<?= escape($profile['fayda_id_back_url']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </a>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center h-full bg-slate-100 text-muted">
                                    <span class="material-symbols-outlined">image_not_supported</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-800 uppercase letter-spacing-lg text-muted">Live Selfie</p>
                        <div class="identity-card-preview" style="aspect-ratio: 3/2; overflow: hidden; border-radius: 12px; border: 2px solid white; box-shadow: var(--shadow-md);">
                            <?php if (!empty($profile['selfie_url'])): ?>
                                <a href="<?= escape($profile['selfie_url']); ?>" target="_blank">
                                    <img src="<?= escape($profile['selfie_url']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </a>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center h-full bg-slate-100 text-muted">
                                    <span class="material-symbols-outlined">no_photography</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white rounded-xl mb-8 border border-slate-200">
                    <h3 class="text-sm font-800 uppercase letter-spacing-lg mb-4 text-primary">Application Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="flex flex-col">
                            <span class="text-xs text-muted font-600">National ID</span>
                            <span class="font-700"><?= escape($profile['national_id'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-muted font-600">Skills</span>
                            <span class="font-700"><?= is_array($profile['skills'] ?? null) ? implode(', ', $profile['skills']) : 'N/A'; ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-muted font-600">Location</span>
                            <span class="font-700"><?= escape($profile['location'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-muted font-600">Rate</span>
                            <span class="font-700"><?= escape($profile['rate'] ?? 'N/A'); ?> ETB</span>
                        </div>
                    </div>
                </div>

                <form id="action_form_<?= escape($uid); ?>" action="/admin/servant-verification" method="POST" class="flex flex-col gap-4">
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                    <input type="hidden" name="servant_user_id" value="<?= escape($uid); ?>">
                    <input type="hidden" id="status_input_<?= escape($uid); ?>" name="verification_status" value="approved">
                    
                    <div class="form-group">
                        <label for="notes_<?= escape($uid); ?>" class="label">Review Notes / Reason for Rejection</label>
                        <textarea id="notes_<?= escape($uid); ?>" name="verification_notes" class="textarea" rows="3" placeholder="Provide specific feedback to the provider..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="padding: 1.5rem 2.5rem; background: white; border-top: 1px solid var(--border-base);">
                <button type="button" class="btn btn-outline" data-close-modal="verification_modal_<?= escape($uid); ?>" style="border:none;">Cancel</button>
                <div class="flex gap-3">
                    <button type="button" class="btn btn-danger" onclick="submitVerification('<?= $uid; ?>', 'rejected')" style="padding-inline: 2rem;">
                        <span class="material-symbols-outlined">block</span> Reject
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitVerification('<?= $uid; ?>', 'approved')" style="padding-inline: 2rem;">
                        <span class="material-symbols-outlined">verified</span> Approve Provider
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

