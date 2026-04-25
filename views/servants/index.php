<div class="card mb-6">
    <div class="card-header">
        <h2 class="card-title">Filter Servants</h2>
        <p class="text-sm text-muted">Showing <?= count($servants); ?> results</p>
    </div>
    <form action="/servants" method="GET" class="grid grid-cols-4 gap-4">
        <div class="form-group">
            <label class="label">Name</label>
            <input name="name" type="text" class="input" value="<?= escape((string) ($filters['name'] ?? '')); ?>" placeholder="Search by name...">
        </div>
        
        <div class="form-group">
            <label class="label">Location</label>
            <input name="location" type="text" class="input" value="<?= escape((string) ($filters['location'] ?? '')); ?>" placeholder="e.g. Dhaka">
        </div>

        <div class="form-group">
            <label class="label">Service Type</label>
            <input name="service_type" type="text" class="input" value="<?= escape((string) ($filters['service_type'] ?? '')); ?>" placeholder="e.g. Maid">
        </div>

        <div class="form-group">
            <label class="label">Min Rating</label>
            <select name="rating" class="select">
                <option value="">Any Rating</option>
                <option value="4.5" <?= ($filters['rating'] ?? '') == '4.5' ? 'selected' : ''; ?>>4.5+ ★</option>
                <option value="4.0" <?= ($filters['rating'] ?? '') == '4.0' ? 'selected' : ''; ?>>4.0+ ★</option>
                <option value="3.0" <?= ($filters['rating'] ?? '') == '3.0' ? 'selected' : ''; ?>>3.0+ ★</option>
            </select>
        </div>

        <div class="form-group flex items-end" style="grid-column: span 4; justify-content: flex-end; margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">
                <span class="material-symbols-outlined">filter_list</span>
                Apply Search Filters
            </button>
            <a href="/servants" class="btn btn-outline ml-2" style="margin-left: 0.5rem;">Clear</a>
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
            <div class="card flex gap-6 items-start" style="margin-bottom: 0;">
                <div style="flex-shrink: 0;">
                    <?php if (!empty($profile['profile_photo'])): ?>
                        <img src="<?= escape((string) $profile['profile_photo']); ?>" alt="<?= escape((string) ($servant['name'] ?? 'Servant')); ?>" style="width:120px; height:120px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border);">
                    <?php else: ?>
                        <div class="user-avatar" style="width:120px; height:120px; border-radius: 12px; font-size: 3rem;">
                            <?= mb_substr(escape($servant['name']), 0, 1); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="card-title"><?= escape((string) ($servant['name'] ?? 'Unnamed servant')); ?></h2>
                            <p class="text-sm text-muted mb-2">
                                <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">location_on</span>
                                <?= escape((string) ($profile['location'] ?? 'Unknown')); ?>
                            </p>
                        </div>
                        <?php if (isset($profile['rating']) && (float)$profile['rating'] > 0): ?>
                            <div class="flex items-center gap-1 bg-warning" style="background: #fffbeb; padding: 0.25rem 0.5rem; border-radius: 6px;">
                                <span class="text-warning font-700" style="font-size: 0.875rem;"><?= number_format((float)$profile['rating'], 1); ?></span>
                                <span class="material-symbols-outlined text-warning" style="font-size: 16px;">star</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm mt-2 mb-4">
                        <p><span class="text-muted">Exp:</span> <strong><?= escape((string) ($profile['experience'] ?? 'N/A')); ?></strong></p>
                        <p><span class="text-muted">Rate:</span> <strong class="text-info"><?= escape((string) ($profile['hourly_rate'] ?? 'N/A')); ?> BDT/hr</strong></p>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php $skills = $profile['skills'] ?? []; ?>
                        <?php if (is_iterable($skills)): ?>
                            <?php foreach (array_slice((array)$skills, 0, 3) as $skill): ?>
                                <span class="badge badge-secondary"><?= escape((string) $skill); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="flex gap-2">
                        <a href="/job/book?provider_id=<?= escape((string) ($profile['user_id'] ?? '')); ?>" class="btn btn-primary btn-sm flex-1">
                            <span class="material-symbols-outlined" style="font-size: 18px;">calendar_today</span>
                            Book Now
                        </a>
                        <form action="/hire-requests" method="POST" class="flex-1">
                            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                            <input type="hidden" name="servant_id" value="<?= escape((string) ($profile['user_id'] ?? '')); ?>">
                            <button type="submit" class="btn btn-outline btn-sm w-full">
                                <span class="material-symbols-outlined" style="font-size: 18px;">mail</span>
                                Send Invitation
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>