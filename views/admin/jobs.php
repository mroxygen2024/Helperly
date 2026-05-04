<div class="animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-wrap justify-between items-center gap-6 mb-8">
        <div>
            <h1 class="page-title text-2xl font-black text-main mb-1">Job Management</h1>
            <p class="text-neutral-400 font-bold">Monitor and coordinate all service requests and active contracts</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-primary-50 text-primary px-4 py-2 rounded-2xl border border-primary-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">work</span>
                <span class="text-sm font-black"><?= count($jobs); ?> Total Jobs</span>
            </div>
        </div>
    </div>

    <!-- Quick Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-8 bg-white p-1.5 rounded-2xl shadow-sm border border-neutral-100 w-fit">
        <a href="/admin/jobs" class="px-6 py-2 rounded-xl text-xs font-black transition-all <?= empty($statusFilter) ? 'bg-primary text-white shadow-lg shadow-primary-200' : 'text-neutral-400 hover:text-main' ?>">
            ALL JOBS
        </a>
        <a href="/admin/jobs?status=open" class="px-6 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 <?= ($statusFilter ?? '') === 'open' ? 'bg-primary text-white shadow-lg shadow-primary-200' : 'text-neutral-400 hover:text-main' ?>">
            <span class="w-2 h-2 rounded-full bg-info"></span>
            OPEN
        </a>
        <a href="/admin/jobs?status=active" class="px-6 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 <?= ($statusFilter ?? '') === 'active' ? 'bg-primary text-white shadow-lg shadow-primary-200' : 'text-neutral-400 hover:text-main' ?>">
            <span class="w-2 h-2 rounded-full bg-warning"></span>
            ACTIVE
        </a>
        <a href="/admin/jobs?status=completed" class="px-6 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 <?= ($statusFilter ?? '') === 'completed' ? 'bg-primary text-white shadow-lg shadow-primary-200' : 'text-neutral-400 hover:text-main' ?>">
            <span class="w-2 h-2 rounded-full bg-success"></span>
            COMPLETED
        </a>
        <a href="/admin/jobs?status=cancelled" class="px-6 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 <?= ($statusFilter ?? '') === 'cancelled' ? 'bg-primary text-white shadow-lg shadow-primary-200' : 'text-neutral-400 hover:text-main' ?>">
            <span class="w-2 h-2 rounded-full bg-danger"></span>
            CANCELLED
        </a>
    </div>

    <!-- Jobs Table Card -->
    <div class="dashboard-card p-0 overflow-hidden">
        <?php if (empty($jobs)): ?>
            <div class="flex flex-col items-center justify-center py-24 text-center px-6">
                <div class="empty-state-icon bg-neutral-50 text-neutral-300">
                    <span class="material-symbols-outlined" style="font-size: 48px;">work_off</span>
                </div>
                <h3 class="text-lg font-black text-main mb-2">No jobs found</h3>
                <p class="text-neutral-400 font-bold max-w-sm">No jobs were found matching the selected filters. Try clearing filters to see more.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table-modern w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Job Type</th>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Participants</th>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Status</th>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Schedule</th>
                            <th class="px-6 py-4 text-left text-[10px] uppercase tracking-widest font-black text-neutral-400">Financials</th>
                            <th class="px-6 py-4 text-right text-[10px] uppercase tracking-widest font-black text-neutral-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        <?php foreach ($jobs as $job): ?>
                            <tr class="hover:bg-neutral-50/50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary flex items-center justify-center">
                                            <span class="material-symbols-outlined text-lg">work</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-extrabold text-main leading-tight"><?= escape($job['service_type'] ?? 'General'); ?></span>
                                            <span class="text-[10px] font-black text-neutral-400 uppercase mt-1">#<?= escape(substr((string)$job['_id'], -8)); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-extrabold text-main"><?= escape($job['parent']['name'] ?? 'N/A'); ?></span>
                                            <span class="text-[10px] font-bold text-neutral-400">Employer</span>
                                        </div>
                                        <span class="material-symbols-outlined text-neutral-200 text-sm">arrow_forward</span>
                                        <div class="flex flex-col">
                                            <?php if (isset($job['provider'])): ?>
                                                <span class="text-xs font-extrabold text-primary"><?= escape($job['provider']['name'] ?? 'N/A'); ?></span>
                                                <span class="text-[10px] font-bold text-neutral-400">Provider</span>
                                            <?php else: ?>
                                                <span class="text-xs font-black text-neutral-300 italic">Pending Assignment</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <?php
                                    $jStatus = $job['status'] ?? 'open';
                                    $jBadge = match ($jStatus) {
                                        'completed' => 'success',
                                        'active' => 'warning',
                                        'cancelled' => 'danger',
                                        default => 'info',
                                    };
                                    ?>
                                    <span class="badge badge-<?= $jBadge; ?> px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                        <?= escape($jStatus); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-extrabold text-main"><?= isset($job['time']) ? (is_string($job['time']) ? date('M d, h:i A', strtotime($job['time'])) : ($job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('M d, h:i A') : 'N/A')) : 'N/A'; ?></span>
                                        <span class="text-[10px] font-black text-neutral-400 uppercase mt-0.5"><?= escape($job['duration'] ?? 'N/A'); ?> Hours</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-main"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> <span class="text-[10px] font-bold text-neutral-400">ETB</span></span>
                                        <span class="text-[10px] font-black text-neutral-300 uppercase mt-0.5"><?= escape($job['payment_method'] ?? 'cash'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <a href="/admin/jobs/detail?id=<?= escape((string)$job['_id']); ?>" class="btn btn-ghost btn-sm p-2 hover:bg-primary-50 hover:text-primary transition-all">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
