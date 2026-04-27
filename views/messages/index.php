<div style="display: flex; flex-direction: column; height: calc(100vh - 120px); min-height: 500px; background: white; border-radius: 16px; box-shadow: var(--shadow-xl); overflow: hidden; position: relative;">
    
    <!-- 1. Header (Fixed at top) -->
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-base); background: white; z-index: 10; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
        <div class="flex items-center gap-3">
            <div class="user-avatar-rect" style="width: 44px; height: 44px; background: var(--grad-primary); color: white; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: 700;">
                <?= mb_substr(escape($otherParty['name'] ?? 'U'), 0, 1); ?>
            </div>
            <div>
                <h2 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-main); line-height: 1.2;"><?= escape((string) ($otherParty['name'] ?? 'Unknown')); ?></h2>
                <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">Discussing: <?= escape((string) ($job['service_type'] ?? 'Untitled Job')); ?></p>
            </div>
        </div>
        <div class="flex gap-3 items-center">
            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-success-soft text-success rounded-full" style="font-size: 0.75rem; font-weight: 700;">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-success"></span>
                </span>
                Connected
            </div>
            <a href="/dashboard" class="btn btn-outline btn-sm" style="padding: 0.5rem; display: flex; align-items: center; justify-content: center;" title="Back to Dashboard">
                <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
            </a>
        </div>
    </div>

    <!-- 2. Messages Area (Scrollable) -->
    <div id="chat-box" style="flex: 1; overflow-y: auto; padding: 1.5rem; background: var(--background); display: flex; flex-direction: column; gap: 1rem; scroll-behavior: smooth;">
        <!-- Messages loaded via AJAX -->
    </div>

    <!-- 3. Input Area (Fixed at bottom) -->
    <div style="background: white; padding: 1rem 1.5rem; border-top: 1px solid var(--border-base); z-index: 10; flex-shrink: 0;">
        <form id="chat-form" action="/messages" method="POST" style="display: flex; gap: 1rem; align-items: center; margin: 0; width: 100%;">
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? ''); ?>">
            <input type="hidden" name="job_id" id="current-job-id" value="<?= escape((string)$job['_id']); ?>">
            <input type="hidden" name="ajax" value="1">
            
            <input type="text" name="message" id="message-input" placeholder="Type your message..." required autocomplete="off" 
                   style="flex: 1; height: 52px; border-radius: 26px; padding: 0 1.5rem; border: 1px solid var(--border-base); background: var(--background); outline: none; transition: border-color 0.2s; font-size: 0.95rem;">
            
            <button type="submit" id="send-btn" class="btn btn-primary" style="height: 52px; width: 52px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: var(--shadow-md);">
                <span class="material-symbols-outlined" style="font-size: 1.5rem;">send</span>
            </button>
        </form>
    </div>
</div>

<script>
(() => {
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const jobId = document.getElementById('current-job-id').value;
    const userId = "<?= $userId; ?>";
    
    let lastMessageCount = 0;

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
            const msgEl = document.createElement('div');
            // Clean styling for individual messages
            msgEl.style.display = 'flex';
            msgEl.style.flexDirection = 'column';
            msgEl.style.maxWidth = '75%'; // Prevent huge bubbles on desktop
            msgEl.style.alignSelf = isSender ? 'flex-end' : 'flex-start';
            
            // Note: Modern corners: 16px all around except the tail piece which is 4px
            const borderRadius = isSender ? '18px 18px 4px 18px' : '18px 18px 18px 4px';
            const bgColor = isSender ? 'var(--primary)' : 'white';
            const color = isSender ? 'white' : 'var(--text-main)';
            const border = isSender ? 'none' : '1px solid var(--border-base)';
            const shadow = '0 2px 4px rgba(0,0,0,0.04)';
            
            // Format time correctly
            const timeAlign = isSender ? 'right' : 'left';
            const checkmark = isSender ? '<span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle; margin-left: 2px;">done_all</span>' : '';

            msgEl.innerHTML = `
                <div style="font-size: 0.95rem; line-height: 1.5; background: ${bgColor}; color: ${color}; padding: 0.8rem 1.25rem; border-radius: ${borderRadius}; box-shadow: ${shadow}; border: ${border}; word-break: break-word;">
                    ${escapeHTML(msg.message).replace(/\n/g, '<br>')}
                </div>
                <div style="font-size: 0.70rem; color: var(--text-muted); font-weight: 500; margin-top: 0.4rem; text-align: ${timeAlign}; padding: 0 0.25rem;">
                    ${msg.created_at} ${checkmark}
                </div>
            `;
            chatBox.appendChild(msgEl);
        });

        // Always scroll to bottom when new messages arrive
        chatBox.scrollTop = chatBox.scrollHeight;
        lastMessageCount = messages.length;
    };

    const fetchMessages = async () => {
        if (document.hidden) return;
        try {
            const resp = await fetch(`/api/messages?job_id=${jobId}&_t=${Date.now()}`, { cache: 'no-store' });
            const data = await resp.json();
            if (data.messages) {
                renderMessages(data.messages);
            }
        } catch (e) {
            console.error('Fetch failed:', e);
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
        const msgEl = document.createElement('div');
        msgEl.style.display = 'flex';
        msgEl.style.flexDirection = 'column';
        msgEl.style.maxWidth = '75%';
        msgEl.style.alignSelf = 'flex-end';
        
        msgEl.innerHTML = `
            <div style="font-size: 0.95rem; line-height: 1.5; background: var(--primary); color: white; padding: 0.8rem 1.25rem; border-radius: 18px 18px 4px 18px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); border: none; word-break: break-word; opacity: 0.8;">
                ${escapeHTML(msg).replace(/\n/g, '<br>')}
            </div>
            <div style="font-size: 0.70rem; color: var(--text-muted); font-weight: 500; margin-top: 0.4rem; text-align: right; padding: 0 0.25rem;">
                Sending...
            </div>
        `;
        
        if (chatBox.innerHTML.includes('No messages yet')) {
            chatBox.innerHTML = '';
        }
        chatBox.appendChild(msgEl);
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
