<div class="card mb-8 border-none shadow-sm overflow-hidden" style="background: white;">
    <div class="p-6 border-b flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-800 text-slate-900">Find Your Perfect Helper</h2>
            <p class="text-sm text-muted font-500">Filter through our community of <?= count($servants); ?> verified providers</p>
        </div>
        <div class="flex gap-2">
            <button type="button" class="btn btn-ghost btn-sm" onclick="location.href='/servants'">Clear All</button>
        </div>
    </div>
    
    <form action="/servants" method="GET" class="p-6 bg-slate-50/50">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="form-group mb-0">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size: 20px;">search</span>
                    <input name="name" type="text" class="input-field pl-10 h-11 text-sm bg-white" 
                           value="<?= escape((string) ($filters['name'] ?? '')); ?>" 
                           placeholder="Search by name...">
                </div>
            </div>
            
            <div class="form-group mb-0">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size: 20px;">location_on</span>
                    <input name="location" type="text" class="input-field pl-10 h-11 text-sm bg-white" 
                           value="<?= escape((string) ($filters['location'] ?? '')); ?>" 
                           placeholder="Any Location">
                </div>
            </div>

            <div class="form-group mb-0">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size: 20px;">construction</span>
                    <input name="skill" type="text" class="input-field pl-10 h-11 text-sm bg-white" 
                           value="<?= escape((string) ($filters['skill'] ?? '')); ?>" 
                           placeholder="Any Skill">
                </div>
            </div>

            <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary w-full h-11 font-800 gap-2">
                    <span class="material-symbols-outlined" style="font-size: 20px;">filter_alt</span>
                    Apply Filters
                </button>
            </div>
        </div>

        <!-- Advanced Filters (Collapsible in a real app, here always shown for simplicity) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            <div class="form-group mb-0">
                <select name="rating" class="input-field h-11 text-sm bg-white">
                    <option value="">Any Rating</option>
                    <option value="4.5" <?= ($filters['rating'] ?? '') == '4.5' ? 'selected' : ''; ?>>4.5+ ★ Superior</option>
                    <option value="4.0" <?= ($filters['rating'] ?? '') == '4.0' ? 'selected' : ''; ?>>4.0+ ★ Great</option>
                    <option value="3.0" <?= ($filters['rating'] ?? '') == '3.0' ? 'selected' : ''; ?>>3.0+ ★ Good</option>
                </select>
            </div>

            <div class="form-group mb-0">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size: 20px;">payments</span>
                    <input name="max_price" type="number" class="input-field pl-10 h-11 text-sm bg-white" 
                           value="<?= escape((string) ($filters['max_price'] ?? '')); ?>" 
                           placeholder="Max Rate/Hr">
                </div>
            </div>

            <div class="form-group mb-0">
                <select name="experience" class="input-field h-11 text-sm bg-white">
                    <option value="">Any Experience</option>
                    <option value="1+ years" <?= ($filters['experience'] ?? '') == '1+ years' ? 'selected' : ''; ?>>1+ years</option>
                    <option value="3+ years" <?= ($filters['experience'] ?? '') == '3+ years' ? 'selected' : ''; ?>>3+ years</option>
                    <option value="5+ years" <?= ($filters['experience'] ?? '') == '5+ years' ? 'selected' : ''; ?>>5+ years</option>
                </select>
            </div>

            <div class="form-group mb-0">
                <select name="availability" class="input-field h-11 text-sm bg-white">
                    <option value="">Any Availability</option>
                    <option value="Full-time" <?= ($filters['availability'] ?? '') == 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                    <option value="Part-time" <?= ($filters['availability'] ?? '') == 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                    <option value="Weekend" <?= ($filters['availability'] ?? '') == 'Weekend' ? 'selected' : ''; ?>>Weekends Only</option>
                </select>
            </div>
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
            <div class="card p-0 overflow-hidden flex flex-col hover:shadow-xl transition-all border-none shadow-md group">
                <div class="relative h-48">
                    <?php if (!empty($profile['profile_photo'])): ?>
                        <img src="<?= escape((string) $profile['profile_photo']); ?>" alt="<?= escape((string) ($servant['name'] ?? 'Servant')); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-slate-200 text-slate-400 group-hover:scale-105 transition-transform duration-500">
                            <span class="material-symbols-outlined" style="font-size: 5rem;">person</span>
                        </div>
                    <?php endif; ?>
                    <div class="absolute top-4 right-4">
                        <span class="badge badge-success shadow-lg border-2 border-white flex items-center gap-1">
                            <span class="material-symbols-outlined" style="font-size: 14px;">verified</span> Verified
                        </span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent">
                        <div class="flex items-center gap-2 text-white">
                            <span class="material-symbols-outlined" style="font-size: 18px; color: #FBBF24; font-variation-settings: 'FILL' 1;">star</span>
                            <span class="font-800"><?= number_format((float)($profile['rating'] ?? 0), 1); ?></span>
                            <span class="text-xs opacity-80">(<?= (int)($profile['rating_count'] ?? 0); ?> reviews)</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-800 text-slate-900 leading-tight group-hover:text-primary transition-colors"><?= escape((string) ($servant['name'] ?? 'Unnamed servant')); ?></h3>
                            <p class="text-sm text-muted flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                                <?= escape((string) ($profile['location'] ?? 'Unknown')); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-900 text-primary mb-0"><?= escape((string) ($profile['rate'] ?? '0')); ?> <span class="text-[10px] font-600 uppercase">BDT/hr</span></p>
                            <p class="text-[10px] text-muted font-700 uppercase tracking-widest"><?= escape((string) ($profile['experience'] ?? 'N/A')); ?></p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-6">
                        <?php $skills = $profile['skills'] ?? []; ?>
                        <?php if (is_iterable($skills)): ?>
                            <?php foreach (array_slice((array)$skills, 0, 3) as $skill): ?>
                                <span class="badge badge-secondary text-[10px] font-700 uppercase tracking-wider bg-slate-100 text-slate-600 border-none"><?= escape((string) $skill); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Trust Signals Bar -->
                    <div class="grid grid-cols-3 gap-2 py-3 border-y mb-6 text-[10px] font-700 uppercase tracking-tight text-slate-500">
                        <div class="flex flex-col items-center gap-1 border-r">
                            <span class="text-slate-400">Response</span>
                            <span class="text-slate-800"><?= escape($profile['response_time'] ?? 'Under 1h'); ?></span>
                        </div>
                        <div class="flex flex-col items-center gap-1 border-r">
                            <span class="text-slate-400">Repeats</span>
                            <span class="text-slate-800"><?= (int)($profile['repeat_clients'] ?? 0); ?> Clients</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <span class="text-slate-400">Success</span>
                            <span class="text-success"><?= (int)($profile['completion_rate'] ?? 100); ?>%</span>
                        </div>
                    </div>

                    <div class="mt-auto flex gap-2">
                        <button type="button" class="btn btn-outline btn-sm flex-1 font-700" data-open-modal="profile_modal_<?= escape((string)$profile['user_id']); ?>">
                           View Profile
                        </button>
                        <a href="/job/book?provider_id=<?= escape((string) ($profile['user_id'] ?? '')); ?>" class="btn btn-primary btn-sm flex-1 font-800 gap-2">
                            <span class="material-symbols-outlined" style="font-size: 18px;">bolt</span> Book Now
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