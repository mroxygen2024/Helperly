<div class="card mb-8">
    <div class="card-header" style="border-bottom: 2px solid var(--border-light); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
        <div class="flex justify-between items-end">
            <div>
                <h2 class="card-title">Find Help</h2>
                <p class="text-sm text-muted">Filter through our verified service providers</p>
            </div>
            <div class="badge badge-info"><?= count($servants); ?> Providers Available</div>
        </div>
    </div>
    
    <form action="/servants" method="GET" class="p-2">
        <div class="grid grid-cols-4 gap-6">
            <div class="form-group">
                <label for="filter_name" class="label">Name</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="filter_name" name="name" type="text" class="input-field" 
                           value="<?= escape((string) ($filters['name'] ?? '')); ?>" 
                           placeholder="Search by name..." style="padding-left: 2.75rem;">
                    <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        search
                    </span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="filter_location" class="label">Location</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="filter_location" name="location" type="text" class="input-field" 
                           value="<?= escape((string) ($filters['location'] ?? '')); ?>" 
                           placeholder="e.g. Dhaka" style="padding-left: 2.75rem;">
                    <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        location_on
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="filter_skill" class="label">Skills</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="filter_skill" name="skill" type="text" class="input-field" 
                           value="<?= escape((string) ($filters['skill'] ?? '')); ?>" 
                           placeholder="e.g. Cleaning, Cooking" style="padding-left: 2.75rem;">
                    <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        construction
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="filter_rating" class="label">Minimum Rating</label>
                <div class="input-wrapper" style="position: relative;">
                    <select id="filter_rating" name="rating" class="select" style="padding-left: 2.75rem;">
                        <option value="">Any Rating</option>
                        <option value="4.5" <?= ($filters['rating'] ?? '') == '4.5' ? 'selected' : ''; ?>>4.5+ ★ Superior</option>
                        <option value="4.0" <?= ($filters['rating'] ?? '') == '4.0' ? 'selected' : ''; ?>>4.0+ ★ Great</option>
                        <option value="3.0" <?= ($filters['rating'] ?? '') == '3.0' ? 'selected' : ''; ?>>3.0+ ★ Good</option>
                    </select>
                    <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        star
                    </span>
                </div>
            </div>
        </div>

        <!-- Second Row of Filters -->
        <div class="grid grid-cols-4 gap-6 mt-4">
            <div class="form-group">
                <label for="filter_service" class="label">Service Category</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="filter_service" name="service_type" type="text" class="input-field" 
                           value="<?= escape((string) ($filters['service_type'] ?? '')); ?>" 
                           placeholder="e.g. Home Care" style="padding-left: 2.75rem;">
                    <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        category
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="filter_min_price" class="label">Min Hourly Rate</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="filter_min_price" name="min_price" type="number" class="input-field" 
                           value="<?= escape((string) ($filters['min_price'] ?? '')); ?>" 
                           placeholder="0" min="0" style="padding-left: 2.75rem;">
                    <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        payments
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="filter_max_price" class="label">Max Hourly Rate</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="filter_max_price" name="max_price" type="number" class="input-field" 
                           value="<?= escape((string) ($filters['max_price'] ?? '')); ?>" 
                           placeholder="Unbound" min="0" style="padding-left: 2.75rem;">
                    <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        toll
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="filter_experience" class="label">Min Experience</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="filter_experience" name="experience" type="text" class="input-field" 
                           value="<?= escape((string) ($filters['experience'] ?? '')); ?>" 
                           placeholder="e.g. 5+ years" style="padding-left: 2.75rem;">
                    <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        history
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="filter_availability" class="label">Availability</label>
                <div class="input-wrapper" style="position: relative;">
                    <input id="filter_availability" name="availability" type="text" class="input-field" 
                           value="<?= escape((string) ($filters['availability'] ?? '')); ?>" 
                           placeholder="e.g. Full-time" style="padding-left: 2.75rem;">
                    <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;">
                        event_available
                    </span>
                </div>
            </div>
        </div>


        <div class="flex justify-end gap-3 mt-4 pt-4" style="border-top: 1px solid var(--border-light);">
            <a href="/servants" class="btn btn-outline" style="min-width: 120px;">
                <span class="material-symbols-outlined">restart_alt</span>
                Reset
            </a>
            <button type="submit" class="btn btn-primary" style="min-width: 200px;">
                <span class="material-symbols-outlined">filter_alt</span>
                Apply Filters
            </button>
        </div>
    </form>
</div>


<div class="grid grid-cols-2 gap-6">
    <?php if (empty($servants)): ?>
        <div class="col-span-2 card text-center py-12">
            <span class="material-symbols-outlined text-muted" style="font-size: 3rem;">search_off</span>
            <h2 class="card-title mt-4">No match found</h2>
            <p class="text-muted">Try adjusting your filters to find more providers.</p>
        </div>
    <?php else: ?>
        <?php foreach ($servants as $servant): ?>
            <?php $profile = $servant['profile'] ?? []; ?>
            <div class="card flex gap-6 items-start">
                <div style="flex-shrink: 0;">
                    <?php if (!empty($profile['profile_photo'])): ?>
                        <img src="<?= escape((string) $profile['profile_photo']); ?>" alt="<?= escape((string) ($servant['name'] ?? 'Servant')); ?>" style="width:120px; height:120px; object-fit: cover; border-radius: 18px; border: 2px solid var(--border-base);">
                    <?php else: ?>
                        <div class="user-avatar-rect" style="width:120px; height:120px; border-radius: 18px; font-size: 3rem; background: var(--grad-primary);">
                            <?= mb_substr(escape($servant['name'] ?? 'U'), 0, 1); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="card-title" style="margin-bottom: 0.25rem;"><?= escape((string) ($servant['name'] ?? 'Unnamed servant')); ?></h3>
                            <p class="text-sm text-muted mb-2 flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                                <?= escape((string) ($profile['location'] ?? 'Unknown')); ?>
                            </p>
                        </div>
                        <?php if (isset($profile['rating']) && (float)$profile['rating'] > 0): ?>
                            <div class="flex items-center gap-1 bg-warning-soft" style="background: #fffbeb; padding: 0.35rem 0.65rem; border-radius: 8px; border: 1px solid #FEF3C7;">
                                <span class="text-warning" style="font-weight: 800; font-size: 0.95rem;"><?= number_format((float)$profile['rating'], 1); ?></span>
                                <span class="material-symbols-outlined text-warning" style="font-size: 18px; font-variation-settings: 'FILL' 1;">star</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm mt-3 mb-4">
                        <p><span class="text-muted">Exp:</span> <strong><?= escape((string) ($profile['experience'] ?? 'N/A')); ?></strong></p>
                        <p><span class="text-muted">Rate:</span> <strong class="text-primary"><?= escape((string) ($profile['rate'] ?? 'N/A')); ?> BDT/hr</strong></p>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-6">
                        <?php $skills = $profile['skills'] ?? []; ?>
                        <?php if (is_iterable($skills)): ?>
                            <?php foreach (array_slice((array)$skills, 0, 3) as $skill): ?>
                                <span class="badge badge-secondary"><?= escape((string) $skill); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" class="btn btn-outline btn-sm w-1/3" data-open-modal="profile_modal_<?= escape((string)$profile['user_id']); ?>">
                           Preview
                        </button>
                        <a href="/job/book?provider_id=<?= escape((string) ($profile['user_id'] ?? '')); ?>" class="btn btn-primary btn-sm w-2/3">
                            <span class="material-symbols-outlined" style="font-size: 18px;">calendar_today</span>
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
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
                         <span class="font-700 text-sm"><?= escape($profile['rate'] ?? 'N/A'); ?> BDT</span>
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