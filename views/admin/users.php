<section class="page-header">
    <div>
        <h1>User Management</h1>
        <p class="muted">Manage all registered users, block suspicious accounts, or remove users.</p>
    </div>
</section>

<section class="card stack">
    <?php if (empty($users)): ?>
        <p class="muted">No users found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <strong><?= escape((string) ($user['name'] ?? 'N/A')); ?></strong>
                            </td>
                            <td><?= escape((string) ($user['email'] ?? 'N/A')); ?></td>
                            <td><span class="badge"><?= escape((string) ($user['role'] ?? 'N/A')); ?></span></td>
                            <td>
                                <?php if ((bool) ($user['is_blocked'] ?? false)): ?>
                                    <span class="badge error">Blocked</span>
                                <?php else: ?>
                                    <span class="badge success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex-row gap-small">
                                    <form action="/admin/users/toggle-block" method="POST" class="inline-form">
                                        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                                        <input type="hidden" name="user_id" value="<?= escape((string) $user['_id']); ?>">
                                        <input type="hidden" name="block" value="<?= (bool)($user['is_blocked'] ?? false) ? '0' : '1'; ?>">
                                        
                                        <?php if ((string)$user['_id'] !== (string)$currentUser['id']): ?>
                                            <button type="submit" class="btn btn-small <?= (bool)($user['is_blocked'] ?? false) ? 'btn-success' : 'btn-warning'; ?>">
                                                <?= (bool)($user['is_blocked'] ?? false) ? 'Unblock' : 'Block'; ?>
                                            </button>
                                        <?php else: ?>
                                            <button disabled class="btn btn-small muted">Current User</button>
                                        <?php endif; ?>
                                    </form>

                                    <?php if ((string)$user['_id'] !== (string)$currentUser['id']): ?>
                                        <form action="/admin/users/delete" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                                            <input type="hidden" name="user_id" value="<?= escape((string) $user['_id']); ?>">
                                            <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<style>
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    .table th, .table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    .table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
    }
    .btn-warning {
        background: #ffc107;
        color: #000;
    }
    .btn-success {
        background: #28a745;
        color: #fff;
    }
    .btn-danger {
        background: #dc3545;
        color: #fff;
    }
    .badge.error {
        background: #fde8e8;
        color: #c81e1e;
    }
    .badge.success {
        background: #def7ec;
        color: #03543f;
    }
</style>
