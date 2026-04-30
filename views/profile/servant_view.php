<div class="container py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Sidebar -->
        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-8">
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="user-avatar-rect mb-4" style="width: 120px; height: 120px; background: var(--grad-primary); font-size: 3rem;">
                        <?php if (!empty($profile['profile_photo'])): ?>
                            <img src="<?= escape($profile['profile_photo']); ?>" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?= mb_substr(escape($profile['full_name'] ?? $user['name'] ?? 'P'), 0, 1); ?>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-2xl font-800 mb-1"><?= escape($profile['full_name'] ?? $user['name'] ?? 'Provider'); ?></h1>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge badge-success flex items-center gap-1">
                            <span class="material-symbols-outlined" style="font-size: 14px;">verified</span> Verified
                        </span>
                        <div class="flex items-center gap-1 text-warning font-700">
                            <span class="material-symbols-outlined" style="font-size: 18px;">star</span>
                            <?= number_format((float)($rating['average'] ?? 0), 1); ?>
                            <span class="text-muted text-xs font-400">(<?= (int)($rating['count'] ?? 0); ?> reviews)</span>
                        </div>
                    </div>
                    <p class="text-muted text-sm"><?= escape($profile['location'] ?? 'Location not specified'); ?></p>
                </div>

                <div class="flex flex-col gap-4 border-t pt-6">
                    <div class="flex justify-between items-center">
                        <span class="text-muted text-sm">Hourly Rate</span>
                        <span class="font-700 text-primary"><?= escape($profile['rate'] ?? $profile['hourly_rate'] ?? 'N/A'); ?> <?= escape($profile['currency'] ?? 'BDT'); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted text-sm">Availability</span>
                        <span class="font-600 text-sm"><?= escape($profile['availability'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-muted text-sm">Experience</span>
                        <span class="font-600 text-sm"><?= escape($profile['experience'] ?? 'N/A'); ?></span>
                    </div>
                </div>

                <div class="flex flex-col gap-3 mt-8">
                    <?php if (($currentUser['role'] ?? '') === 'parent'): ?>
                        <a href="/job/book?provider_id=<?= escape((string)$user['_id']); ?>" class="btn btn-primary w-full py-4">
                            <span class="material-symbols-outlined">event_available</span> Hire Now
                        </a>
                        <a href="/messages?provider_id=<?= escape((string)$user['_id']); ?>" class="btn btn-outline w-full py-4">
                            <span class="material-symbols-outlined">chat</span> Message
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 flex flex-col gap-8">
            <!-- About Section -->
            <div class="card p-8">
                <h2 class="text-xl font-800 mb-4">Professional Overview</h2>
                <div class="prose max-w-none text-slate-700 leading-relaxed">
                    <?= nl2br(escape($profile['bio'] ?? 'This provider has not added a bio yet.')); ?>
                </div>
                
                <div class="mt-8">
                    <h3 class="text-sm font-800 uppercase tracking-widest text-muted mb-4">Skills & Services</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php if (!empty($profile['skills'])): ?>
                            <?php foreach ($profile['skills'] as $skill): ?>
                                <span class="badge badge-secondary py-2 px-4"><?= escape($skill); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm text-muted italic">No skills listed.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Work History & Reviews -->
            <div class="card p-0 overflow-hidden">
                <div class="card-header p-8 border-b">
                    <h2 class="text-xl font-800">Work History & Reviews</h2>
                </div>
                <div class="p-8">
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-12">
                            <span class="material-symbols-outlined text-muted" style="font-size: 3rem; opacity: 0.3;">rate_review</span>
                            <p class="text-muted mt-4">No reviews yet for this provider.</p>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col gap-8">
                            <?php foreach ($reviews as $review): ?>
                                <div class="border-b last:border-0 pb-8 last:pb-0">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="user-avatar" style="width: 40px; height: 40px;">
                                                <?= mb_substr(escape($review['parent']['name'] ?? 'E'), 0, 1); ?>
                                            </div>
                                            <div>
                                                <p class="font-700 m-0"><?= escape($review['parent']['name'] ?? 'Employer'); ?></p>
                                                <p class="text-xs text-muted"><?= $review['created_at']->toDateTime()->format('M d, Y'); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 text-warning">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <span class="material-symbols-outlined" style="font-size: 16px;">
                                                    <?= $i < $review['rating'] ? 'star' : 'star_outline'; ?>
                                                </span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-slate-700 italic">"<?= escape($review['review_text']); ?>"</p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
