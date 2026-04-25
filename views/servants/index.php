<section class="page-header">
    <div>
        <h1>Servant Directory</h1>
        <p class="muted">Browse servant profiles and narrow the list by location or skill.</p>
    </div>
    <div class="muted">
        Showing up to 50 profiles
    </div>
</section>

<section class="card stack">
    <form action="/servants" method="GET" class="form-grid filters">
        <div>
            <label for="name">Name</label>
            <input id="name" name="name" type="text" value="<?= escape((string) ($filters['name'] ?? '')); ?>" placeholder="e.g. John">
        </div>
        
        <div>
            <label for="location">Location</label>
            <input id="location" name="location" type="text" value="<?= escape((string) ($filters['location'] ?? '')); ?>" placeholder="e.g. Dhaka">
        </div>

        <div>
            <label for="skill">Skill</label>
            <input id="skill" name="skill" type="text" value="<?= escape((string) ($filters['skill'] ?? '')); ?>" placeholder="e.g. cleaning">
        </div>

        <div>
            <label for="experience">Experience</label>
            <input id="experience" name="experience" type="text" value="<?= escape((string) ($filters['experience'] ?? '')); ?>" placeholder="e.g. 2 years">
        </div>

        <div>
            <label for="availability">Availability</label>
            <input id="availability" name="availability" type="text" value="<?= escape((string) ($filters['availability'] ?? '')); ?>" placeholder="e.g. full-time">
        </div>

        <div>
            <label for="service_type">Service Type</label>
            <input id="service_type" name="service_type" type="text" value="<?= escape((string) ($filters['service_type'] ?? '')); ?>" placeholder="e.g. Maid">
        </div>

        <div>
            <label for="min_price">Min Hourly Rate</label>
            <input id="min_price" name="min_price" type="number" step="0.01" value="<?= escape((string) ($filters['min_price'] ?? '')); ?>" placeholder="Min">
        </div>

        <div>
            <label for="max_price">Max Hourly Rate</label>
            <input id="max_price" name="max_price" type="number" step="0.01" value="<?= escape((string) ($filters['max_price'] ?? '')); ?>" placeholder="Max">
        </div>

        <div>
            <label for="rating">Min Rating</label>
            <input id="rating" name="rating" type="number" step="0.1" max="5" value="<?= escape((string) ($filters['rating'] ?? '')); ?>" placeholder="e.g. 4.0">
        </div>

        <div style="grid-column: 1 / -1;">
            <button type="submit" class="btn">Apply filters</button>
        </div>
    </form>
</section>

<section class="grid">
    <?php if (empty($servants)): ?>
        <article class="card empty-state">
            <h2>No servants found</h2>
            <p class="muted">Try changing the filters or clear them to see the full list.</p>
        </article>
    <?php else: ?>
        <?php foreach ($servants as $servant): ?>
            <?php $profile = $servant['profile'] ?? []; ?>
            <article class="card servant-card">
                <header>
                    <div>
                        <h2><?= escape((string) ($servant['name'] ?? 'Unnamed servant')); ?></h2>
                        <p class="muted"><?= escape((string) ($servant['phone'] ?? 'Not provided')); ?></p>
                    </div>
                    <div class="pill-row">
                        <span class="pill"><?= escape((string) ($profile['location'] ?? 'Unknown location')); ?></span>
                        <?php if (isset($profile['rating']) && (float)$profile['rating'] > 0): ?>
                            <span class="pill" style="background: #fff9e6; color: #f39c12; border-color: #f39c12;">
                                <?= number_format((float)$profile['rating'], 1); ?> ★ (<?= (int)($profile['rating_count'] ?? 0); ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="servant-meta">
                    <p><strong>Gender:</strong> <?= escape((string) ($profile['gender'] ?? 'Not provided')); ?></p>
                    <p><strong>Experience:</strong> <?= escape((string) ($profile['experience'] ?? 'Not provided')); ?></p>
                    <p><strong>Availability:</strong> <?= escape((string) ($profile['availability'] ?? 'Not provided')); ?></p>
                    <p><strong>Hourly Rate:</strong> <?= escape((string) ($profile['hourly_rate'] ?? 'Not provided')); ?></p>
                </div>

                <?php if (!empty($profile['profile_photo'])): ?>
                    <div>
                        <img src="<?= escape((string) $profile['profile_photo']); ?>" alt="<?= escape((string) ($servant['name'] ?? 'Servant')); ?>" style="width:100%;max-width:220px;border-radius:12px;border:1px solid #ead9c0;">
                    </div>
                <?php endif; ?>

                <?php $skills = $profile['skills'] ?? []; ?>
                <div class="pill-row">
                    <?php if (is_iterable($skills)): ?>
                        <?php foreach ($skills as $skill): ?>
                            <span class="pill"><?= escape((string) $skill); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 1rem;">
                    <a href="/job/book?provider_id=<?= escape((string) ($profile['user_id'] ?? '')); ?>" class="btn">Book Directly</a>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>