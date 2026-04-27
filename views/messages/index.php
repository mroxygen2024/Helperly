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
            <div class="flex gap-2 items-center">
                <div class="flex items-center gap-1.5 px-3 py-1 bg-success-soft text-success rounded-full" style="font-size: 0.75rem; font-weight: 700;">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-success"></span>
                    </span>
                    Socket Connected
                </div>
                <a href="/dashboard" class="btn btn-outline btn-sm" title="Back to Dashboard">
                    <span class="material-symbols-outlined">exit_to_app</span>
                </a>
            </div>
        </div>
    </div>

    <div class="message-list flex-1 p-8 flex flex-col overflow-y-auto" id="chat-box" style="scroll-behavior: smooth;">
        <!-- Messages loaded via AJAX -->
    </div>

    <div class="chat-input-container">
        <form id="chat-form" action="/messages" method="POST" class="flex gap-4 items-center">
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? ''); ?>">
            <input type="hidden" name="job_id" id="current-job-id" value="<?= escape((string)$job['_id']); ?>">
            <input type="hidden" name="ajax" value="1">
            
            <div class="flex-1" style="position: relative;">
                <input type="text" name="message" id="message-input" class="input-field" placeholder="Write your message here..." required autocomplete="off" 
                       style="border-radius: 99px; height: 56px; padding-left: 2rem; border-color: var(--border-base); background: var(--background);">
            </div>
            
            <button type="submit" id="send-btn" class="btn btn-primary" style="border-radius: 50%; width: 56px; height: 56px; padding: 0; min-width: 56px;">
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

    const renderMessages = (messages) => {
        if (messages.length === lastMessageCount) return;
        
        chatBox.innerHTML = '';
        if (messages.length === 0) {
            chatBox.innerHTML = `
                <div class="flex-1 flex flex-col justify-center items-center text-muted" style="opacity: 0.5;">
                    <span class="material-symbols-outlined" style="font-size: 4rem; margin-bottom: 1rem;">chat_bubble_outline</span>
                    <p class="font-600">No messages yet</p>
                    <p class="text-sm">Start the conversation by sending a message below.</p>
                </div>
            `;
            return;
        }

        messages.forEach(msg => {
            const isSender = msg.is_sender;
            const msgEl = document.createElement('div');
            msgEl.className = `message ${isSender ? 'sent' : 'received'}`;
            msgEl.style.marginBottom = '1rem';
            msgEl.style.maxWidth = '70%';
            msgEl.style.alignSelf = isSender ? 'flex-end' : 'flex-start';
            
            msgEl.innerHTML = `
                <div style="font-size: 0.95rem; background: ${isSender ? 'var(--primary)' : 'white'}; color: ${isSender ? 'white' : 'var(--text-main)'}; padding: 0.75rem 1.25rem; border-radius: ${isSender ? '20px 20px 4px 20px' : '20px 20px 20px 4px'}; box-shadow: var(--shadow-sm); border: ${isSender ? 'none' : '1px solid var(--border-base)'};">
                    ${msg.message.replace(/\\n/g, '<br>')}
                </div>
                <div class="flex ${isSender ? 'justify-end' : 'justify-start'} items-center mt-1" style="font-size: 0.7rem; opacity: 0.7; font-weight: 600;">
                    <span>${msg.created_at}</span>
                    ${isSender ? '<span class="material-symbols-outlined" style="font-size: 12px; margin-left: 4px; color: var(--primary);">done_all</span>' : ''}
                </div>
            `;
            chatBox.appendChild(msgEl);
        });

        chatBox.scrollTop = chatBox.scrollHeight;
        lastMessageCount = messages.length;
    };

    const fetchMessages = async () => {
        if (document.hidden) return;
        try {
            const resp = await fetch(`/api/messages?job_id=${jobId}`);
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
        
        // Optimistic UI update could be added here
        
        try {
            const resp = await fetch('/messages', {
                method: 'POST',
                body: formData
            });
            const data = await resp.json();
            if (data.success) {
                messageInput.value = '';
                fetchMessages();
            } else {
                alert(data.error || 'Failed to send');
            }
        } catch (e) {
            console.error('Send failed:', e);
        }
    };
})();
</script>
