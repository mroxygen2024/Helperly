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
