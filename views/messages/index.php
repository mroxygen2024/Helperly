<div class="card p-0 overflow-hidden" style="display: flex; flex-direction: column; height: calc(100vh - 200px);">
    <div class="modal-header">
        <div>
            <h2 class="card-title"><?= escape((string) ($otherParty['name'] ?? 'Unknown')); ?></h2>
            <p class="text-sm text-muted">Conversation regarding <?= escape((string) ($job['service_type'] ?? 'Untitled Job')); ?></p>
        </div>
        <div class="flex gap-2">
            <a href="/dashboard" class="btn btn-outline btn-sm">
                <span class="material-symbols-outlined">dashboard</span>
                Dashboard
            </a>
        </div>
    </div>

    <div class="message-list flex-1 p-6 flex flex-col overflow-y-auto" id="chat-box" style="background: var(--bg-main);">
        <?php if (empty($messages)): ?>
            <div class="flex-1 flex flex-col justify-center items-center text-muted">
                <span class="material-symbols-outlined" style="font-size: 3rem; margin-bottom: 1rem;">forum</span>
                <p>No messages yet. Say hi!</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <?php $isSender = ((string)$msg['sender_id'] === $userId); ?>
                <div class="message <?= $isSender ? 'sent' : 'received'; ?>" style="margin-bottom: 1.5rem; max-width: 60%; <?= $isSender ? 'align-self: flex-end;' : 'align-self: flex-start;'; ?>">
                    <p style="margin: 0; font-size: 0.9375rem;"><?= nl2br(escape((string) ($msg['message'] ?? ''))); ?></p>
                    <div class="flex <?= $isSender ? 'justify-between' : 'justify-start'; ?> items-center mt-1" style="font-size: 0.75rem; opacity: 0.8;">
                        <span><?= escape(isset($msg['created_at']) && $msg['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $msg['created_at']->toDateTime()->format('g:i A') : ''); ?></span>
                        <?php if ($isSender): ?>
                            <span class="material-symbols-outlined" style="font-size: 12px; margin-left: 4px;">done_all</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="p-4 border-t bg-white">
        <form action="/messages" method="POST" class="flex gap-2">
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? ''); ?>">
            <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
            
            <input type="text" name="message" class="input flex-1" placeholder="Type a message..." required autocomplete="off" style="border-radius: 9999px; padding-left: 1.5rem;">
            <button type="submit" class="btn btn-primary" style="border-radius: 9999px; width: 48px; height: 48px; padding: 0;">
                <span class="material-symbols-outlined">send</span>
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var chatBox = document.getElementById('chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });

    // Auto-reload to check for new messages every 15 seconds
    setInterval(() => {
        var input = document.querySelector('input[name="message"]');
        if (input && input.value.trim() === "") {
            window.location.reload();
        }
    }, 15000);
</script>
