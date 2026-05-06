<div class="flex items-center gap-4 mb-8">
    <a href="/admin/jobs" class="btn btn-outline btn-sm">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <div>
        <h1 class="card-title" style="font-size: 2rem;">Job Details</h1>
        <p class="text-sm text-muted">ID: <?= escape((string)$job['_id']); ?></p>
    </div>
</div>

<?php
$status = $job['status'] ?? 'open';
$badgeClass = match ($status) {
    'completed' => 'badge-success',
    'active' => 'badge-warning',
    'cancelled' => 'badge-danger',
    default => 'badge-info',
};
?>

<div class="grid grid-cols-3 gap-8">
    <!-- Left: Quick Info -->
    <div class="card" style="grid-column: span 1;">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="user-avatar-rect" style="width: 80px; height: 80px; font-size: 2rem; margin-bottom: 1.5rem; background: var(--grad-primary);">
                <span class="material-symbols-outlined" style="color: white; font-size: 2rem;">work</span>
            </div>
            <h2 class="font-800 text-xl mb-2"><?= escape($job['service_type'] ?? 'General'); ?></h2>
            <span class="badge <?= $badgeClass; ?>"><?= escape(ucfirst($status)); ?></span>
        </div>

        <div class="w-full text-left">
            <div class="flex justify-between py-3 border-t">
                <span class="text-sm text-muted">Location</span>
                <span class="text-sm font-600"><?= escape($job['location'] ?? 'N/A'); ?></span>
            </div>
            <div class="flex justify-between py-3 border-t">
                <span class="text-sm text-muted">Duration</span>
                <span class="text-sm font-600"><?= escape($job['duration'] ?? 'N/A'); ?></span>
            </div>
            <div class="flex justify-between py-3 border-t">
                <span class="text-sm text-muted">Hourly Rate</span>
                <span class="text-sm font-600"><?= number_format((float)($job['rate'] ?? 0), 2); ?> ETB</span>
            </div>
            <div class="flex justify-between py-3 border-t">
                <span class="text-sm text-muted">Total Cost</span>
                <span class="text-sm font-700 text-primary"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> ETB</span>
            </div>
            <div class="flex justify-between py-3 border-t">
                <span class="text-sm text-muted">Payment Method</span>
                <span class="text-sm font-600"><?= escape(ucfirst($job['payment_method'] ?? 'cash')); ?></span>
            </div>
            <div class="flex justify-between py-3 border-t">
                <span class="text-sm text-muted">Scheduled Time</span>
                <span class="text-sm font-600"><?= isset($job['time']) ? (is_string($job['time']) ? date('M d, Y h:i A', strtotime($job['time'])) : ($job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('M d, Y h:i A') : 'N/A')) : 'N/A'; ?></span>
            </div>
            <div class="flex justify-between py-3 border-t">
                <span class="text-sm text-muted">Created</span>
                <span class="text-sm font-600"><?= isset($job['created_at']) ? (is_string($job['created_at']) ? date('M d, Y', strtotime($job['created_at'])) : ($job['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $job['created_at']->toDateTime()->format('M d, Y') : 'N/A')) : 'N/A'; ?></span>
            </div>
        </div>
    </div>

    <!-- Right: Details -->
    <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 2rem;">
        <!-- Instructions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Instructions</h3>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200" style="white-space: pre-wrap;">
                <?= escape($job['instructions'] ?? 'No special instructions provided.'); ?>
            </div>
        </div>

        <!-- Parent Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Parent (Client)</h3>
            </div>
            <?php if (isset($job['parent'])): ?>
                <div class="flex items-center gap-4">
                    <div class="user-avatar-rect" style="width: 56px; height: 56px; background: var(--grad-primary);">
                        <?= mb_substr(escape($job['parent']['name'] ?? 'P'), 0, 1); ?>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-700 text-lg"><?= escape($job['parent']['name'] ?? 'Unknown'); ?></span>
                        <span class="text-sm text-muted"><?= escape($job['parent']['email'] ?? 'N/A'); ?></span>
                        <span class="text-sm text-muted"><?= escape($job['parent']['phone'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="mt-auto ml-auto">
                        <a href="/admin/users/detail?id=<?= escape((string)$job['parent_id']); ?>" class="btn btn-outline btn-sm">
                            <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                            View Profile
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-muted italic">Parent information unavailable.</p>
            <?php endif; ?>
        </div>

        <!-- Provider Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Assigned Provider</h3>
            </div>
            <?php if (isset($job['provider'])): ?>
                <div class="flex items-center gap-4">
                    <div class="user-avatar-rect" style="width: 56px; height: 56px; background: var(--secondary);">
                        <?= mb_substr(escape($job['provider']['name'] ?? 'S'), 0, 1); ?>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-700 text-lg"><?= escape($job['provider']['name'] ?? 'Unknown'); ?></span>
                        <span class="text-sm text-muted"><?= escape($job['provider']['email'] ?? 'N/A'); ?></span>
                        <span class="text-sm text-muted"><?= escape($job['provider']['phone'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="mt-auto ml-auto">
                        <a href="/admin/providers/detail?id=<?= escape((string)$job['selected_provider_id']); ?>" class="btn btn-outline btn-sm">
                            <span class="material-symbols-outlined" style="font-size: 16px;">badge</span>
                            View Profile
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-6 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                    <span class="material-symbols-outlined text-muted" style="font-size: 2rem; opacity: 0.5;">person_off</span>
                    <p class="text-muted mt-2">No provider has been assigned to this job yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Confirmation Status -->
        <?php if ($status === 'active' || $status === 'completed'): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Completion Status</h3>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="flex items-center gap-3 p-4 rounded-xl border <?= !empty($job['parent_confirmed']) ? 'bg-green-50 border-green-200' : 'bg-slate-50 border-slate-200'; ?>">
                    <span class="material-symbols-outlined" style="font-size: 28px; color: <?= !empty($job['parent_confirmed']) ? 'var(--success)' : 'var(--text-muted)'; ?>;">
                        <?= !empty($job['parent_confirmed']) ? 'check_circle' : 'hourglass_empty'; ?>
                    </span>
                    <div>
                        <p class="font-700 text-sm">Parent Confirmation</p>
                        <p class="text-xs text-muted"><?= !empty($job['parent_confirmed']) ? 'Confirmed' : 'Pending'; ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 rounded-xl border <?= !empty($job['provider_confirmed']) ? 'bg-green-50 border-green-200' : 'bg-slate-50 border-slate-200'; ?>">
                    <span class="material-symbols-outlined" style="font-size: 28px; color: <?= !empty($job['provider_confirmed']) ? 'var(--success)' : 'var(--text-muted)'; ?>;">
                        <?= !empty($job['provider_confirmed']) ? 'check_circle' : 'hourglass_empty'; ?>
                    </span>
                    <div>
                        <p class="font-700 text-sm">Provider Confirmation</p>
                        <p class="text-xs text-muted"><?= !empty($job['provider_confirmed']) ? 'Confirmed' : 'Pending'; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
