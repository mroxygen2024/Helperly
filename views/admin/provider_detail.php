<div class="flex items-center gap-4 mb-8">
    <a href="/admin/providers" class="btn btn-outline btn-sm">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <div>
        <h1 class="card-title" style="font-size: 2rem;">Provider Details</h1>
        <p class="text-sm text-muted">ID: <?= escape((string)$provider['_id']); ?></p>
    </div>
</div>

<div class="grid grid-cols-3 gap-8">
    <!-- Left Column: Avatar & Quick Info -->
    <div class="card" style="grid-column: span 1;">
        <div class="flex flex-col items-center text-center">
            <?php if (!empty($provider['profile']['profile_photo'])): ?>
                <img src="<?= escape($provider['profile']['profile_photo']); ?>" style="width: 120px; height: 120px; border-radius: 16px; object-fit: cover; margin-bottom: 1.5rem; border: 3px solid var(--border-base); box-shadow: var(--shadow-md);">
            <?php else: ?>
                <div class="user-avatar-rect" style="width: 120px; height: 120px; font-size: 3rem; margin-bottom: 1.5rem; background: var(--grad-primary);">
                    <?= mb_substr(escape($provider['name'] ?? 'P'), 0, 1); ?>
                </div>
            <?php endif; ?>
            <h2 class="font-800 text-xl mb-1"><?= escape($provider['name'] ?? 'N/A'); ?></h2>
            <p class="text-muted text-sm mb-4"><?= escape($provider['email'] ?? 'N/A'); ?></p>
            
            <div class="flex flex-wrap justify-center gap-2 mb-6">
                <?php $vStatus = (string)($provider['profile']['verification_status'] ?? 'pending'); ?>
                <span class="badge badge-<?= $vStatus === 'approved' ? 'success' : ($vStatus === 'rejected' ? 'danger' : 'warning'); ?>">
                    <?= escape(ucfirst($vStatus)); ?>
                </span>
                <?php if ((bool)($provider['is_blocked'] ?? false)): ?>
                    <span class="badge badge-danger">Blocked</span>
                <?php endif; ?>
            </div>

            <div class="w-full text-left">
                <div class="flex justify-between py-3 border-t">
                    <span class="text-sm text-muted">Phone</span>
                    <span class="text-sm font-600"><?= escape($provider['phone'] ?? 'N/A'); ?></span>
                </div>
                <div class="flex justify-between py-3 border-t">
                    <span class="text-sm text-muted">Location</span>
                    <span class="text-sm font-600"><?= escape($provider['profile']['location'] ?? 'N/A'); ?></span>
                </div>
                <div class="flex justify-between py-3 border-t">
                    <span class="text-sm text-muted">Hourly Rate</span>
                    <span class="text-sm font-700 text-primary"><?= escape($provider['profile']['rate'] ?? '0'); ?> ETB</span>
                </div>
                <div class="flex justify-between py-3 border-t">
                    <span class="text-sm text-muted">Rating</span>
                    <span class="text-sm font-700">
                        <span class="material-symbols-outlined text-warning" style="font-size: 14px; vertical-align: -2px;">star</span>
                        <?= number_format((float)($provider['profile']['rating'] ?? 0), 1); ?>
                    </span>
                </div>
                <div class="flex justify-between py-3 border-t">
                    <span class="text-sm text-muted">Joined</span>
                    <span class="text-sm font-600"><?= isset($provider['created_at']) ? (is_string($provider['created_at']) ? date('M d, Y', strtotime($provider['created_at'])) : ($provider['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $provider['created_at']->toDateTime()->format('M d, Y') : 'N/A')) : 'N/A'; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Full Profile -->
    <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 2rem;">
        <!-- Skills & Experience -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Skills & Experience</h3>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-2">Experience Level</p>
                    <p class="font-600"><?= escape($provider['profile']['experience'] ?? 'N/A'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-2">Availability</p>
                    <p class="font-600"><?= escape($provider['profile']['availability'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-3">Skills</p>
                    <div class="flex flex-wrap gap-2">
                        <?php if (!empty($provider['profile']['skills'])): ?>
                            <?php foreach ($provider['profile']['skills'] as $skill): ?>
                                <span class="badge badge-info" style="font-size: 12px;"><?= escape($skill); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-sm text-muted italic">No skills listed</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verification Documents -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Verification Documents</h3>
                <span class="badge badge-<?= $vStatus === 'approved' ? 'success' : ($vStatus === 'rejected' ? 'danger' : 'warning'); ?>">
                    <?= escape(ucfirst($vStatus)); ?>
                </span>
            </div>
            
            <div class="grid grid-cols-3 gap-6">
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-700 text-muted uppercase">Selfie</span>
                    <?php if (!empty($provider['profile']['selfie_url'])): ?>
                        <img src="<?= escape($provider['profile']['selfie_url']); ?>" style="width: 100%; height: 160px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border-base); box-shadow: var(--shadow-sm);">
                    <?php else: ?>
                        <div style="height: 160px; background: var(--border-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 2px dashed var(--border-base);">
                            <span class="material-symbols-outlined text-muted" style="font-size: 2rem;">no_photography</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-700 text-muted uppercase">Fayda ID Front</span>
                    <?php if (!empty($provider['profile']['fayda_id_front_url'])): ?>
                        <img src="<?= escape($provider['profile']['fayda_id_front_url']); ?>" style="width: 100%; height: 160px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border-base); box-shadow: var(--shadow-sm);">
                    <?php else: ?>
                        <div style="height: 160px; background: var(--border-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 2px dashed var(--border-base);">
                            <span class="material-symbols-outlined text-muted" style="font-size: 2rem;">credit_card</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-700 text-muted uppercase">Fayda ID Back</span>
                    <?php if (!empty($provider['profile']['fayda_id_back_url'])): ?>
                        <img src="<?= escape($provider['profile']['fayda_id_back_url']); ?>" style="width: 100%; height: 160px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border-base); box-shadow: var(--shadow-sm);">
                    <?php else: ?>
                        <div style="height: 160px; background: var(--border-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 2px dashed var(--border-base);">
                            <span class="material-symbols-outlined text-muted" style="font-size: 2rem;">credit_card</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($provider['profile']['verification_notes'])): ?>
                <div class="mt-6 p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="text-xs font-800 text-muted uppercase letter-spacing-lg mb-1">Admin Notes</p>
                    <p class="text-sm"><?= escape($provider['profile']['verification_notes']); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Admin Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Admin Actions</h3>
            </div>
            <div class="flex gap-4">
                <form action="/admin/users/toggle-block" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                    <input type="hidden" name="user_id" value="<?= escape((string)$provider['_id']); ?>">
                    <input type="hidden" name="block" value="<?= (bool)($provider['is_blocked'] ?? false) ? '0' : '1'; ?>">
                    <button type="submit" class="btn <?= (bool)($provider['is_blocked'] ?? false) ? 'btn-outline' : 'btn-danger'; ?>">
                        <span class="material-symbols-outlined"><?= (bool)($provider['is_blocked'] ?? false) ? 'lock_open' : 'block'; ?></span>
                        <?= (bool)($provider['is_blocked'] ?? false) ? 'Unblock Provider' : 'Block Provider'; ?>
                    </button>
                </form>
                <form action="/admin/users/delete" method="POST" onsubmit="return confirm('Permanently delete this provider? This cannot be undone.');">
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                    <input type="hidden" name="user_id" value="<?= escape((string)$provider['_id']); ?>">
                    <button type="submit" class="btn btn-danger">
                        <span class="material-symbols-outlined">delete_forever</span>
                        Delete Provider
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
