<div style="display: flex; flex-direction: column; height: calc(100vh - 120px); min-height: 500px; background: white; border-radius: 16px; box-shadow: var(--shadow-xl); overflow: hidden; position: relative;">
    
    <!-- Header (Fixed) -->
    <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-base); background: white; z-index: 10; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--text-main); line-height: 1.2;">Inbox</h2>
            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Manage your active conversations</p>
        </div>
        <div class="flex gap-2">
            <a href="/dashboard" class="btn btn-outline btn-sm" style="padding: 0.5rem; display: flex; align-items: center; justify-content: center;" title="Back to Dashboard">
                <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
            </a>
        </div>
    </div>

    <!-- Conversations List (Scrollable) -->
    <div style="flex: 1; overflow-y: auto; background: var(--background);">
        <?php if (empty($conversations)): ?>
            <div style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; color: var(--text-muted); padding: 2rem;">
                <div style="background: white; padding: 1.5rem; border-radius: 50%; box-shadow: var(--shadow-sm); margin-bottom: 1rem;">
                    <span class="material-symbols-outlined" style="font-size: 4rem;">chat_bubble_outline</span>
                </div>
                <h3 style="font-weight: 600; font-size: 1.1rem; margin: 0;">No conversations yet</h3>
                <p style="font-size: 0.85rem; margin-top: 0.5rem; text-align: center;">When you book a provider or start a job, chat will appear here.</p>
                <a href="/servants" class="btn btn-primary" style="margin-top: 1.5rem; border-radius: 99px; padding: 0.75rem 2rem;">Find Providers</a>
            </div>
        <?php else: ?>
            <div style="background: white; display: flex; flex-direction: column;">
                <?php foreach ($conversations as $conv): ?>
                    <a href="/messages?job_id=<?= escape((string)$conv['job']['_id']); ?>" 
                       style="display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem 2rem; border-bottom: 1px solid var(--border-base); transition: background 0.2s; text-decoration: none; color: inherit;"
                       onmouseover="this.style.background='var(--background)'"
                       onmouseout="this.style.background='transparent'">
                        
                        <div class="user-avatar-rect" style="width: 56px; height: 56px; background: var(--grad-primary); flex-shrink: 0; color: white; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem;">
                            <?= mb_substr(escape($conv['other_party']['name'] ?? 'U'), 0, 1); ?>
                        </div>
                        
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                <h3 style="margin: 0; font-weight: 700; font-size: 1.05rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= escape($conv['other_party']['name'] ?? 'Participant'); ?></h3>
                                <span style="font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 12px; background: var(--primary-soft); color: var(--primary); flex-shrink: 0;"><?= escape(ucfirst($conv['job']['status'])); ?></span>
                            </div>
                            <p style="margin: 0; font-size: 0.8rem; font-weight: 600; color: var(--primary); margin-bottom: 4px;"><?= escape($conv['job']['service_type']); ?></p>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?php if (!empty($conv['last_message'])): ?>
                                    <?= escape($conv['last_message']); ?>
                                <?php else: ?>
                                    <i>Click to start chatting...</i>
                                <?php endif; ?>
                            </p>
                        </div>
                        <span class="material-symbols-outlined" style="color: var(--border-base); font-size: 24px;">chevron_right</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
