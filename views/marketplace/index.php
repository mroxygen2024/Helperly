<div class="hero-section">
    <h1 class="hero-title">
        The Trusted Way to <span class="text-primary">Find Reliable Help.</span>
    </h1>
    <p class="text-xl text-muted mb-8">
        Helperly connects families with verified service providers for household tasks, cleaning, child care, and more.
    </p>

    <div class="home-search-container">
        <form action="/servants" method="GET" class="flex items-center w-full gap-2">
            <div class="flex-1 flex items-center px-4 border-r border-neutral-100">
                <span class="material-symbols-outlined text-muted mr-2">location_on</span>
                <input type="text" name="location" placeholder="Which city?" class="w-full border-none focus:ring-0 text-sm py-3" style="outline: none; background: transparent;">
            </div>
            <div class="flex-1 flex items-center px-4">
                <span class="material-symbols-outlined text-muted mr-2">construction</span>
                <input type="text" name="skill" placeholder="What skill?" class="w-full border-none focus:ring-0 text-sm py-3" style="outline: none; background: transparent;">
            </div>
            <button type="submit" class="btn btn-primary rounded-lg">
                <span class="material-symbols-outlined">search</span>
            </button>
        </form>
    </div>
    <p class="text-xs text-muted mt-4">Popular: Cleaning, Baby Sitting, Home Tutor, Gardening</p>

    <div class="flex flex-wrap justify-center gap-4 mt-8">
        <a href="/register" class="btn btn-primary px-8 py-4 text-lg">Join as Client</a>
        <a href="/profile/servant" class="btn btn-outline px-8 py-4 text-lg">Apply as Provider</a>
    </div>

    <!-- Featured Providers Section -->
    <?php if (!empty($featuredProviders)): ?>
    <div class="mt-12 text-left">
        <div class="flex flex-wrap justify-between items-end mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-main">Featured Providers</h2>
                <p class="text-muted">Top-rated and recently verified specialists in your area.</p>
            </div>
            <a href="/servants" class="btn btn-sm btn-outline">View Directory</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($featuredProviders as $profile): ?>
                <div class="card p-6 flex flex-col h-full">
                    <div class="flex items-center gap-4 mb-4">
                        <?php if (!empty($profile['profile_photo'])): ?>
                            <img src="<?= escape($profile['profile_photo']); ?>" class="avatar avatar-lg rounded-lg">
                        <?php else: ?>
                            <div class="avatar avatar-lg rounded-lg">
                                <?= mb_substr(escape($profile['full_name'] ?? 'P'), 0, 1); ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h4 class="font-bold text-lg m-0"><?= escape($profile['full_name'] ?? 'Provider'); ?></h4>
                            <div class="flex items-center gap-1 text-xs text-muted mt-1">
                                <span class="material-symbols-outlined" style="font-size: 14px;">location_on</span>
                                <?= escape($profile['location'] ?? 'Unknown'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-2 mb-4">
                            <?php foreach (array_slice((array)($profile['skills'] ?? []), 0, 2) as $skill): ?>
                                <span class="badge badge-primary"><?= escape($skill); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-sm text-muted italic">
                             "<?= escape($profile['experience'] ?? 'Expert provider'); ?>"
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t flex justify-between items-center">
                        <span class="text-primary font-bold"><?= escape($profile['rate'] ?? '0'); ?> <span class="text-xs font-normal text-muted">/hr</span></span>
                        <a href="/job/book?provider_id=<?= escape((string)$profile['user_id']); ?>" class="text-xs font-bold uppercase text-primary hover:underline">Book Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16 pt-12 border-t">
        <div class="flex flex-col items-center">
            <span class="material-symbols-outlined text-primary text-3xl mb-2">verified</span>
            <h3 class="font-bold">Verified Providers</h3>
            <p class="text-sm text-muted">Rigorous vetting process for your peace of mind.</p>
        </div>
        <div class="flex flex-col items-center">
            <span class="material-symbols-outlined text-primary text-3xl mb-2">payments</span>
            <h3 class="font-bold">Secure Payments</h3>
            <p class="text-sm text-muted">Pay only when you are satisfied with the work.</p>
        </div>
        <div class="flex flex-col items-center">
            <span class="material-symbols-outlined text-primary text-3xl mb-2">chat_bubble_outline</span>
            <h3 class="font-bold">Direct Messaging</h3>
            <p class="text-sm text-muted">Communicate clearly before hiring.</p>
        </div>
    </div>
</div>
