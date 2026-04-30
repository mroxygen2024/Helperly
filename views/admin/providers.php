<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="card-title" style="font-size: 2rem;">Provider Management</h1>
        <p class="text-sm text-muted">Manage all service providers and their verification status</p>
    </div>
    <div class="flex items-center gap-3">
        <?php if (!empty($verifiedFilter)): ?>
            <a href="/admin/providers" class="btn btn-outline btn-sm">
                <span class="material-symbols-outlined" style="font-size: 16px;">filter_alt_off</span>
                Clear Filter
            </a>
            <span class="badge badge-success">Showing: <?= escape(ucfirst($verifiedFilter)); ?></span>
        <?php endif; ?>
        <div class="badge badge-info"><?= count($providers); ?> Providers</div>
    </div>
</div>

<!-- Quick Filter Tabs -->
<div class="flex gap-3 mb-6">
    <a href="/admin/providers" class="btn btn-sm <?= empty($verifiedFilter) ? 'btn-primary' : 'btn-outline'; ?>">All</a>
    <a href="/admin/providers?verified=approved" class="btn btn-sm <?= ($verifiedFilter ?? '') === 'approved' ? 'btn-primary' : 'btn-outline'; ?>">
        <span class="material-symbols-outlined" style="font-size: 14px;">verified</span>
        Approved
    </a>
    <a href="/admin/providers?verified=pending" class="btn btn-sm <?= ($verifiedFilter ?? '') === 'pending' ? 'btn-primary' : 'btn-outline'; ?>">
        <span class="material-symbols-outlined" style="font-size: 14px;">hourglass_top</span>
        Pending
    </a>
    <a href="/admin/providers?verified=rejected" class="btn btn-sm <?= ($verifiedFilter ?? '') === 'rejected' ? 'btn-primary' : 'btn-outline'; ?>">
        <span class="material-symbols-outlined" style="font-size: 14px;">cancel</span>
        Rejected
    </a>
</div>

<div class="card p-0 overflow-hidden">
    <?php if (empty($providers)): ?>
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-muted" style="font-size: 3rem;">badge</span>
            <p class="text-muted mt-2">No providers found<?= !empty($verifiedFilter) ? ' with this filter' : ''; ?>.</p>
        </div>
    <?php else: ?>
    <div class="p-0 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Provider</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Experience</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Hourly Rate</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Verification</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Rating</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($providers as $p): ?>
                    <tr class="hover:bg-slate-50 transition-colors border-b last:border-0">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($p['profile']['profile_photo'])): ?>
                                    <img src="<?= escape($p['profile']['profile_photo']); ?>" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="user-avatar" style="width: 40px; height: 40px; border-radius: 10px; font-size: 14px;"><?= mb_substr(escape($p['name'] ?? 'P'), 0, 1); ?></div>
                                <?php endif; ?>
                                <div class="flex flex-col">
                                    <span class="font-600"><?= escape($p['name'] ?? 'Unnamed'); ?></span>
                                    <span class="text-xs text-muted"><?= escape($p['email'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="text-sm font-500"><?= escape($p['profile']['experience'] ?? 'N/A'); ?></span>
                        </td>
                        <td class="p-4">
                            <span class="text-sm font-700 text-slate-800"><?= escape($p['profile']['rate'] ?? '0'); ?> BDT/hr</span>
                        </td>
                        <td class="p-4">
                            <?php $vStatus = (string)($p['profile']['verification_status'] ?? 'pending'); ?>
                            <span class="badge badge-<?= $vStatus === 'approved' ? 'success' : ($vStatus === 'rejected' ? 'danger' : 'warning'); ?>">
                                <?= escape(ucfirst($vStatus)); ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-warning" style="font-size: 16px;">star</span>
                                <span class="text-sm font-600"><?= number_format((float)($p['profile']['rating'] ?? 0), 1); ?></span>
                            </div>
                        </td>
                        <td class="p-4 text-right">
                            <a href="/admin/providers/detail?id=<?= escape((string)$p['_id']); ?>" class="btn btn-outline btn-sm">
                                <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                                Details
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
