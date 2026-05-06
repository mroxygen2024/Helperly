<div class="container py-8">
    <div class="flex flex-col gap-8">
        <div>
            <h1 class="text-3xl font-900 mb-2">Transaction History</h1>
            <p class="text-muted">Review your payments and earnings on the platform.</p>
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-800 text-muted uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-800 text-muted uppercase tracking-wider">Service</th>
                            <th class="px-6 py-4 text-left text-xs font-800 text-muted uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-800 text-muted uppercase tracking-wider">Method</th>
                            <th class="px-6 py-4 text-left text-xs font-800 text-muted uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-800 text-muted uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-muted italic">
                                    No payment records found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $payment): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?= $payment['created_at']->toDateTime()->format('M d, Y'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-700"><?= escape($payment['job']['service_type'] ?? 'N/A'); ?></div>
                                        <div class="text-xs text-muted font-mono"><?= escape((string)$payment['job_id']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-800 text-primary">
                                        <?= number_format((float)($payment['amount'] ?? 0), 2); ?> ETB
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm uppercase">
                                        <?= escape($payment['method'] ?? 'cash'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="badge badge-<?= $payment['status'] === 'paid' ? 'success' : 'warning'; ?>">
                                            <?= escape(ucfirst($payment['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="/jobs/detail?id=<?= escape((string)$payment['job_id']); ?>" class="text-primary hover:text-primary-dark">View Job</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
