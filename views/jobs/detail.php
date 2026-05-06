<div class="container py-8">
    <div class="max-w-4xl mx-auto">
        <nav class="mb-6">
            <a href="/dashboard" class="flex items-center text-primary font-600 hover:underline gap-1">
                <span class="material-symbols-outlined">arrow_back</span>
                Back to Dashboard
            </a>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 flex flex-col gap-6">
                <div class="card p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="badge badge-primary mb-2"><?= escape(ucfirst($job['status'])); ?></span>
                            <h1 class="text-3xl font-800"><?= escape($job['service_type']); ?></h1>
                            <p class="text-muted flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined" style="font-size: 18px;">location_on</span>
                                <?= escape($job['location']); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-muted font-600">Total Budget</p>
                            <p class="text-2xl font-800 text-primary"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> ETB</p>
                            <p class="text-xs text-muted mt-1"><?= escape($job['rate'] ?? 0); ?> ETB / hr • <?= (float)($job['duration'] ?? 0); ?> hrs</p>
                        </div>
                    </div>

                    <div class="border-t pt-6 mb-6">
                        <h2 class="text-lg font-700 mb-4">Job Description & Instructions</h2>
                        <div class="prose max-w-none text-slate-700 leading-relaxed bg-gray-50 p-6 rounded-xl border">
                            <?= nl2br(escape($job['instructions'] ?? 'No special instructions provided.')); ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 border-t pt-6">
                        <div>
                            <h3 class="text-xs font-800 uppercase tracking-widest text-muted mb-2">Scheduled Time</h3>
                            <p class="font-600 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary" style="font-size: 20px;">calendar_today</span>
                                <?= isset($job['time']) ? (is_string($job['time']) ? date('M d, Y h:i A', strtotime($job['time'])) : ($job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('M d, Y h:i A') : 'N/A')) : 'N/A'; ?>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-xs font-800 uppercase tracking-widest text-muted mb-2">Payment Method</h3>
                            <p class="font-600 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary" style="font-size: 20px;">payments</span>
                                <?= escape(ucfirst($job['payment_method'] ?? 'cash')); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if ($job['status'] === 'completed' && !empty($job['review'])): ?>
                    <div class="card p-8 bg-success-soft border-none">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-700">Client Feedback</h2>
                            <div class="flex items-center gap-1 text-warning">
                                <?php for($i=0; $i<5; $i++): ?>
                                    <span class="material-symbols-outlined" style="font-size: 20px;">
                                        <?= $i < $job['review']['rating'] ? 'star' : 'star_outline'; ?>
                                    </span>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="text-slate-700 italic text-lg leading-relaxed">"<?= escape($job['review']['review_text']); ?>"</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="flex flex-col gap-6">
                <!-- Client/Provider Info -->
                <div class="card p-6">
                    <?php if (($user['role'] ?? '') === 'provider'): ?>
                        <h3 class="text-xs font-800 uppercase tracking-widest text-muted mb-4">About the Client</h3>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="user-avatar" style="width: 48px; height: 48px;">
                                <?= mb_substr(escape($job['parent']['name'] ?? 'C'), 0, 1); ?>
                            </div>
                            <div>
                                <p class="font-700"><?= escape($job['parent']['name'] ?? 'Client'); ?></p>
                                <a href="/parent/view.php?id=<?= escape((string)$job['parent_id']); ?>" class="text-xs text-primary hover:underline">View Profile</a>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted">Status</span>
                                <span class="font-600 text-success flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">verified</span> Verified
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                        <h3 class="text-xs font-800 uppercase tracking-widest text-muted mb-4">Assigned Provider</h3>
                        <?php if (isset($job['provider'])): ?>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="user-avatar" style="width: 48px; height: 48px;">
                                    <?= mb_substr(escape($job['provider']['name'] ?? 'P'), 0, 1); ?>
                                </div>
                                <div>
                                    <p class="font-700"><?= escape($job['provider']['name'] ?? 'Provider'); ?></p>
                                    <a href="/provider/view.php?id=<?= escape((string)$job['selected_provider_id']); ?>" class="text-xs text-primary hover:underline">View Profile</a>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 text-sm border-t pt-4 mt-2">
                                <div class="flex justify-between">
                                    <span class="text-muted">Verification</span>
                                    <span class="font-600 text-success flex items-center gap-1">
                                        <span class="material-symbols-outlined" style="font-size: 14px;">verified</span> Verified
                                    </span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 bg-gray-50 rounded-xl border border-dashed">
                                <p class="text-sm text-muted">No provider assigned yet.</p>
                                <a href="/servants" class="text-xs text-primary font-600 hover:underline mt-1 block">Browse Providers</a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="card p-6">
                    <h3 class="text-xs font-800 uppercase tracking-widest text-muted mb-4">Actions</h3>
                    <div class="flex flex-col gap-3">
                        <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline w-full py-3 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">chat</span> Chat
                        </a>

                        <?php if ($job['status'] === 'open' && ($user['role'] ?? '') === 'provider'): ?>
                            <a href="/jobs/apply?id=<?= escape((string)$job['_id']); ?>" class="btn btn-primary w-full py-3 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">assignment</span> Apply Now
                            </a>
                        <?php endif; ?>

                        <?php if ($job['status'] === 'active'): ?>
                            <form action="/jobs/confirm" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                                <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
                                <button type="submit" class="btn btn-primary w-full py-3 flex items-center justify-center gap-2" 
                                        <?= ((($user['role'] ?? '') === 'parent' && ($job['parent_confirmed'] ?? false)) || (($user['role'] ?? '') === 'provider' && ($job['provider_confirmed'] ?? false))) ? 'disabled' : ''; ?>>
                                    <span class="material-symbols-outlined">check_circle</span>
                                    <?= ((($user['role'] ?? '') === 'parent' && ($job['parent_confirmed'] ?? false)) || (($user['role'] ?? '') === 'provider' && ($job['provider_confirmed'] ?? false))) ? 'Confirmed' : 'Confirm Completion'; ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($job['status'] === 'completed' && ($user['role'] ?? '') === 'parent' && empty($job['review'])): ?>
                            <button type="button" class="btn btn-warning w-full py-3 flex items-center justify-center gap-2" data-open-modal="review_modal">
                                <span class="material-symbols-outlined">rate_review</span> Leave Review
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($job['status'] === 'completed' && ($user['role'] ?? '') === 'parent' && empty($job['review'])): ?>
<div id="review_modal" class="modal-overlay" data-modal>
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2 class="card-title">Leave a Review</h2>
            <button type="button" class="btn btn-ghost" data-close-modal="review_modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="/reviews" method="POST" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
            <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
            
            <div class="form-group">
                <label class="block text-sm font-600 mb-2">Rating</label>
                <div class="flex gap-2 rating-stars">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="<?= $i; ?>" class="hidden" required>
                            <span class="material-symbols-outlined text-gray-300" style="font-size: 32px;">star</span>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="block text-sm font-600 mb-2">Your Experience</label>
                <textarea name="review_text" class="input-field" placeholder="How was the service?" required style="height: 120px;"></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-full py-3">Submit Review</button>
        </form>
    </div>
</div>

<script>
(() => {
    const stars = document.querySelectorAll('.rating-stars input');
    stars.forEach(star => {
        star.onchange = () => {
            const val = parseInt(star.value);
            stars.forEach((s, idx) => {
                const icon = s.nextElementSibling;
                if (idx < val) {
                    icon.classList.remove('text-gray-300');
                    icon.classList.add('text-warning');
                    icon.textContent = 'star';
                } else {
                    icon.classList.remove('text-warning');
                    icon.classList.add('text-gray-300');
                    icon.textContent = 'star';
                }
            });
        };
    });

    const openBtns = document.querySelectorAll('[data-open-modal]');
    const closeBtns = document.querySelectorAll('[data-close-modal]');

    openBtns.forEach(btn => {
        btn.onclick = () => {
            const modal = document.getElementById(btn.dataset.openModal);
            if (modal) modal.classList.add('open');
        }
    });

    closeBtns.forEach(btn => {
        btn.onclick = () => {
            const modal = document.getElementById(btn.dataset.closeModal);
            if (modal) modal.classList.remove('open');
        }
    });
})();
</script>
<?php endif; ?>
