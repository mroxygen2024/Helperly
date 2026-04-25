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
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="card-title">Verification: <?= escape((string)($provider['name'] ?? 'Provider')); ?></h2>
                <button type="button" class="btn btn-outline btn-sm" data-close-modal="verification_modal_<?= escape($uid); ?>" style="border:none; padding: 0.25rem;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <p class="label mb-2">ID Front</p>
                        <?php if (!empty($profile['fayda_id_front_url'])): ?>
                            <a href="<?= escape($profile['fayda_id_front_url']); ?>" target="_blank">
                                <img src="<?= escape($profile['fayda_id_front_url']); ?>" class="w-full rounded-lg border">
                            </a>
                        <?php else: ?>
                            <div class="bg-gray-100 rounded-lg py-12 text-center text-muted">No Image</div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="label mb-2">ID Back</p>
                        <?php if (!empty($profile['fayda_id_back_url'])): ?>
                            <a href="<?= escape($profile['fayda_id_back_url']); ?>" target="_blank">
                                <img src="<?= escape($profile['fayda_id_back_url']); ?>" class="w-full rounded-lg border">
                            </a>
                        <?php else: ?>
                            <div class="bg-gray-100 rounded-lg py-12 text-center text-muted">No Image</div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="label mb-2">Selfie</p>
                        <?php if (!empty($profile['selfie_url'])): ?>
                            <a href="<?= escape($profile['selfie_url']); ?>" target="_blank">
                                <img src="<?= escape($profile['selfie_url']); ?>" class="w-full rounded-lg border">
                            </a>
                        <?php else: ?>
                            <div class="bg-gray-100 rounded-lg py-12 text-center text-muted">No Image</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="p-4 bg-primary-soft rounded-lg mb-6">
                    <h3 class="text-sm font-600 mb-1">Provider Summary</h3>
                    <div class="grid grid-cols-2 text-sm">
                        <p><span class="text-muted">National ID:</span> <?= escape($profile['national_id'] ?? 'N/A'); ?></p>
                        <p><span class="text-muted">Skills:</span> <?= is_array($profile['skills'] ?? null) ? implode(', ', $profile['skills']) : 'N/A'; ?></p>
                    </div>
                </div>

                <form id="action_form_<?= escape($uid); ?>" action="/admin/servant-verification" method="POST" class="flex flex-col gap-4">
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                    <input type="hidden" name="servant_user_id" value="<?= escape($uid); ?>">
                    <input type="hidden" id="status_input_<?= escape($uid); ?>" name="verification_status" value="approved">
                    
                    <div class="form-group">
                        <label class="label">Rejection Reason (Required if rejecting)</label>
                        <textarea name="verification_notes" class="textarea" rows="2" placeholder="e.g. ID image is blurry..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-close-modal="verification_modal_<?= escape($uid); ?>">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitVerification('<?= $uid; ?>', 'rejected')">Reject</button>
                <button type="button" class="btn btn-primary" onclick="submitVerification('<?= $uid; ?>', 'approved')">Approve Provider</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
function submitVerification(uid, status) {
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
