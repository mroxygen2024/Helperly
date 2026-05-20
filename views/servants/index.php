<!-- Header Section -->
<div class="marketplace-header mb-12">
    <div class="marketplace-header-content">
        <h1 class="marketplace-title">Find Your Perfect Helper</h1>
        <p class="marketplace-subtitle">Discover verified service providers in your area</p>
        <div class="provider-badge-container">
            <span class="provider-count-badge"><?= count($servants); ?> Verified Providers</span>
        </div>
    </div>
</div>

<!-- Main Marketplace Container with Sidebar & Content -->
<div class="marketplace-container">
    <!-- Filter Sidebar -->
    <aside class="marketplace-sidebar">
        <div class="sidebar-header-compact">
            <h3 class="sidebar-title">Filters</h3>
            <button type="button" class="filter-close-btn" id="filterCloseBtn" aria-label="Close filters">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="/servants" method="GET" class="filter-form" id="filterForm">
            <!-- Search Filter -->
            <div class="filter-group">
                <label class="filter-label">Search Provider</label>
                <div class="filter-input-wrapper">
                    <span class="filter-input-icon material-symbols-outlined">search</span>
                    <input name="name" type="text" class="filter-input" 
                           value="<?= escape((string) ($filters['name'] ?? '')); ?>" 
                           placeholder="By name">
                </div>
            </div>

            <!-- Location Filter -->
            <div class="filter-group">
                <label class="filter-label">Location</label>
                <div class="filter-input-wrapper">
                    <span class="filter-input-icon material-symbols-outlined">location_on</span>
                    <input name="location" type="text" class="filter-input" 
                           value="<?= escape((string) ($filters['location'] ?? '')); ?>" 
                           placeholder="Any location">
                </div>
            </div>

            <!-- Skill Filter -->
            <div class="filter-group">
                <label class="filter-label">Skill or Service</label>
                <div class="filter-input-wrapper">
                    <span class="filter-input-icon material-symbols-outlined">construction</span>
                    <input name="skill" type="text" class="filter-input" 
                           value="<?= escape((string) ($filters['skill'] ?? '')); ?>" 
                           placeholder="What skill?">
                </div>
            </div>

            <!-- Rating Filter -->
            <div class="filter-group">
                <label class="filter-label">Minimum Rating</label>
                <select name="rating" class="filter-select">
                    <option value="">Any Rating</option>
                    <option value="4.5" <?= ($filters['rating'] ?? '') == '4.5' ? 'selected' : ''; ?>>4.5+ ★ Superior</option>
                    <option value="4.0" <?= ($filters['rating'] ?? '') == '4.0' ? 'selected' : ''; ?>>4.0+ ★ Great</option>
                    <option value="3.0" <?= ($filters['rating'] ?? '') == '3.0' ? 'selected' : ''; ?>>3.0+ ★ Good</option>
                </select>
            </div>

            <!-- Price Filter -->
            <div class="filter-group">
                <label class="filter-label">Max Hourly Rate (ETB)</label>
                <div class="filter-input-wrapper">
                    <span class="filter-input-icon material-symbols-outlined">payments</span>
                    <input name="max_price" type="number" class="filter-input" 
                           value="<?= escape((string) ($filters['max_price'] ?? '')); ?>" 
                           placeholder="No limit">
                </div>
            </div>

            <!-- Experience Filter -->
            <div class="filter-group">
                <label class="filter-label">Experience</label>
                <select name="experience" class="filter-select">
                    <option value="">Any Experience</option>
                    <option value="1+ years" <?= ($filters['experience'] ?? '') == '1+ years' ? 'selected' : ''; ?>>1+ years</option>
                    <option value="3+ years" <?= ($filters['experience'] ?? '') == '3+ years' ? 'selected' : ''; ?>>3+ years</option>
                    <option value="5+ years" <?= ($filters['experience'] ?? '') == '5+ years' ? 'selected' : ''; ?>>5+ years</option>
                </select>
            </div>

            <!-- Availability Filter -->
            <div class="filter-group">
                <label class="filter-label">Availability</label>
                <select name="availability" class="filter-select">
                    <option value="">Any Availability</option>
                    <option value="Full-time" <?= ($filters['availability'] ?? '') == 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                    <option value="Part-time" <?= ($filters['availability'] ?? '') == 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                    <option value="Weekend" <?= ($filters['availability'] ?? '') == 'Weekend' ? 'selected' : ''; ?>>Weekends Only</option>
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary filter-apply-btn">
                    <span class="material-symbols-outlined">filter_alt</span>
                    Apply Filters
                </button>
                <a href="/servants" class="btn btn-outline filter-clear-btn">
                    Clear All
                </a>
            </div>
        </form>
    </aside>

    <!-- Filter Toggle for Mobile -->
    <button type="button" class="filter-toggle-btn" id="filterToggleBtn" aria-label="Open filters">
        <span class="material-symbols-outlined">tune</span>
        <span>Filters</span>
    </button>

    <!-- Main Content Area -->
    <div class="marketplace-content">
        <div class="providers-grid">

            <!-- Empty State -->
            <?php if (empty($servants)): ?>
                <div class="providers-empty-state">
                    <span class="material-symbols-outlined">search_off</span>
                    <h3>No providers found</h3>
                    <p>Try adjusting your filters to find more providers</p>
                </div>
            <?php else: ?>
                <!-- Provider Cards Grid -->
                <?php foreach ($servants as $servant): ?>
                    <?php $profile = $servant['profile'] ?? []; ?>
                    <div class="provider-card">
                        <!-- Card Header with Image -->
                        <div class="provider-card-image-wrapper">
                            <?php if (!empty($profile['profile_photo'])): ?>
                                <img src="<?= escape((string) $profile['profile_photo']); ?>" 
                                     alt="<?= escape((string) ($servant['name'] ?? 'Servant')); ?>" 
                                     class="provider-card-image">
                            <?php else: ?>
                                <div class="provider-card-placeholder">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Verification Badge -->
                            <div class="provider-verification-badge">
                                <span class="material-symbols-outlined">verified</span>
                                <span>Verified</span>
                            </div>

                            <!-- Rating Overlay -->
                            <div class="provider-rating-overlay">
                                <span class="material-symbols-outlined provider-star">star</span>
                                <span class="provider-rating-value"><?= number_format((float)($profile['rating'] ?? 0), 1); ?></span>
                                <span class="provider-rating-count">(<?= (int)($profile['rating_count'] ?? 0); ?>)</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="provider-card-body">
                            <!-- Header with Name and Rate -->
                            <div class="provider-header">
                                <div class="provider-name-section">
                                    <h3 class="provider-name"><?= escape((string) ($servant['name'] ?? 'Unnamed')); ?></h3>
                                    <div class="provider-location">
                                        <span class="material-symbols-outlined">location_on</span>
                                        <span><?= escape((string) ($profile['location'] ?? 'Unknown')); ?></span>
                                    </div>
                                </div>
                                <div class="provider-rate-section">
                                    <p class="provider-rate"><?= escape((string) ($profile['rate'] ?? '0')); ?></p>
                                    <p class="provider-rate-unit">ETB/hr</p>
                                </div>
                            </div>

                            <!-- Experience -->
                            <div class="provider-experience">
                                <span class="experience-label">Experience</span>
                                <span class="experience-value"><?= escape((string) ($profile['experience'] ?? 'N/A')); ?></span>
                            </div>

                            <!-- Skills Tags -->
                            <div class="provider-skills">
                                <?php $skills = $profile['skills'] ?? []; ?>
                                <?php if (is_iterable($skills)): ?>
                                    <?php foreach (array_slice((array)$skills, 0, 4) as $skill): ?>
                                        <span class="skill-badge"><?= escape((string) $skill); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Stats Section -->
                            <div class="provider-stats">
                                <div class="stat-item">
                                    <span class="stat-icon material-symbols-outlined">schedule</span>
                                    <div class="stat-content">
                                        <span class="stat-label">Response</span>
                                        <span class="stat-value"><?= escape($profile['response_time'] ?? 'Under 1h'); ?></span>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-icon material-symbols-outlined">people</span>
                                    <div class="stat-content">
                                        <span class="stat-label">Repeats</span>
                                        <span class="stat-value"><?= (int)($profile['repeat_clients'] ?? 0); ?></span>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-icon material-symbols-outlined">check_circle</span>
                                    <div class="stat-content">
                                        <span class="stat-label">Success</span>
                                        <span class="stat-value-success"><?= (int)($profile['completion_rate'] ?? 100); ?>%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="provider-actions">
                                <button type="button" class="btn btn-outline provider-btn-secondary" data-open-modal="profile_modal_<?= escape((string)$profile['user_id']); ?>">
                                    <span class="material-symbols-outlined">person_outline</span>
                                    View Profile
                                </button>
                                <a href="/job/book?provider_id=<?= escape((string) ($profile['user_id'] ?? '')); ?>" class="btn btn-primary provider-btn-primary">
                                    <span class="material-symbols-outlined">event_available</span>
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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