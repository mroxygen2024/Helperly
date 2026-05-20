<?php
$otherRoleRaw = normalizeRole((string) ($otherParty['role'] ?? ''));
$otherRole = $otherRoleRaw === 'parent' ? 'Parent' : ($otherRoleRaw === 'provider' ? 'Servant' : 'User');
$jobStatus = escape(ucfirst((string) ($job['status'] ?? 'active')));
?>

<section class="messenger-page">
    <div class="messenger-shell">
        <article class="messenger-card messenger-chat">
            <header class="chat-header">
                <div class="chat-header-main">
                    <div class="chat-header-avatar">
                        <?php if (!empty($otherParty['profile_photo'])): ?>
                            <img src="<?= escape((string) $otherParty['profile_photo']); ?>" alt="<?= escape((string) ($otherParty['name'] ?? 'User')); ?>">
                        <?php else: ?>
                            <?= mb_substr(escape((string) ($otherParty['name'] ?? 'U')), 0, 1); ?>
                        <?php endif; ?>
                    </div>
                    <div style="min-width:0;">
                        <h2 class="chat-header-name"><?= escape((string) ($otherParty['name'] ?? 'Unknown')); ?></h2>
                        <p class="chat-header-sub">
                            <span class="messenger-role-badge <?= $otherRoleRaw === 'parent' ? 'messenger-role-parent' : 'messenger-role-servant'; ?>"><?= escape($otherRole); ?></span>
                            <span class="chat-status-pill">
                                <span class="material-symbols-outlined" style="font-size: 12px;">circle</span>
                                Online
                            </span>
                            <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:220px;">
                                <?= escape((string) ($job['service_type'] ?? 'Untitled Job')); ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="chat-header-actions">
                    <span class="chat-sync">
                        <span class="material-symbols-outlined" style="font-size: 14px;">sync</span>
                        Connected
                        <span id="heartbeat"></span>
                    </span>
                    <button type="button" class="chat-action-btn" title="Call">
                        <span class="material-symbols-outlined" style="font-size: 18px;">call</span>
                    </button>
                    <button type="button" class="chat-action-btn" title="Attachments">
                        <span class="material-symbols-outlined" style="font-size: 18px;">attach_file</span>
                    </button>
                    <button type="button" class="chat-action-btn" title="More">
                        <span class="material-symbols-outlined" style="font-size: 18px;">more_horiz</span>
                    </button>
                    <a href="/messages" class="chat-action-btn" title="Back to conversations">
                        <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
                    </a>
                    <a href="/dashboard" class="chat-action-btn" title="Back to dashboard">
                        <span class="material-symbols-outlined" style="font-size: 18px;">close</span>
                    </a>
                </div>
            </header>

            <div id="chat-box" class="chat-messages">
                <?php if (empty($messages)): ?>
                    <div id="empty-state" class="messenger-empty" style="min-height: 100%;">
                        <div>
                            <span class="material-symbols-outlined">chat_bubble</span>
                            <h3 class="messenger-title" style="font-size:1.05rem;">Start the conversation</h3>
                            <p class="messenger-subtitle" style="max-width: 270px; margin: 0.35rem auto 0;">
                                Discuss job details, requirements, and updates in real time.
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $msg):
                        $isSender = (string) $msg['sender_id'] === $userId;
                    ?>
                        <div class="msg-bubble-wrapper <?= $isSender ? 'sent' : 'received'; ?>">
                            <div class="msg-bubble">
                                <?= str_replace("\n", "<br>", escape((string) ($msg['message'] ?? ''))); ?>
                            </div>
                            <div class="msg-meta">
                                <?= $msg['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $msg['created_at']->toDateTime()->format('g:i A') : ''; ?>
                                <?php if ($isSender): ?>
                                    <span class="material-symbols-outlined" style="color: <?= ($msg['is_read'] ?? false) ? 'var(--primary)' : 'inherit'; ?>;">
                                        <?= ($msg['is_read'] ?? false) ? 'done_all' : 'done'; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="chat-typing-row" id="typing-indicator"></div>

            <div class="chat-input-area">
                <form id="chat-form" action="/messages" method="POST" class="chat-input-form">
                    <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? ''); ?>">
                    <input type="hidden" name="job_id" id="current-job-id" value="<?= escape((string) $job['_id']); ?>">
                    <input type="hidden" name="ajax" value="1">

                    <button type="button" class="chat-icon-btn" title="Attachment">
                        <span class="material-symbols-outlined" style="font-size: 18px;">attach_file</span>
                    </button>
                    <button type="button" class="chat-icon-btn" title="Emoji">
                        <span class="material-symbols-outlined" style="font-size: 18px;">mood</span>
                    </button>

                    <input type="text" name="message" id="message-input" placeholder="Write a message..." required autocomplete="off" class="chat-input-field">

                    <button type="submit" id="send-btn" class="chat-send-btn" title="Send message">
                        <span class="material-symbols-outlined" style="font-size: 19px;">send</span>
                    </button>
                </form>
            </div>
        </article>

        <aside class="messenger-card messenger-details">
            <div class="messenger-details-head">
                <h3 class="messenger-details-title">Conversation Details</h3>
            </div>
            <div class="messenger-details-body">
                <div class="details-item">
                    <p class="details-label">Participant</p>
                    <p class="details-value"><?= escape((string) ($otherParty['name'] ?? 'Unknown')); ?></p>
                </div>
                <div class="details-item">
                    <p class="details-label">Role</p>
                    <p class="details-value"><?= escape($otherRole); ?></p>
                </div>
                <div class="details-item">
                    <p class="details-label">Job</p>
                    <p class="details-value"><?= escape((string) ($job['service_type'] ?? 'Untitled Job')); ?></p>
                </div>
                <div class="details-item">
                    <p class="details-label">Status</p>
                    <p class="details-value"><?= $jobStatus; ?></p>
                </div>
                <div class="details-item">
                    <p class="details-label">Conversation ID</p>
                    <p class="details-value">#<?= escape(substr((string) ($job['_id'] ?? ''), -8)); ?></p>
                </div>
                <a href="/jobs/detail?id=<?= escape((string) ($job['_id'] ?? '')); ?>" class="btn btn-primary" style="border-radius: 999px; justify-content: center;">
                    Open Job Details
                </a>
            </div>
        </aside>
    </div>
</section>

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
    const sendBtn = document.getElementById('send-btn');
    const typingIndicator = document.getElementById('typing-indicator');
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
                <div class="messenger-empty" style="min-height:100%;">
                    <div>
                        <span class="material-symbols-outlined">chat_bubble_outline</span>
                        <p class="messenger-title" style="font-size:1rem; margin:0.25rem 0 0;">No messages yet</p>
                        <p class="messenger-subtitle" style="margin:0.35rem 0 0;">Start the conversation by sending a message.</p>
                    </div>
                </div>
            `;
            return;
        }

        messages.forEach(msg => {
            const isSender = msg.is_sender;
            const bubbleWrapper = document.createElement('div');
            bubbleWrapper.className = `msg-bubble-wrapper ${isSender ? 'sent' : 'received'}`;
            
            const checkmark = isSender ? '<span class="material-symbols-outlined">done_all</span>' : '';

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
        if (sendBtn) sendBtn.disabled = true;
        if (typingIndicator) typingIndicator.textContent = '';

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
            if (sendBtn) sendBtn.disabled = false;
            messageInput.focus();
        }
    };

    let typingTimeout;
    if (messageInput) {
        messageInput.addEventListener('input', () => {
            if (!typingIndicator) return;
            typingIndicator.textContent = messageInput.value.trim() ? 'Typing...' : '';
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                if (typingIndicator) typingIndicator.textContent = '';
            }, 1200);
        });
    }

    window.addEventListener('beforeunload', () => clearInterval(interval));
})();
</script>
