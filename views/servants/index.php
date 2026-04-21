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
            <label for="location">Location</label>
            <input id="location" name="location" type="text" value="<?= escape((string) ($filters['location'] ?? '')); ?>" placeholder="e.g. Dhaka">
        </div>

        <div>
            <label for="skill">Skill</label>
            <input id="skill" name="skill" type="text" value="<?= escape((string) ($filters['skill'] ?? '')); ?>" placeholder="e.g. cleaning">
        </div>

        <div>
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
                    </div>
                </header>

                <div class="servant-meta">
                    <p><strong>Experience:</strong> <?= escape((string) ($profile['experience'] ?? 'Not provided')); ?></p>
                    <p><strong>Availability:</strong> <?= escape((string) ($profile['availability'] ?? 'Not provided')); ?></p>
                </div>

                <?php $skills = $profile['skills'] ?? []; ?>
                <div class="pill-row">
                    <?php if (is_iterable($skills)): ?>
                        <?php foreach ($skills as $skill): ?>
                            <span class="pill"><?= escape((string) $skill); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <form action="/hire-requests" method="POST" class="hire-form">
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                    <input type="hidden" name="servant_id" value="<?= escape((string) ($profile['user_id'] ?? '')); ?>">
                    <button type="submit" class="btn">Hire</button>
                </form>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>