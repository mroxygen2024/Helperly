<div class="container py-8">
    <div class="max-w-5xl mx-auto">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-800"><?= escape($title ?? 'Jobs'); ?></h1>
                <p class="text-muted mt-1"><?= escape($subtitle ?? 'Manage and track your work and applications.'); ?></p>
            </div>
            <?php if (($user['role'] ?? '') === 'parent'): ?>
                <a href="/dashboard" class="btn btn-primary">
                    <span class="material-symbols-outlined">add</span> Post New Job
                </a>
            <?php endif; ?>
        </header>

        <?php if (empty($jobs) && empty($applications)): ?>
            <div class="card p-12 text-center">
                <span class="material-symbols-outlined text-muted" style="font-size: 4rem; opacity: 0.3;">work_off</span>
                <h2 class="text-xl font-700 mt-4">Nothing to show yet</h2>
                <p class="text-muted mt-2">When you have active work or applications, they will appear here.</p>
                <a href="/dashboard" class="btn btn-outline mt-6">Go to Dashboard</a>
            </div>
        <?php else: ?>
            <div class="flex flex-col gap-4">
                <!-- Job List -->
                <?php if (!empty($jobs)): ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="card p-6 hover:shadow-md transition-all cursor-pointer border-l-4 border-l-primary" onclick="location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="badge badge-<?= $job['status'] === 'completed' ? 'success' : ($job['status'] === 'active' ? 'warning' : 'info'); ?>">
                                            <?= escape(ucfirst($job['status'])); ?>
                                        </span>
                                        <span class="text-xs text-muted font-600 uppercase tracking-widest">Job #<?= substr((string)$job['_id'], -6); ?></span>
                                    </div>
                                    <h3 class="text-xl font-800"><?= escape($job['service_type']); ?></h3>
                                    <p class="text-muted flex items-center gap-1 mt-1 text-sm">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                                        <?= escape($job['location']); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-800 text-primary"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> BDT</p>
                                    <p class="text-xs text-muted mt-1"><?= (float)($job['duration'] ?? 0); ?> hours</p>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-between items-center border-t pt-4">
                                <div class="flex items-center gap-4">
                                    <div class="text-xs">
                                        <p class="text-muted font-600 mb-0.5">Posted On</p>
                                        <p class="font-700"><?= isset($job['created_at']) ? (is_string($job['created_at']) ? date('M d, Y', strtotime($job['created_at'])) : ($job['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $job['created_at']->toDateTime()->format('M d, Y') : 'N/A')) : 'N/A'; ?></p>
                                    </div>
                                    <?php if (isset($job['time'])): ?>
                                        <div class="text-xs">
                                            <p class="text-muted font-600 mb-0.5">Scheduled For</p>
                                            <p class="font-700"><?= is_string($job['time']) ? date('M d, Y', strtotime($job['time'])) : ($job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('M d, Y h:i A') : 'N/A'); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex gap-2">
                                    <a href="/jobs/detail?id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm">View Details</a>
                                    <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-ghost btn-sm">Chat</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Applications List -->
                <?php if (!empty($applications)): ?>
                    <?php foreach ($applications as $app): ?>
                        <div class="card p-6 hover:shadow-md transition-all cursor-pointer border-l-4 border-l-info" onclick="location.href='/jobs/detail?id=<?= escape((string)$app['job_id']); ?>'">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="badge badge-<?= $app['status'] === 'accepted' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'info'); ?>">
                                            Application <?= escape(ucfirst($app['status'])); ?>
                                        </span>
                                        <span class="text-xs text-muted font-600 uppercase tracking-widest">App #<?= substr((string)$app['_id'], -6); ?></span>
                                    </div>
                                    <h3 class="text-xl font-800"><?= escape($app['job_data']['service_type'] ?? 'Job'); ?></h3>
                                    <p class="text-muted flex items-center gap-1 mt-1 text-sm">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                                        <?= escape($app['job_data']['location'] ?? 'N/A'); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-800 text-info">Pending</p>
                                    <p class="text-xs text-muted mt-1">Applied <?= isset($app['created_at']) ? (is_string($app['created_at']) ? date('M d', strtotime($app['created_at'])) : ($app['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $app['created_at']->toDateTime()->format('M d') : 'N/A')) : 'N/A'; ?></p>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-between items-center border-t pt-4">
                                <div class="text-xs">
                                    <p class="text-muted font-600 mb-0.5">Budget</p>
                                    <p class="font-700"><?= number_format((float)($app['job_data']['total_cost'] ?? 0), 2); ?> BDT</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="/jobs/detail?id=<?= escape((string)$app['job_id']); ?>" class="btn btn-outline btn-sm">View Job Posting</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
