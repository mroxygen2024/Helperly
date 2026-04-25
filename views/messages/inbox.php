<section>
    <h1>My Conversations</h1>
    <p class="muted">Recent messages from your jobs and service requests.</p>
</section>

<section class="stack">
    <?php if (empty($conversations)): ?>
        <article class="card empty-state">
            <h3>No conversations yet</h3>
            <p class="muted">Messages will appear here once you book a provider or start a job.</p>
            <a href="/?page=dashboard" class="btn btn-outline" style="margin-top: 1rem;">Go to Dashboard</a>
        </article>
    <?php else: ?>
        <div class="grid" style="--grid-cols: 1;">
            <?php foreach ($conversations as $conv): ?>
                <?php 
                    $latest = $conv['latest_message']; 
                    $job = $conv['job_info'];
                    $otherParty = $conv['other_party'];
                ?>
                <article class="card border flex-between" style="align-items: center;">
                    <div style="flex: 1;">
                        <div class="flex-row gap-small" style="align-items: baseline;">
                            <h3 style="margin: 0;"><?= escape((string) ($otherParty['name'] ?? 'Unknown User')); ?></h3>
                            <span class="muted small"><?= escape((string) ($job['service_type'] ?? 'Job')); ?></span>
                        </div>
                        <p style="margin: 0.5rem 0; color: #444;">
                            <?php if ((string) $latest['sender_id'] === $userId): ?>
                                <span class="muted">You:</span>
                            <?php endif; ?>
                            <?= escape(mb_strimwidth((string) $latest['message'], 0, 100, '...')); ?>
                        </p>
                        <small class="muted">
                            <?= escape($latest['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $latest['created_at']->toDateTime()->format('M j, g:i A') : ''); ?>
                        </small>
                    </div>
                    <div>
                        <a href="/messages?job_id=<?= escape((string) $job['_id']); ?>" class="btn btn-small">Open Chat</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
