<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="card-title" style="font-size: 2rem;">Job Management</h1>
        <p class="text-sm text-muted">Monitor all service requests across the platform</p>
    </div>
    <div class="flex items-center gap-3">
        <?php if (!empty($statusFilter)): ?>
            <a href="/admin/jobs" class="btn btn-outline btn-sm">
                <span class="material-symbols-outlined" style="font-size: 16px;">filter_alt_off</span>
                Clear Filter
            </a>
            <span class="badge badge-warning">Showing: <?= escape(ucfirst($statusFilter)); ?></span>
        <?php endif; ?>
        <div class="badge badge-info"><?= count($jobs); ?> Jobs</div>
    </div>
</div>

<!-- Quick Filter Tabs -->
<div class="flex gap-3 mb-6">
    <a href="/admin/jobs" class="btn btn-sm <?= empty($statusFilter) ? 'btn-primary' : 'btn-outline'; ?>">All</a>
    <a href="/admin/jobs?status=open" class="btn btn-sm <?= ($statusFilter ?? '') === 'open' ? 'btn-primary' : 'btn-outline'; ?>">
        <span class="material-symbols-outlined" style="font-size: 14px;">fiber_new</span>
        Open
    </a>
    <a href="/admin/jobs?status=active" class="btn btn-sm <?= ($statusFilter ?? '') === 'active' ? 'btn-primary' : 'btn-outline'; ?>">
        <span class="material-symbols-outlined" style="font-size: 14px;">play_circle</span>
        Active
    </a>
    <a href="/admin/jobs?status=completed" class="btn btn-sm <?= ($statusFilter ?? '') === 'completed' ? 'btn-primary' : 'btn-outline'; ?>">
        <span class="material-symbols-outlined" style="font-size: 14px;">check_circle</span>
        Completed
    </a>
    <a href="/admin/jobs?status=cancelled" class="btn btn-sm <?= ($statusFilter ?? '') === 'cancelled' ? 'btn-primary' : 'btn-outline'; ?>">
        <span class="material-symbols-outlined" style="font-size: 14px;">cancel</span>
        Cancelled
    </a>
</div>

<div class="card p-0 overflow-hidden">
    <?php if (empty($jobs)): ?>
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-muted" style="font-size: 3rem;">work</span>
            <p class="text-muted mt-2">No jobs found<?= !empty($statusFilter) ? ' with status "' . escape($statusFilter) . '"' : ''; ?>.</p>
        </div>
    <?php else: ?>
    <div class="p-0 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Job Type</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Parent (Client)</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Provider</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Status</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Time / Duration</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Budget</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $job): ?>
                    <tr class="hover:bg-slate-50 transition-colors border-b last:border-0">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--primary-glow); display: flex; align-items: center; justify-content: center;">
                                    <span class="material-symbols-outlined" style="font-size: 18px; color: var(--primary);">work</span>
                                </div>
                                <span class="font-600"><?= escape($job['service_type'] ?? 'General'); ?></span>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex flex-col">
                                <span class="font-600"><?= escape($job['parent']['name'] ?? 'N/A'); ?></span>
                                <span class="text-xs text-muted"><?= escape($job['parent']['email'] ?? 'N/A'); ?></span>
                            </div>
                        </td>
                        <td class="p-4">
                            <?php if (isset($job['provider'])): ?>
                                <div class="flex flex-col">
                                    <span class="font-600"><?= escape($job['provider']['name'] ?? 'N/A'); ?></span>
                                    <span class="text-xs text-muted"><?= escape($job['provider']['email'] ?? 'N/A'); ?></span>
                                </div>
                            <?php else: ?>
                                <span class="badge badge-secondary" style="font-size: 10px;">Unassigned</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <?php
                            $jStatus = $job['status'] ?? 'open';
                            $jBadge = match ($jStatus) {
                                'completed' => 'badge-success',
                                'active' => 'badge-warning',
                                'cancelled' => 'badge-danger',
                                default => 'badge-info',
                            };
                            ?>
                            <span class="badge <?= $jBadge; ?>"><?= escape(ucfirst($jStatus)); ?></span>
                        </td>
                        <td class="p-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-600"><?= isset($job['time']) ? (is_string($job['time']) ? date('M d, h:i A', strtotime($job['time'])) : ($job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('M d, h:i A') : 'N/A')) : 'N/A'; ?></span>
                                <span class="text-xs text-muted"><?= escape($job['duration'] ?? 'N/A'); ?></span>
                            </div>
                        </td>
                        <td class="p-4 font-700 text-slate-800">
                            <?= number_format((float)($job['total_cost'] ?? 0), 2); ?> BDT
                        </td>
                        <td class="p-4 text-right">
                            <a href="/admin/jobs/detail?id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm">
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
