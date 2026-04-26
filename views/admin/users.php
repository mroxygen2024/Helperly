<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="card-title" style="font-size: 2rem;">User Management</h1>
        <p class="text-sm text-muted">Manage all registered users across the platform</p>
    </div>
    <div class="badge badge-info"><?= count($users); ?> Total Users</div>
</div>

<div class="card p-0 overflow-hidden">
    <?php if (empty($users)): ?>
        <p class="text-muted text-center py-8">No users found in the database.</p>
    <?php else: ?>
        <div class="table-container" style="overflow-x: auto;">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">User</th>
                        <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Phone</th>
                        <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Role</th>
                        <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Status</th>
                        <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Created</th>
                        <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-slate-50 transition-colors border-b last:border-0">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="user-avatar" style="width: 36px; height: 36px; border-radius: 10px; font-size: 13px;"><?= mb_substr(escape($u['name'] ?? '?'), 0, 1); ?></div>
                                    <div class="flex flex-col">
                                        <span class="font-600"><?= escape($u['name'] ?? 'N/A'); ?></span>
                                        <span class="text-xs text-muted"><?= escape($u['email'] ?? 'N/A'); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-sm"><?= escape($u['phone'] ?? 'N/A'); ?></td>
                            <td class="p-4">
                                <span class="badge badge-secondary"><?= escape(ucfirst($u['role'] ?? 'N/A')); ?></span>
                            </td>
                            <td class="p-4">
                                <?php if ((bool)($u['is_blocked'] ?? false)): ?>
                                    <span class="badge badge-danger">Blocked</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-muted text-sm">
                                <?= isset($u['created_at']) ? (is_string($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : ($u['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $u['created_at']->toDateTime()->format('M d, Y') : 'N/A')) : 'N/A'; ?>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-end gap-2">
                                    <a href="/admin/users/detail?id=<?= escape((string)$u['_id']); ?>" class="btn btn-outline btn-sm" title="View Details">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                        Details
                                    </a>
                                    <?php if ((string)$u['_id'] !== (string)$currentUser['id']): ?>
                                        <form action="/admin/users/toggle-block" method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                                            <input type="hidden" name="user_id" value="<?= escape((string)$u['_id']); ?>">
                                            <input type="hidden" name="block" value="<?= (bool)($u['is_blocked'] ?? false) ? '0' : '1'; ?>">
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">
                                                    <?= (bool)($u['is_blocked'] ?? false) ? 'lock_open' : 'block'; ?>
                                                </span>
                                            </button>
                                        </form>
                                        <form action="/admin/users/delete" method="POST" onsubmit="return confirm('Delete this user forever?');" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                                            <input type="hidden" name="user_id" value="<?= escape((string)$u['_id']); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-sm text-muted" style="padding: 0.5rem;">You</span>
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
