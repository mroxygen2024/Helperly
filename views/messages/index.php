<section>
    <div class="flex-between">
        <div>
            <h1>Conversation</h1>
            <p class="muted">
                Job: <?= escape((string) ($job['service_type'] ?? 'Untitled')); ?> 
                | With: <?= escape((string) ($otherParty['name'] ?? 'Unknown')); ?>
            </p>
        </div>
        <a href="/?page=dashboard" class="btn btn-outline" style="align-self: center;">&larr; Back</a>
    </div>
</section>

<section class="card stack">
    <div class="chat-box" style="display: flex; flex-direction: column; gap: 1rem; max-height: 500px; overflow-y: auto; padding: 1rem; background: #fdfdfd; border: 1px solid #eaeaea; border-radius: 8px;">
        <?php if (empty($messages)): ?>
            <p class="muted" style="text-align: center;">No messages yet. Send a message to start the conversation.</p>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <?php 
                    $isSender = ((string)$msg['sender_id'] === $userId); 
                    $bubbleColor = $isSender ? '#e1f5fe' : '#f5f5f5';
                    $alignSelf = $isSender ? 'flex-end' : 'flex-start';
                ?>
                <div style="background: <?= $bubbleColor ?>; padding: 0.75rem 1rem; border-radius: 8px; max-width: 75%; align-self: <?= $alignSelf ?>;">
                    <p style="margin: 0; word-break: break-word;"><?= nl2br(escape((string) ($msg['message'] ?? ''))); ?></p>
                    <small class="muted" style="display: block; text-align: right; margin-top: 0.5rem; font-size: 0.75rem;">
                        <?= escape(isset($msg['created_at']) && $msg['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $msg['created_at']->toDateTime()->format('D, M j g:i A') : ''); ?>
                    </small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form action="/messages" method="POST" style="margin-top: 1rem; display: flex; gap: 1rem;">
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? ''); ?>">
        <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">
        
        <input type="text" name="message" placeholder="Type your message..." style="flex: 1; padding: 0.75rem;" required autocomplete="off">
        <button type="submit" class="btn">Send</button>
    </form>
</section>

<script>
    // Scroll chat to bottom on load
    document.addEventListener("DOMContentLoaded", function() {
        var chatBox = document.querySelector('.chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });

    // Optional MVP auto-refresh every 10 seconds simply by reloading the page
    setTimeout(() => {
        window.location.reload();
    }, 10000);
</script>
