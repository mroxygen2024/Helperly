<div class="card">
    <div class="card-header">
        <h2 class="card-title">All Registered Users</h2>
        <div class="text-sm text-muted">Total: <?= count($users); ?> users</div>
    </div>

    <?php if (empty($users)): ?>
        <p class="text-muted text-center py-8">No users found in the database.</p>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="user-avatar"><?= mb_substr(escape($u['name'] ?? '?'), 0, 1); ?></div>
                                    <div class="flex flex-col">
                                        <span class="font-600"><?= escape($u['name'] ?? 'N/A'); ?></span>
                                        <span class="text-sm text-muted"><?= escape($u['email'] ?? 'N/A'); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-secondary"><?= escape($u['role'] ?? 'N/A'); ?></span>
                            </td>
                            <td>
                                <?php if ((bool)($u['is_blocked'] ?? false)): ?>
                                    <span class="badge badge-danger">Blocked</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted text-sm">
                                <?= isset($u['created_at']) ? (is_string($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : ($u['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $u['created_at']->toDateTime()->format('M d, Y') : 'N/A')) : 'N/A'; ?>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <?php if ((string)$u['_id'] !== (string)$currentUser['id']): ?>
                                        <button type="button" class="btn btn-outline btn-sm" data-open-modal="user_modal_<?= escape((string)$u['_id']); ?>" title="View Details">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                        </button>
                                        <form action="/admin/users/toggle-block" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                                            <input type="hidden" name="user_id" value="<?= escape((string)$u['_id']); ?>">
                                            <input type="hidden" name="block" value="<?= (bool)($u['is_blocked'] ?? false) ? '0' : '1'; ?>">
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">
                                                    <?= (bool)($u['is_blocked'] ?? false) ? 'lock_open' : 'block'; ?>
                                                </span>
                                                <?= (bool)($u['is_blocked'] ?? false) ? 'Unblock' : 'Block'; ?>
                                            </button>
                                        </form>
                                        <form action="/admin/users/delete" method="POST" onsubmit="return confirm('Delete this user forever?');">
                                            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                                            <input type="hidden" name="user_id" value="<?= escape((string)$u['_id']); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-sm text-muted">You</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php foreach ($users as $u): ?>
    <div id="user_modal_<?= escape((string)$u['_id']); ?>" class="modal-overlay" data-modal>
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header" style="background: white; border-bottom: 2px solid var(--border-light);">
                <div class="flex items-center gap-4">
                    <div class="user-avatar-rect" style="width: 48px; height: 48px; background: var(--grad-primary);">
                        <?= mb_substr(escape($u['name'] ?? 'U'), 0, 1); ?>
                    </div>
                    <div>
                        <h2 class="card-title" style="margin: 0;">User Details</h2>
                        <p class="text-sm text-muted">ID: <?= escape((string)$u['_id']); ?></p>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-sm" data-close-modal="user_modal_<?= escape((string)$u['_id']); ?>" style="border:none; padding: 0.5rem; border-radius: 50%;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="modal-body" style="background: #F8FAFC; padding: 2rem;">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Name</span>
                        <span class="font-700"><?= escape($u['name'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Email</span>
                        <span class="font-700"><?= escape($u['email'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Role</span>
                        <span class="font-700"><?= escape($u['role'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Status</span>
                        <span class="font-700"><?= (bool)($u['is_blocked'] ?? false) ? 'Blocked' : 'Active'; ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Joined</span>
                        <span class="font-700"><?= isset($u['created_at']) ? (is_string($u['created_at']) ? date('M d, Y h:i A', strtotime($u['created_at'])) : ($u['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $u['created_at']->toDateTime()->format('M d, Y h:i A') : 'N/A')) : 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.5rem 2.5rem; background: white; border-top: 1px solid var(--border-base);">
                <button type="button" class="btn btn-outline w-full" data-close-modal="user_modal_<?= escape((string)$u['_id']); ?>">Close</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
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
