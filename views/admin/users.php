<div class="animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-wrap justify-between items-center gap-6 mb-8">
        <div>
            <h1 class="page-title text-2xl font-black text-main mb-1">User Management</h1>
            <p class="text-neutral-400 font-bold">Manage system users, permissions, and account status</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-primary-50 text-primary px-4 py-2 rounded-2xl border border-primary-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">group</span>
                <span class="text-sm font-black"><?= count($users); ?> Total Users</span>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="dashboard-card p-0 overflow-hidden">
        <?php if (empty($users)): ?>
            <div class="flex flex-col items-center justify-center py-24 text-center px-6">
                <div class="empty-state-icon bg-neutral-50 text-neutral-300">
                    <span class="material-symbols-outlined" style="font-size: 48px;">person_off</span>
                </div>
                <h3 class="text-lg font-black text-main mb-2">No users found</h3>
                <p class="text-neutral-400 font-bold max-w-sm">The user database appears to be empty or no results matched your search.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table-modern w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">User Identity</th>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Role</th>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Status</th>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Joined Date</th>
                            <th class="px-6 py-4 text-right text-[10px] uppercase tracking-widest font-black text-neutral-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-neutral-50/50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="user-avatar w-10 h-10 rounded-xl text-xs">
                                            <?= mb_substr(escape($u['name'] ?? '?'), 0, 1); ?>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-extrabold text-main leading-tight"><?= escape($u['name'] ?? 'N/A'); ?></span>
                                            <span class="text-[11px] font-bold text-neutral-400 mt-0.5"><?= escape($u['email'] ?? 'N/A'); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="text-xs font-black text-neutral-500 uppercase tracking-wider">
                                        <?= escape(str_replace('_', ' ', $u['role'] ?? 'N/A')); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <?php if ((bool)($u['is_blocked'] ?? false)): ?>
                                        <span class="badge badge-danger px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">Blocked</span>
                                    <?php else: ?>
                                        <span class="badge badge-success px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-main"><?= isset($u['created_at']) ? (is_string($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : ($u['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $u['created_at']->toDateTime()->format('M d, Y') : 'N/A')) : 'N/A'; ?></span>
                                        <span class="text-[10px] font-black text-neutral-300 uppercase"><?= escape($u['phone'] ?? 'No phone'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="/admin/users/detail?id=<?= escape((string)$u['_id']); ?>" class="btn btn-ghost btn-sm p-2 hover:bg-primary-50 hover:text-primary transition-all" title="View Profile">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </a>
                                        
                                        <?php if ((string)$u['_id'] !== (string)$currentUser['id']): ?>
                                            <form action="/admin/users/toggle-block" method="POST" class="inline-block">
                                                <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                                                <input type="hidden" name="user_id" value="<?= escape((string)$u['_id']); ?>">
                                                <input type="hidden" name="block" value="<?= (bool)($u['is_blocked'] ?? false) ? '0' : '1'; ?>">
                                                <button type="submit" class="btn btn-ghost btn-sm p-2 <?= (bool)($u['is_blocked'] ?? false) ? 'text-success hover:bg-success-50' : 'text-warning hover:bg-warning-soft' ?> transition-all" title="<?= (bool)($u['is_blocked'] ?? false) ? 'Unblock' : 'Block' ?>">
                                                    <span class="material-symbols-outlined">
                                                        <?= (bool)($u['is_blocked'] ?? false) ? 'lock_open' : 'block'; ?>
                                                    </span>
                                                </button>
                                            </form>
                                            
                                            <form action="/admin/users/delete" method="POST" onsubmit="return confirm('Delete this user forever? This cannot be undone.');" class="inline-block">
                                                <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                                                <input type="hidden" name="user_id" value="<?= escape((string)$u['_id']); ?>">
                                                <button type="submit" class="btn btn-ghost btn-sm p-2 text-danger hover:bg-danger-light transition-all" title="Delete Account">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-[10px] font-black text-primary bg-primary-50 px-3 py-1 rounded-lg uppercase tracking-widest">Self</span>
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
</div>
