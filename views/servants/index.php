<script src="https://cdn.tailwindcss.com"></script>

<header class="mb-10 px-4">
    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Find Your Perfect Helper</h1>
    <p class="mt-2 text-lg text-gray-600">Discover verified service providers in your area</p>
    <div class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-full text-sm font-bold">
        <?= count($servants); ?> Verified Providers
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8 px-4">
    <!-- Filter Sidebar -->
    <aside class="lg:col-span-1">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 sticky top-8">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Filters</h2>
            <form action="/servants" method="GET" class="space-y-6">
                <!-- Search Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search Provider</label>
                    <input name="name" type="text" class="w-full px-3 py-2 border rounded-lg text-sm" value="<?= escape((string) ($filters['name'] ?? '')); ?>" placeholder="By name">
                </div>

                <!-- Location Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                    <input name="location" type="text" class="w-full px-3 py-2 border rounded-lg text-sm" value="<?= escape((string) ($filters['location'] ?? '')); ?>" placeholder="Any location">
                </div>

                <!-- Skill Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Skill or Service</label>
                    <input name="skill" type="text" class="w-full px-3 py-2 border rounded-lg text-sm" value="<?= escape((string) ($filters['skill'] ?? '')); ?>" placeholder="What skill?">
                </div>
                
                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 rounded-lg text-sm hover:bg-indigo-700 transition">Apply Filters</button>
                <a href="/servants" class="block text-center text-sm text-gray-500 hover:underline">Clear All</a>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:col-span-3">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Empty State -->
            <?php if (empty($servants)): ?>
                <div class="providers-empty-state">
                    <span class="material-symbols-outlined">search_off</span>
                    <h3>No providers found</h3>
                    <p>Try adjusting your filters to find more providers</p>
                </div>
            <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($servants as $servant): ?>
                            <?php $profile = $servant['profile'] ?? []; ?>
                            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                                <div class="flex items-center gap-4 mb-4">
                                    <?php if (!empty($profile['profile_photo'])): ?>
                                        <img src="<?= escape((string) $profile['profile_photo']); ?>" class="w-16 h-16 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700"><?= mb_substr(escape($servant['name'] ?? 'P'), 0, 1); ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <h3 class="font-bold text-lg"><?= escape((string) ($servant['name'] ?? 'Unnamed')); ?></h3>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span class="material-symbols-outlined text-sm mr-1">location_on</span>
                                            <?= escape((string) ($profile['location'] ?? 'Unknown')); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4 text-sm text-gray-700">
                                    <span class="font-semibold">Rate:</span> <?= escape((string) ($profile['rate'] ?? '0')); ?> ETB/hr
                                </div>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <?php foreach (array_slice((array)($profile['skills'] ?? []), 0, 3) as $skill): ?>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold"><?= escape((string) $skill); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold hover:bg-gray-200 transition-all text-center" data-open-modal="profile_modal_<?= escape((string)($profile['user_id'] ?? '')); ?>">Profile</button>
                                    <a href="/job/book?provider_id=<?= escape((string) ($profile['user_id'] ?? '')); ?>" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition-all text-center">Book</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
            <?php endif; ?>
        </div>
    </div>
</div>



<!-- Servant Profile Modals -->
<?php foreach ($servants as $servant): ?>
    <?php $profile = $servant['profile'] ?? []; ?>
    <?php $uid = (string)($profile['user_id'] ?? ''); ?>
    <div id="profile_modal_<?= escape($uid); ?>" class="modal-overlay" data-modal>
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div class="flex items-center gap-4">
                    <div class="user-avatar-rect" style="width: 64px; height: 64px; background: var(--grad-primary);">
                        <?php if (!empty($profile['profile_photo'])): ?>
                            <img src="<?= escape($profile['profile_photo']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?= mb_substr(escape($servant['name'] ?? 'P'), 0, 1); ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 class="card-title" style="margin: 0;"><?= escape($servant['name'] ?? 'Provider'); ?></h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="badge badge-success">Verified</span>
                            <?php if (isset($profile['rating']) && (float)$profile['rating'] > 0): ?>
                            <div class="flex items-center gap-1 text-sm bg-warning-soft px-2 rounded">
                                <span class="material-symbols-outlined text-warning" style="font-size: 16px;">star</span>
                                <span class="font-600"><?= number_format((float)$profile['rating'], 1); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-sm" data-close-modal="profile_modal_<?= escape($uid); ?>" style="border:none; padding: 0.5rem; border-radius: 50%;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="modal-body" style="background: #F8FAFC; padding: 2rem;">
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <span class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-2 block">All Skills</span>
                        <div class="flex flex-wrap gap-2">
                            <?php if (!empty($profile['skills'])): ?>
                                <?php foreach ($profile['skills'] as $skill): ?>
                                    <span class="badge badge-secondary"><?= escape($skill); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-sm text-muted italic">No skills listed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-muted font-800 uppercase letter-spacing-lg mb-2 block">Experience</span>
                        <p class="text-sm font-600"><?= escape($profile['experience'] ?? 'N/A'); ?></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4 text-xs bg-white p-4 rounded-xl border border-slate-200 mb-6">
                     <div class="flex flex-col">
                         <span class="text-muted font-600 mb-1">Rate</span>
                         <span class="font-700 text-sm"><?= escape($profile['rate'] ?? 'N/A'); ?> ETB</span>
                     </div>
                     <div class="flex flex-col">
                         <span class="text-muted font-600 mb-1">Location</span>
                         <span class="font-700 text-sm"><?= escape($profile['location'] ?? 'N/A'); ?></span>
                     </div>
                     <div class="flex flex-col">
                         <span class="text-muted font-600 mb-1">Availability</span>
                         <span class="font-700 text-sm"><?= escape($profile['availability'] ?? 'N/A'); ?></span>
                     </div>
                </div>

                <div class="p-4 bg-primary-soft rounded-xl text-sm italic">
                    "<?= escape($servant['name'] ?? 'This provider'); ?> is one of our top-rated specialists in <?= escape($profile['location'] ?? 'your area'); ?>."
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.5rem 2.5rem; background: white; border-top: 1px solid var(--border-base);">
                <button type="button" class="btn btn-outline" data-close-modal="profile_modal_<?= escape($uid); ?>">Close</button>
                <a href="/job/book?provider_id=<?= escape($uid); ?>" class="btn btn-primary" style="padding-inline: 2rem;">
                    <span class="material-symbols-outlined">calendar_today</span> Book Now
                </a>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
(() => {
    // Modal handling
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

    // Filter sidebar toggle for mobile
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterCloseBtn = document.getElementById('filterCloseBtn');
    const marketplaceSidebar = document.querySelector('.marketplace-sidebar');

    if (filterToggleBtn) {
        filterToggleBtn.addEventListener('click', () => {
            marketplaceSidebar.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if (filterCloseBtn) {
        filterCloseBtn.addEventListener('click', () => {
            marketplaceSidebar.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    }

    // Close sidebar on filter apply
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', () => {
            if (window.innerWidth < 1024) {
                marketplaceSidebar.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 1024) {
            const isClickInsideSidebar = marketplaceSidebar.contains(e.target);
            const isClickOnToggle = filterToggleBtn && filterToggleBtn.contains(e.target);
            
            if (!isClickInsideSidebar && !isClickOnToggle && marketplaceSidebar.classList.contains('active')) {
                marketplaceSidebar.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }
    });
})();
</script>