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
        <?php $isAdmin = normalizeRole((string) ($user['role'] ?? '')) === 'administrator'; ?>
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
                        <span class="pill"><?= escape(ServantProfile::verificationStatusLabel((string) ($profile['verification_status'] ?? 'pending'))); ?></span>
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

                <form action="/hire-requests" method="POST" class="hire-form">
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                    <input type="hidden" name="servant_id" value="<?= escape((string) ($profile['user_id'] ?? '')); ?>">
                    <button type="submit" class="btn">Hire</button>
                </form>

                <?php if ($isAdmin): ?>
                    <form action="/admin/servant-verification" method="POST" class="form-grid" style="margin-top:1rem;">
                        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                        <input type="hidden" name="servant_user_id" value="<?= escape((string) ($profile['user_id'] ?? '')); ?>">

                        <label for="verification_status_<?= escape((string) ($profile['user_id'] ?? '')); ?>">Verification status</label>
                        <select id="verification_status_<?= escape((string) ($profile['user_id'] ?? '')); ?>" name="verification_status">
                            <option value="pending" <?= (string) ($profile['verification_status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?= (string) ($profile['verification_status'] ?? '') === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?= (string) ($profile['verification_status'] ?? '') === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>

                        <label for="verification_notes_<?= escape((string) ($profile['user_id'] ?? '')); ?>">Verification notes</label>
                        <textarea id="verification_notes_<?= escape((string) ($profile['user_id'] ?? '')); ?>" name="verification_notes" rows="3" placeholder="Optional review notes"><?= escape((string) ($profile['verification_notes'] ?? '')); ?></textarea>

                        <button type="submit" class="btn btn-secondary">Update verification</button>
                    </form>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>