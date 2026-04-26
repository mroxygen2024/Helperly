<div class="card p-0 overflow-hidden" style="display: flex; flex-direction: column; height: calc(100vh - 160px); border: none; box-shadow: var(--shadow-xl);">
    <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-base); padding: 1.5rem 2.5rem;">
        <div class="flex justify-between items-center w-full">
            <div class="flex items-center gap-4">
                <div class="user-avatar-rect" style="width: 48px; height: 48px; background: var(--grad-primary);">
                    <?= mb_substr(escape($otherParty['name'] ?? 'U'), 0, 1); ?>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.25rem;"><?= escape((string) ($otherParty['name'] ?? 'Unknown')); ?></h2>
                    <p class="text-sm text-muted">Discussing: <?= escape((string) ($job['service_type'] ?? 'Untitled Job')); ?></p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="/dashboard" class="btn btn-outline btn-sm" title="Back to Dashboard">
                    <span class="material-symbols-outlined">exit_to_app</span>
                </a>
            </div>
        </div>
    </div>

    <div class="message-list flex-1 p-8 flex flex-col overflow-y-auto" id="chat-box">
        <?php if (empty($messages)): ?>
            <div class="flex-1 flex flex-col justify-center items-center text-muted" style="opacity: 0.5;">
                <span class="material-symbols-outlined" style="font-size: 4rem; margin-bottom: 1rem;">chat_bubble_outline</span>
                <p class="font-600">No messages yet</p>
                <p class="text-sm">Start the conversation by sending a message below.</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <?php $isSender = ((string)$msg['sender_id'] === $userId); ?>
                <div class="message <?= $isSender ? 'sent' : 'received'; ?>" 
                     style="margin-bottom: 1rem; max-width: 70%; <?= $isSender ? 'align-self: flex-end;' : 'align-self: flex-start;'; ?>">
                    <div style="font-size: 0.95rem;"><?= nl2br(escape((string) ($msg['message'] ?? ''))); ?></div>
                    <div class="flex <?= $isSender ? 'justify-end' : 'justify-start'; ?> items-center mt-1" 
                         style="font-size: 0.7rem; opacity: 0.7; font-weight: 600;">
                        <span><?= escape(isset($msg['created_at']) && $msg['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $msg['created_at']->toDateTime()->format('g:i A') : ''); ?></span>
                        <?php if ($isSender): ?>
                            <span class="material-symbols-outlined" style="font-size: 12px; margin-left: 4px;">done_all</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="chat-input-container">
        <form action="/messages" method="POST" class="flex gap-4 items-center">
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? ''); ?>">
            <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
            
            <div class="flex-1" style="position: relative;">
                <input type="text" name="message" class="input-field" placeholder="Write your message here..." required autocomplete="off" 
                       style="border-radius: 99px; height: 56px; padding-left: 2rem; border-color: var(--border-base); background: var(--background);">
            </div>
            
            <button type="submit" class="btn btn-primary" style="border-radius: 50%; width: 56px; height: 56px; padding: 0; min-width: 56px;">
                <span class="material-symbols-outlined" style="font-size: 1.5rem;">send</span>
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
