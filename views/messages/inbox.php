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
    <div style="flex: 1; overflow-y: auto; background: #F8FAFC;">
        <?php if (empty($conversations)): ?>
            <div style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; color: var(--text-muted); padding: 4rem 2rem; text-align: center;">
                <div style="background: white; padding: 2rem; border-radius: 50%; box-shadow: var(--shadow-md); margin-bottom: 1.5rem; color: var(--primary);">
                    <span class="material-symbols-outlined" style="font-size: 5rem;">forum</span>
                </div>
                <h3 style="font-weight: 800; font-size: 1.5rem; margin: 0; color: var(--text-main);">Your inbox is empty</h3>
                <p style="font-size: 0.95rem; margin-top: 0.75rem; max-width: 300px; line-height: 1.5;">When you book a provider or apply for a job, your conversations will appear here.</p>
                <a href="/dashboard" class="btn btn-primary" style="margin-top: 2rem; border-radius: 99px; padding: 0.75rem 2.5rem; font-weight: 700;">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <div style="background: white; display: flex; flex-direction: column;">
                <?php foreach ($conversations as $conv): 
                    $hasUnread = ($conv['unread_count'] ?? 0) > 0;
                ?>
                    <a href="/messages?job_id=<?= escape((string)$conv['job']['_id']); ?>" 
                       style="display: flex; align-items: center; gap: 1.25rem; padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-base); transition: all 0.2s; text-decoration: none; color: inherit; position: relative; <?= $hasUnread ? 'background: #F0F7FF;' : '' ?>"
                       onmouseover="this.style.background='#F1F5F9'"
                       onmouseout="this.style.background='<?= $hasUnread ? '#F0F7FF' : 'transparent' ?>'">
                        
                        <div style="position: relative; flex-shrink: 0;">
                            <div class="user-avatar-rect" style="width: 60px; height: 60px; background: var(--grad-primary); color: white; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.4rem; box-shadow: var(--shadow-sm);">
                                <?php if (!empty($conv['other_party']['profile_photo'])): ?>
                                    <img src="<?= escape($conv['other_party']['profile_photo']); ?>" style="width:100%; height:100%; object-fit: cover; border-radius: 16px;">
                                <?php else: ?>
                                    <?= mb_substr(escape($conv['other_party']['name'] ?? 'U'), 0, 1); ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($hasUnread): ?>
                                <span style="position: absolute; -top: 6px; -right: 6px; width: 22px; height: 22px; background: var(--danger); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; border: 3px solid white; box-shadow: var(--shadow-sm);">
                                    <?= $conv['unread_count']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <h3 style="margin: 0; font-weight: 800; font-size: 1.1rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= escape($conv['other_party']['name'] ?? 'Participant'); ?></h3>
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">
                                    <?= isset($conv['last_message_time']) ? (is_string($conv['last_message_time']) ? date('g:i A', strtotime($conv['last_message_time'])) : ($conv['last_message_time'] instanceof \MongoDB\BSON\UTCDateTime ? $conv['last_message_time']->toDateTime()->format('g:i A') : '')) : ''; ?>
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; padding: 2px 8px; border-radius: 6px; background: var(--primary-soft); color: var(--primary);"><?= escape($conv['job']['service_type']); ?></span>
                                <span style="width: 4px; height: 4px; border-radius: 50%; background: #CBD5E1;"></span>
                                <span style="font-size: 0.7rem; font-weight: 700; color: <?= $conv['job']['status'] === 'active' ? 'var(--warning)' : 'var(--success)' ?>;"><?= escape(ucfirst($conv['job']['status'])); ?></span>
                            </div>
                            <p style="margin: 0; font-size: 0.9rem; color: <?= $hasUnread ? 'var(--text-main)' : 'var(--text-muted)' ?>; font-weight: <?= $hasUnread ? '700' : '500' ?>; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?php if (!empty($conv['last_message'])): ?>
                                    <?= escape($conv['last_message']); ?>
                                <?php else: ?>
                                    <i style="opacity: 0.6;">Click to start chatting...</i>
                                <?php endif; ?>
                            </p>
                        </div>
                        <span class="material-symbols-outlined" style="color: #CBD5E1; font-size: 24px; margin-left: 0.5rem;">chevron_right</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
