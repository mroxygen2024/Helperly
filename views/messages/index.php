<div class="chat-viewport-wrapper">
    <div class="chat-container">
        <!-- 1. Header -->
        <header class="chat-header">
            <div class="flex items-center gap-3">
                <div class="user-avatar-rect" style="width: 44px; height: 44px;">
                    <?= mb_substr(escape($otherParty['name'] ?? 'U'), 0, 1); ?>
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-main); line-height: 1.2;"><?= escape((string) ($otherParty['name'] ?? 'Unknown')); ?></h2>
                    <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">Discussing: <?= escape((string) ($job['service_type'] ?? 'Untitled Job')); ?></p>
                </div>
            </div>
            <div class="flex gap-3 items-center">
            <!-- DEBUG INFO (Hidden in production) -->
            <div style="font-size: 10px; opacity: 0.3; text-align: right;">
                Job: <?= substr((string)$job['_id'], -6) ?><br>
                User: <?= substr($userId, -6) ?>
            </div>
            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-success-soft text-success rounded-full" style="font-size: 0.75rem; font-weight: 700;">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-success"></span>
                    </span>
                    Connected
                    <span id="heartbeat" style="margin-left: 4px; font-weight: normal; opacity: 0.5;"></span>
                </div>
                <a href="/dashboard" class="btn btn-outline btn-sm" style="padding: 0.5rem; display: flex;" title="Back to Dashboard">
                    <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
                </a>
            </div>
        </header>

        <!-- 2. Messages Area (Inner Scroll) -->
        <div id="chat-box" class="chat-messages" style="background: #F1F5F9; padding: 1.5rem;">
            <?php if (empty($messages)): ?>
                <div id="empty-state" style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 4rem 2rem;">
                    <div style="background: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem; color: var(--primary);">
                        <span class="material-symbols-outlined" style="font-size: 2.5rem;">chat_bubble</span>
                    </div>
                    <h3 style="font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">Start the conversation</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); max-width: 250px;">Discuss job details, requirements, or any questions here.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): 
                    $isSender = (string)$msg['sender_id'] === $userId;
                ?>
                    <div class="msg-bubble-wrapper <?= $isSender ? 'sent' : 'received' ?>" style="margin-bottom: 1.25rem;">
                        <div class="msg-bubble" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-radius: <?= $isSender ? '18px 18px 2px 18px' : '18px 18px 18px 2px' ?>;">
                            <?= str_replace("\n", "<br>", escape($msg['message'])); ?>
                        </div>
                        <div class="msg-meta" style="font-size: 0.7rem; font-weight: 700; opacity: 0.6; margin-top: 4px; display: flex; align-items: center; gap: 4px; justify-content: <?= $isSender ? 'flex-end' : 'flex-start' ?>;">
                            <?= $msg['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $msg['created_at']->toDateTime()->format('g:i A') : ''; ?>
                            <?php if ($isSender): ?>
                                <span class="material-symbols-outlined" style="font-size: 14px; color: <?= ($msg['is_read'] ?? false) ? 'var(--primary)' : 'inherit' ?>;">
                                    <?= ($msg['is_read'] ?? false) ? 'done_all' : 'done' ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 3. Input Area -->
        <div class="chat-input-area" style="background: white; border-top: 1px solid var(--border-base); padding: 1.25rem 2rem;">
            <form id="chat-form" action="/messages" method="POST" class="chat-input-form" style="background: #F8FAFC; border: 1px solid var(--border-base); border-radius: 99px; padding: 0.4rem 0.4rem 0.4rem 1.5rem; display: flex; align-items: center; gap: 1rem;">
                <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? ''); ?>">
                <input type="hidden" name="job_id" id="current-job-id" value="<?= escape((string)$job['_id']); ?>">
                <input type="hidden" name="ajax" value="1">
                
                <button type="button" class="btn btn-ghost btn-sm" style="padding: 0; min-width: 32px; height: 32px; border-radius: 50%; color: var(--text-muted);">
                    <span class="material-symbols-outlined" style="font-size: 20px;">add_circle</span>
                </button>

                <input type="text" name="message" id="message-input" placeholder="Type your message..." required autocomplete="off" class="chat-input-field" style="background: transparent; border: none; flex: 1; outline: none; font-size: 0.95rem; font-weight: 500; height: 40px; color: var(--text-main);">
                
                <button type="submit" id="send-btn" class="chat-send-btn" style="background: var(--primary); color: white; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: transform 0.2s; border: none; box-shadow: var(--shadow-md);">
                    <span class="material-symbols-outlined" style="font-size: 1.25rem;">send</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    // Layout Layout adjustment for full-screen chat
    const contentBody = document.querySelector('.content-body');
    if (contentBody) {
        contentBody.style.padding = '0';
        contentBody.style.maxWidth = 'none';
        contentBody.style.height = 'calc(100vh - var(--topbar-height))';
    }

    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const jobId = document.getElementById('current-job-id').value;
    const userId = "<?= $userId; ?>";
    
    let lastMessageCount = <?= count($messages); ?>;
    let isInitialLoad = true;

    // Scroll to bottom immediately on load
    chatBox.scrollTop = chatBox.scrollHeight;

    const escapeHTML = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    const renderMessages = (messages) => {
        if (messages.length === lastMessageCount) return;
        
        chatBox.innerHTML = '';
        if (messages.length === 0) {
            chatBox.innerHTML = `
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; items-center; font-weight: center; opacity: 0.5; color: var(--text-muted); align-items: center;">
                    <span class="material-symbols-outlined" style="font-size: 4rem; margin-bottom: 1rem;">chat_bubble_outline</span>
                    <p style="font-weight: 600; margin: 0;">No messages yet</p>
                    <p style="font-size: 0.85rem; margin-top: 0.25rem;">Start the conversation by sending a message.</p>
                </div>
            `;
            return;
        }

        messages.forEach(msg => {
            const isSender = msg.is_sender;
            const bubbleWrapper = document.createElement('div');
            bubbleWrapper.className = `msg-bubble-wrapper ${isSender ? 'sent' : 'received'}`;
            
            const checkmark = isSender ? '<span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle; margin-left: 2px;">done_all</span>' : '';

            bubbleWrapper.innerHTML = `
                <div class="msg-bubble">
                    ${escapeHTML(msg.message).replace(/\n/g, '<br>')}
                </div>
                <div class="msg-meta">
                    ${msg.created_at} ${checkmark}
                </div>
            `;
            chatBox.appendChild(bubbleWrapper);
        });

        // Always scroll to bottom when new messages arrive
        chatBox.scrollTop = chatBox.scrollHeight;
        lastMessageCount = messages.length;
    };

    const fetchMessages = async () => {
        const heartbeat = document.getElementById('heartbeat');
        try {
            const resp = await fetch(`/api/messages?job_id=${jobId}&_t=${Date.now()}`, { 
                cache: 'no-store',
                credentials: 'same-origin',
                headers: { 'Pragma': 'no-cache', 'Cache-Control': 'no-cache' }
            });
            
            if (!resp.ok) {
                const err = await resp.json().catch(() => ({}));
                console.error('Polling failed:', resp.status, err);
                if (heartbeat) heartbeat.textContent = ' (Sync Error)';
                return;
            }

            const data = await resp.json();
            if (heartbeat) heartbeat.textContent = ` (Last sync: ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'})})`;
            
            if (data.messages && (data.messages.length !== lastMessageCount || isInitialLoad)) {
                isInitialLoad = false;
                renderMessages(data.messages);
            }
        } catch (e) {
            console.error('Fetch error:', e);
            if (heartbeat) heartbeat.textContent = ` (Offline: ${e.message})`;
        }
    };

    // Initial fetch
    fetchMessages();
    
    // Polling interval
    const interval = setInterval(fetchMessages, 3000);

    chatForm.onsubmit = async (e) => {
        e.preventDefault();
        const msg = messageInput.value.trim();
        if (!msg) return;

        const formData = new FormData(chatForm);
        
        // Optimistic UI Append
        const optimisticWrapper = document.createElement('div');
        optimisticWrapper.className = 'msg-bubble-wrapper sent';
        optimisticWrapper.style.opacity = '0.7';
        
        optimisticWrapper.innerHTML = `
            <div class="msg-bubble">
                ${escapeHTML(msg).replace(/\n/g, '<br>')}
            </div>
            <div class="msg-meta">
                Sending...
            </div>
        `;
        
        if (chatBox.innerHTML.includes('No messages yet')) {
            chatBox.innerHTML = '';
        }
        chatBox.appendChild(optimisticWrapper);
        chatBox.scrollTop = chatBox.scrollHeight;
        
        messageInput.value = '';
        messageInput.disabled = true;

        try {
            const resp = await fetch('/messages', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });
            const data = await resp.json();
            if (data.success) {
                // Instantly fetch upon success
                await fetchMessages();
            } else {
                alert(data.error || 'Failed to send');
            }
        } catch (e) {
            console.error('Send failed:', e);
        } finally {
            messageInput.disabled = false;
            messageInput.focus();
        }
    };
})();
</script>
