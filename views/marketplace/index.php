<div class="text-center py-12" style="max-width: 800px; margin: 4rem auto;">
    <h1 style="font-size: 3.5rem; font-weight: 800; line-height: 1.1; margin-bottom: 1.5rem; letter-spacing: -0.02em; color: var(--bg-sidebar);">
        The Trusted Way to <span style="color: var(--primary);">Find Reliable Help.</span>
    </h1>
    <p class="text-xl text-muted mb-8" style="font-size: 1.25rem;">
        Helperly connects families with verified service providers for household tasks, cleaning, child care, and more.
    </p>

    <div class="flex justify-center gap-4 mt-8">
        <a href="/register" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.125rem;">Get Started Today</a>
        <a href="/servants" class="btn btn-outline" style="padding: 1rem 2rem; font-size: 1.125rem;">Browse Providers</a>
    </div>

    <!-- Featured Providers Section -->
    <?php if (!empty($featuredProviders)): ?>
    <div class="mt-20">
        <div class="flex justify-between items-end mb-8">
            <div class="text-left">
                <h2 style="font-size: 1.85rem; font-weight: 800; color: var(--bg-sidebar);">Featured Providers</h2>
                <p class="text-muted">Top-rated and recently verified specialists in your area.</p>
            </div>
            <a href="/servants" class="btn btn-sm btn-outline">View Directory</a>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <?php foreach ($featuredProviders as $profile): ?>
                <div class="card p-5 text-left flex flex-col" style="border: 1px solid var(--border-light); transition: transform 0.2s; cursor: default;">
                    <div class="flex items-center gap-4 mb-4">
                        <?php if (!empty($profile['profile_photo'])): ?>
                            <img src="<?= escape($profile['profile_photo']); ?>" style="width: 56px; height: 56px; border-radius: 12px; object-fit: cover;">
                        <?php else: ?>
                            <div class="user-avatar-rect" style="width: 56px; height: 56px; border-radius: 12px; background: var(--grad-primary); font-size: 1.25rem;">
                                <?= mb_substr(escape($profile['full_name'] ?? 'P'), 0, 1); ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h4 class="font-700" style="margin: 0;"><?= escape($profile['full_name'] ?? 'Provider'); ?></h4>
                            <div class="flex items-center gap-1 text-xs text-muted mt-1">
                                <span class="material-symbols-outlined" style="font-size: 14px;">location_on</span>
                                <?= escape($profile['location'] ?? 'Unknown'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-1 mb-4">
                            <?php foreach (array_slice((array)($profile['skills'] ?? []), 0, 2) as $skill): ?>
                                <span class="badge badge-secondary" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;"><?= escape($skill); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-sm text-muted line-clamp-2" style="font-style: italic;">
                             "<?= escape($profile['experience'] ?? 'Expert provider'); ?>"
                        </p>
                    </div>

                    <div class="mt-4 pt-4 border-t flex justify-between items-center">
                        <span class="text-primary font-700"><?= escape($profile['hourly_rate'] ?? '0'); ?> <span class="text-xs font-400 text-muted">/hr</span></span>
                        <a href="/job/book?provider_id=<?= escape((string)$profile['user_id']); ?>" class="text-xs font-600 uppercase letter-spacing-lg text-primary hover-underline">Book Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-3 gap-8 mt-16 pt-12 border-t">
        <div class="flex flex-col items-center">
            <span class="material-symbols-outlined text-primary" style="font-size: 2.5rem;">verified</span>
            <h3 class="font-600 mt-2">Verified Providers</h3>
            <p class="text-sm text-muted">Rigorous vetting process for your peace of mind.</p>
        </div>
        <div class="flex flex-col items-center">
            <span class="material-symbols-outlined text-primary" style="font-size: 2.5rem;">payments</span>
            <h3 class="font-600 mt-2">Secure Payments</h3>
            <p class="text-sm text-muted">Pay only when you are satisfied with the work.</p>
        </div>
        <div class="flex flex-col items-center">
            <span class="material-symbols-outlined text-primary" style="font-size: 2.5rem;">chat_bubble_outline</span>
            <h3 class="font-600 mt-2">Direct Messaging</h3>
            <p class="text-sm text-muted">Communicate clearly before hiring.</p>
        </div>
    </div>
</div>
