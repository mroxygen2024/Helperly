<?php
$currentUri = $_SERVER['REQUEST_URI'] ?? '/messages';
$jobParam = sanitizeInput($_GET['job_id'] ?? '');
?>

<section class="messenger-page">
    <div class="messenger-card messenger-sidebar">
        <div class="messenger-sidebar-header">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="messenger-title">Messages</h2>
                    <p class="messenger-subtitle">Conversations with parents and servants</p>
                </div>
                <a href="/dashboard" class="chat-action-btn" title="Back to Dashboard">
                    <span class="material-symbols-outlined">close</span>
                </a>
            </div>
            <label class="messenger-search-wrap" aria-label="Search conversations">
                <span class="material-symbols-outlined">search</span>
                <input type="text" id="conversation-search" class="messenger-search-input" placeholder="Search by name, message, or job">
            </label>
        </div>

        <div class="messenger-conversation-list" id="conversation-list">
            <?php if (empty($conversations)): ?>
                <div class="messenger-empty">
                    <div>
                        <span class="material-symbols-outlined">forum</span>
                        <h3 class="messenger-title" style="font-size:1.05rem;">Your inbox is empty</h3>
                        <p class="messenger-subtitle" style="max-width: 280px; margin: 0.35rem auto 0;">
                            Conversations will appear here after booking or applying for jobs.
                        </p>
                        <a href="/dashboard" class="btn btn-primary" style="margin-top: 1rem; border-radius: 999px;">Back to Dashboard</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $conv):
                    $hasUnread = ($conv['unread_count'] ?? 0) > 0;
                    $otherRoleRaw = normalizeRole((string) ($conv['other_party']['role'] ?? ''));
                    $otherRole = $otherRoleRaw === 'parent' ? 'Parent' : ($otherRoleRaw === 'provider' ? 'Servant' : 'User');
                    $roleClass = $otherRoleRaw === 'parent' ? 'messenger-role-parent' : 'messenger-role-servant';
                    $isActive = $jobParam && (string)($conv['job']['_id'] ?? '') === $jobParam;
                    $searchBlob = mb_strtolower(trim((string) (
                        ($conv['other_party']['name'] ?? '') . ' ' .
                        ($conv['last_message'] ?? '') . ' ' .
                        ($conv['job']['service_type'] ?? '')
                    )));
                ?>
                    <a
                        href="/messages?job_id=<?= escape((string) $conv['job']['_id']); ?>"
                        class="messenger-conversation-item <?= $isActive ? 'is-active' : ''; ?>"
                        data-conversation-item
                        data-search="<?= escape($searchBlob); ?>"
                    >
                        <div class="messenger-avatar">
                            <?php if (!empty($conv['other_party']['profile_photo'])): ?>
                                <img src="<?= escape((string) $conv['other_party']['profile_photo']); ?>" alt="<?= escape((string) ($conv['other_party']['name'] ?? 'User')); ?>">
                            <?php else: ?>
                                <?= mb_substr(escape((string) ($conv['other_party']['name'] ?? 'U')), 0, 1); ?>
                            <?php endif; ?>
                            <span class="messenger-presence-dot"></span>
                        </div>

                        <div class="messenger-conversation-body">
                            <div class="messenger-conversation-row">
                                <p class="messenger-name"><?= escape((string) ($conv['other_party']['name'] ?? 'Participant')); ?></p>
                                <span class="messenger-time">
                                    <?= isset($conv['last_message_time'])
                                        ? (is_string($conv['last_message_time'])
                                            ? date('g:i A', strtotime($conv['last_message_time']))
                                            : ($conv['last_message_time'] instanceof \MongoDB\BSON\UTCDateTime
                                                ? $conv['last_message_time']->toDateTime()->format('g:i A')
                                                : ''))
                                        : ''; ?>
                                </span>
                            </div>

                            <div class="messenger-conversation-row" style="margin-top: 0.2rem;">
                                <span class="messenger-role-badge <?= $roleClass; ?>"><?= escape($otherRole); ?></span>
                                <?php if ($hasUnread): ?>
                                    <span class="messenger-unread"><?= (int) $conv['unread_count']; ?></span>
                                <?php endif; ?>
                            </div>

                            <span class="messenger-job-pill" title="<?= escape((string) ($conv['job']['service_type'] ?? 'Job')); ?>">
                                <span class="material-symbols-outlined" style="font-size: 12px;">work</span>
                                <?= escape((string) ($conv['job']['service_type'] ?? 'Job')); ?>
                            </span>

                            <p class="messenger-preview <?= $hasUnread ? 'is-unread' : ''; ?>">
                                <?php if (!empty($conv['last_message'])): ?>
                                    <?= escape((string) $conv['last_message']); ?>
                                <?php else: ?>
                                    Start chatting now...
                                <?php endif; ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
(() => {
    const searchInput = document.getElementById('conversation-search');
    const items = Array.from(document.querySelectorAll('[data-conversation-item]'));

    if (!searchInput || items.length === 0) return;

    searchInput.addEventListener('input', () => {
        const term = searchInput.value.trim().toLowerCase();
        items.forEach((item) => {
            const haystack = item.dataset.search || '';
            item.style.display = haystack.includes(term) ? '' : 'none';
        });
    });
})();
</script>
