<section class="page-header">
    <div>
        <h1>Provider Verifications</h1>
        <p class="muted">Review pending providers and approve or reject with notes.</p>
    </div>
</section>

<section class="card stack">
    <?php if (empty($providers)): ?>
        <p class="muted">No pending providers found.</p>
    <?php else: ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;border-bottom:1px solid #e5e7eb;padding:0.5rem;">Provider</th>
                    <th style="text-align:left;border-bottom:1px solid #e5e7eb;padding:0.5rem;">Phone</th>
                    <th style="text-align:left;border-bottom:1px solid #e5e7eb;padding:0.5rem;">Location</th>
                    <th style="text-align:left;border-bottom:1px solid #e5e7eb;padding:0.5rem;">Status</th>
                    <th style="text-align:left;border-bottom:1px solid #e5e7eb;padding:0.5rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($providers as $provider): ?>
                    <?php $profile = $provider['profile'] ?? []; ?>
                    <?php $uid = (string) ($profile['user_id'] ?? ''); ?>
                    <tr>
                        <td style="padding:0.5rem;border-bottom:1px solid #f3f4f6;"><?= escape((string) ($provider['name'] ?? 'Unnamed')); ?></td>
                        <td style="padding:0.5rem;border-bottom:1px solid #f3f4f6;"><?= escape((string) ($provider['phone'] ?? 'Not provided')); ?></td>
                        <td style="padding:0.5rem;border-bottom:1px solid #f3f4f6;"><?= escape((string) ($profile['location'] ?? 'Unknown')); ?></td>
                        <td style="padding:0.5rem;border-bottom:1px solid #f3f4f6;"><?= escape(ServantProfile::verificationStatusLabel((string) ($profile['verification_status'] ?? 'pending'))); ?></td>
                        <td style="padding:0.5rem;border-bottom:1px solid #f3f4f6;">
                            <button type="button" class="btn btn-secondary" data-open-modal="verification_modal_<?= escape($uid); ?>">Review</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php foreach ($providers as $provider): ?>
    <?php $profile = $provider['profile'] ?? []; ?>
    <?php $uid = (string) ($profile['user_id'] ?? ''); ?>
    <div id="verification_modal_<?= escape($uid); ?>" data-modal hidden style="position:fixed;inset:0;background:rgba(17,24,39,0.55);display:flex;align-items:center;justify-content:center;padding:1rem;z-index:999;">
        <div class="card" style="max-width:780px;width:100%;max-height:90vh;overflow:auto;">
            <h2><?= escape((string) ($provider['name'] ?? 'Provider')); ?></h2>
            <p class="muted">Review uploaded verification images before taking action.</p>

            <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0.75rem;">
                <div>
                    <p><strong>Fayda ID Front</strong></p>
                    <?php if (!empty($profile['fayda_id_front_url'])): ?>
                        <a href="<?= escape((string) $profile['fayda_id_front_url']); ?>" target="_blank" rel="noopener noreferrer">
                            <img src="<?= escape((string) $profile['fayda_id_front_url']); ?>" alt="Fayda ID front" style="width:100%;border-radius:8px;border:1px solid #e5e7eb;">
                        </a>
                    <?php else: ?>
                        <p class="muted">Not uploaded.</p>
                    <?php endif; ?>
                </div>
                <div>
                    <p><strong>Fayda ID Back</strong></p>
                    <?php if (!empty($profile['fayda_id_back_url'])): ?>
                        <a href="<?= escape((string) $profile['fayda_id_back_url']); ?>" target="_blank" rel="noopener noreferrer">
                            <img src="<?= escape((string) $profile['fayda_id_back_url']); ?>" alt="Fayda ID back" style="width:100%;border-radius:8px;border:1px solid #e5e7eb;">
                        </a>
                    <?php else: ?>
                        <p class="muted">Not uploaded.</p>
                    <?php endif; ?>
                </div>
                <div>
                    <p><strong>Selfie</strong></p>
                    <?php if (!empty($profile['selfie_url'])): ?>
                        <a href="<?= escape((string) $profile['selfie_url']); ?>" target="_blank" rel="noopener noreferrer">
                            <img src="<?= escape((string) $profile['selfie_url']); ?>" alt="Selfie" style="width:100%;border-radius:8px;border:1px solid #e5e7eb;">
                        </a>
                    <?php else: ?>
                        <p class="muted">Not uploaded.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-top:1rem;">
                <form action="/admin/servant-verification" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                    <input type="hidden" name="servant_user_id" value="<?= escape($uid); ?>">
                    <input type="hidden" name="verification_status" value="approved">
                    <button type="submit" class="btn">Approve</button>
                </form>

                <form action="/admin/servant-verification" method="POST" class="form-grid" style="min-width:320px;flex:1;">
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                    <input type="hidden" name="servant_user_id" value="<?= escape($uid); ?>">
                    <input type="hidden" name="verification_status" value="rejected">
                    <label for="verification_notes_<?= escape($uid); ?>">Rejection reason</label>
                    <textarea id="verification_notes_<?= escape($uid); ?>" name="verification_notes" rows="3" placeholder="Explain why this was rejected" required><?= escape((string) ($profile['verification_notes'] ?? '')); ?></textarea>
                    <button type="submit" class="btn btn-secondary">Reject</button>
                </form>
            </div>

            <div style="margin-top:1rem;">
                <button type="button" class="btn btn-secondary" data-close-modal="verification_modal_<?= escape($uid); ?>">Close</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
(() => {
    const modals = document.querySelectorAll('[data-modal]');
    const openButtons = document.querySelectorAll('[data-open-modal]');
    const closeButtons = document.querySelectorAll('[data-close-modal]');

    const openModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) {
            return;
        }
        modal.hidden = false;
    };

    const closeModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) {
            return;
        }
        modal.hidden = true;
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => openModal(button.getAttribute('data-open-modal')));
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => closeModal(button.getAttribute('data-close-modal')));
    });

    modals.forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.hidden = true;
            }
        });
    });
})();
</script>
