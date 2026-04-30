<div class="flex items-center gap-4 mb-8">
    <a href="/admin/users" class="btn btn-outline btn-sm">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <div>
        <h1 class="card-title" style="font-size: 2rem;">User Details</h1>
        <p class="text-sm text-muted">ID: <?= escape((string)$user['_id']); ?></p>
    </div>
</div>

<div class="grid grid-cols-3 gap-8">
    <div class="card md:col-span-1">
        <div class="flex flex-col items-center text-center">
            <div class="user-avatar-rect" style="width: 100px; height: 100px; font-size: 2.5rem; margin-bottom: 1.5rem; background: var(--grad-primary);">
                <?= mb_substr(escape($user['name'] ?? 'U'), 0, 1); ?>
            </div>
            <h2 class="font-800 text-xl mb-1"><?= escape($user['name'] ?? 'N/A'); ?></h2>
            <p class="text-muted mb-4"><?= escape($user['email'] ?? 'N/A'); ?></p>
            
            <div class="flex flex-wrap justify-center gap-2 mb-6">
                <span class="badge badge-secondary"><?= escape(ucfirst($user['role'] ?? 'user')); ?></span>
                <?php if ((bool)($user['is_blocked'] ?? false)): ?>
                    <span class="badge badge-danger">Blocked</span>
                <?php else: ?>
                    <span class="badge badge-success">Active</span>
                <?php endif; ?>
            </div>

            <div class="w-full pt-6 border-t">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-muted">Joined:</span>
                    <span class="text-sm font-600"><?= isset($user['created_at']) ? (is_string($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : ($user['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $user['created_at']->toDateTime()->format('M d, Y') : 'N/A')) : 'N/A'; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-muted">Phone:</span>
                    <span class="text-sm font-600"><?= escape($user['phone'] ?? 'N/A'); ?></span>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-3">
             <form action="/admin/users/toggle-block" method="POST" class="w-full">
                <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                <input type="hidden" name="user_id" value="<?= escape((string)$user['_id']); ?>">
                <input type="hidden" name="block" value="<?= (bool)($user['is_blocked'] ?? false) ? '0' : '1'; ?>">
                <button type="submit" class="btn <?= (bool)($user['is_blocked'] ?? false) ? 'btn-outline' : 'btn-danger'; ?> w-full">
                    <span class="material-symbols-outlined"><?= (bool)($user['is_blocked'] ?? false) ? 'lock_open' : 'block'; ?></span>
                    <?= (bool)($user['is_blocked'] ?? false) ? 'Unblock User' : 'Block User'; ?>
                </button>
            </form>
        </div>
    </div>

    <div class="card md:col-span-2">
        <div class="card-header">
            <h3 class="card-title">Profile Information</h3>
        </div>
        
        <?php if (!empty($user['profile'])): ?>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-1">Status</p>
                    <p class="font-600"><?= escape($user['profile']['status'] ?? 'N/A'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-1">Location</p>
                    <p class="font-600"><?= escape($user['profile']['location'] ?? 'N/A'); ?></p>
                </div>
                
                <?php if (normalizeRole((string)$user['role']) === 'service_provider'): ?>
                    <div>
                        <p class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-1">Experience</p>
                        <p class="font-600"><?= escape($user['profile']['experience'] ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-1">Hourly Rate</p>
                        <p class="font-600 text-primary"><?= number_format((float)($user['profile']['rate'] ?? 0), 2); ?> BDT</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-2">Skills</p>
                        <div class="flex flex-wrap gap-2">
                            <?php if (!empty($user['profile']['skills'])): ?>
                                <?php foreach ($user['profile']['skills'] as $skill): ?>
                                    <span class="badge badge-secondary"><?= escape($skill); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-sm text-muted italic">No skills listed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (normalizeRole((string)$user['role']) === 'service_provider'): ?>
                <div class="mt-8 pt-8 border-t">
                    <h4 class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-4">Verification Documents</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <?php if (!empty($user['profile']['selfie_url'])): ?>
                            <div class="flex flex-col gap-2">
                                <span class="text-xs font-600 text-muted">Selfie</span>
                                <img src="<?= escape($user['profile']['selfie_url']); ?>" class="rounded-lg border shadow-sm w-full h-32 object-cover">
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($user['profile']['fayda_id_front_url'])): ?>
                            <div class="flex flex-col gap-2">
                                <span class="text-xs font-600 text-muted">ID Front</span>
                                <img src="<?= escape($user['profile']['fayda_id_front_url']); ?>" class="rounded-lg border shadow-sm w-full h-32 object-cover">
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($user['profile']['fayda_id_back_url'])): ?>
                            <div class="flex flex-col gap-2">
                                <span class="text-xs font-600 text-muted">ID Back</span>
                                <img src="<?= escape($user['profile']['fayda_id_back_url']); ?>" class="rounded-lg border shadow-sm w-full h-32 object-cover">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-12 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                <span class="material-symbols-outlined text-muted" style="font-size: 3rem; opacity: 0.5;">account_circle</span>
                <p class="text-muted mt-2">No detailed profile has been created for this user yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
