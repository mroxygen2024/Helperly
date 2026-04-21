<section class="page-header">
    <div>
        <h1>Incoming Hire Requests</h1>
        <p class="muted">Review employer requests and accept or reject each one.</p>
    </div>
</section>

<section class="grid">
    <?php if (empty($incomingRequests)): ?>
        <article class="card empty-state">
            <h2>No requests yet</h2>
            <p class="muted">New requests from employers will appear here.</p>
        </article>
    <?php else: ?>
        <?php foreach ($incomingRequests as $item): ?>
            <?php
            $request = $item['request'] ?? [];
            $employer = $item['employer'] ?? [];
            $status = (string) ($request['status'] ?? 'pending');
            ?>
            <article class="card servant-card">
                <header>
                    <div>
                        <h2><?= escape((string) ($employer['name'] ?? 'Unknown employer')); ?></h2>
                        <p class="muted"><?= escape((string) ($employer['phone'] ?? 'Phone not provided')); ?></p>
                    </div>
                    <div class="pill-row">
                        <span class="pill">Status: <?= escape($status); ?></span>
                    </div>
                </header>

                <p class="muted">Requested at <?= escape((string) ($item['created_at_text'] ?? 'Unknown')); ?></p>

                <?php if ($status === 'pending'): ?>
                    <div class="request-actions">
                        <form action="/servant/requests/status" method="POST" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                            <input type="hidden" name="request_id" value="<?= escape((string) ($request['_id'] ?? '')); ?>">
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn">Accept</button>
                        </form>

                        <form action="/servant/requests/status" method="POST" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">
                            <input type="hidden" name="request_id" value="<?= escape((string) ($request['_id'] ?? '')); ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-secondary">Reject</button>
                        </form>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
