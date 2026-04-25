<section class="page-header">
    <div>
        <h1><?= escape($title ?? 'Hire Requests'); ?></h1>
        <p class="muted">
            <?php if (($role ?? '') === 'servant'): ?>
                Review employer requests and accept or reject each one.
            <?php else: ?>
                Track the status of invitations you've sent to service providers.
            <?php endif; ?>
        </p>
    </div>
</section>

<section class="grid">
    <?php if (empty($requests)): ?>
        <article class="card empty-state">
            <h2>No requests yet</h2>
            <p class="muted">
                <?php if (($role ?? '') === 'servant'): ?>
                    New requests from employers will appear here.
                <?php else: ?>
                    You haven't sent any invitations yet. Browse the directory to find help.
                <?php endif; ?>
            </p>
        </article>
    <?php else: ?>
        <?php foreach ($requests as $item): ?>
            <?php
            $request = $item['request'] ?? [];
            $other = $item['other'] ?? [];
            $status = (string) ($request['status'] ?? 'pending');
            $createdAt = $request['created_at'] ?? null;
            $createdAtText = 'Unknown';
            if ($createdAt instanceof \MongoDB\BSON\UTCDateTime) {
                $createdAtText = $createdAt->toDateTime()->format('Y-m-d H:i');
            }
            ?>
            <article class="card servant-card">
                <header>
                    <div>
                        <h2><?= escape((string) ($other['name'] ?? 'Unknown user')); ?></h2>
                        <p class="muted"><?= escape((string) ($other['phone'] ?? 'Phone not provided')); ?></p>
                    </div>
                    <div>
                        <span class="badge" style="background: <?= $status === 'accepted' ? '#def7ec' : ($status === 'rejected' ? '#fde8e8' : '#feecdc'); ?>; color: <?= $status === 'accepted' ? '#03543f' : ($status === 'rejected' ? '#981b1b' : '#9a3412'); ?>;">
                            <?= ucfirst(escape($status)); ?>
                        </span>
                    </div>
                </header>

                <p class="muted small">Request date: <?= escape($createdAtText); ?></p>

                <?php if (($role ?? '') === 'servant' && $status === 'pending'): ?>
                    <div class="flex-row gap-small" style="margin-top: 1rem;">
                        <form action="/servant/requests/status" method="POST" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                            <input type="hidden" name="request_id" value="<?= escape((string) ($request['_id'] ?? '')); ?>">
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn btn-small">Accept</button>
                        </form>

                        <form action="/servant/requests/status" method="POST" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                            <input type="hidden" name="request_id" value="<?= escape((string) ($request['_id'] ?? '')); ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-small btn-outline">Reject</button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if ($status === 'accepted'): ?>
                    <p class="success small" style="margin-top: 1rem;">Invitation confirmed. You can now coordinate further.</p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
