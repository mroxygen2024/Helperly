<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="card-title" style="font-size: 2rem;">Job Management</h1>
        <p class="text-sm text-muted">Monitor all service requests across the platform</p>
    </div>
    <div class="badge badge-info"><?= count($jobs); ?> Total Jobs</div>
</div>

<div class="card p-0 overflow-hidden shadow-xl border-none">
    <div class="card-header bg-white p-6 border-b">
        <h2 class="font-700">All System Jobs</h2>
    </div>
    <div class="p-0 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Job Type</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Parent (Client)</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Provider</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Status</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b">Budget</th>
                    <th class="p-4 text-xs font-800 uppercase text-muted letter-spacing-lg border-b text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $job): ?>
                    <tr class="hover:bg-slate-50 transition-colors border-b last:border-0">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-primary-soft text-primary">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">work</span>
                                </div>
                                <span class="font-600"><?= escape($job['service_type']); ?></span>
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
                            <span class="badge badge-<?= $job['status'] === 'completed' ? 'success' : ($job['status'] === 'active' ? 'warning' : 'info'); ?>">
                                <?= escape($job['status']); ?>
                            </span>
                        </td>
                        <td class="p-4 font-700 text-slate-800">
                            <?= number_format((float)($job['total_cost'] ?? 0), 2); ?> BDT
                        </td>
                        <td class="p-4 text-right">
                            <button type="button" class="btn btn-outline btn-sm" data-open-modal="job_modal_<?= escape((string)$job['_id']); ?>">
                                <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Job Details Modals -->
<?php foreach ($jobs as $job): ?>
    <div id="job_modal_<?= escape((string)$job['_id']); ?>" class="modal-overlay" data-modal>
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div class="flex items-center gap-4">
                    <div class="user-avatar-rect" style="background: var(--grad-primary);">
                        <span class="material-symbols-outlined" style="color: white;">work</span>
                    </div>
                    <div>
                        <h2 class="card-title" style="margin: 0;"><?= escape($job['service_type']); ?> Details</h2>
                        <p class="text-xs text-muted">Job ID: <?= escape((string)$job['_id']); ?></p>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-sm" data-close-modal="job_modal_<?= escape((string)$job['_id']); ?>" style="border:none; padding: 0.5rem; border-radius: 50%;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="modal-body" style="background: #F8FAFC; padding: 2rem;">
                <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Location</span>
                        <span class="font-700"><?= escape($job['location'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Status</span>
                        <span class="badge badge-<?= $job['status'] === 'completed' ? 'success' : 'info'; ?>" style="font-size: 10px; width: fit-content;"><?= escape(ucfirst($job['status'])); ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Start Time</span>
                        <span class="font-700"><?= isset($job['time']) ? (is_string($job['time']) ? date('M d, Y h:i A', strtotime($job['time'])) : ($job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('M d, Y h:i A') : 'N/A')) : 'N/A'; ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-muted font-600">Earnings</span>
                        <span class="font-700 text-primary"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> BDT</span>
                    </div>
                </div>

                <div class="flex flex-col mb-6 bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-xs text-muted font-600 mb-2">Instructions</span>
                    <p class="text-sm text-slate-700 italic" style="white-space: pre-wrap;"><?= escape($job['instructions'] ?? 'No special instructions.'); ?></p>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="flex flex-col bg-slate-100 p-3 rounded-lg border border-slate-200">
                        <span class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-1 block">Parent</span>
                        <p class="font-700 text-sm m-0"><?= escape($job['parent']['name'] ?? 'Unknown'); ?></p>
                        <p class="text-xs text-muted m-0"><?= escape($job['parent']['email'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="flex flex-col bg-slate-100 p-3 rounded-lg border border-slate-200">
                        <span class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-1 block">Provider</span>
                        <p class="font-700 text-sm m-0"><?= escape($job['provider']['name'] ?? 'None'); ?></p>
                        <p class="text-xs text-muted m-0"><?= escape($job['provider']['email'] ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline w-full" data-close-modal="job_modal_<?= escape((string)$job['_id']); ?>">Close Detail</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
(() => {
    const openButtons = document.querySelectorAll('[data-open-modal]');
    const closeButtons = document.querySelectorAll('[data-close-modal]');

    openButtons.forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
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
