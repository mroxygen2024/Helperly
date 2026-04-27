<div class="card p-0 overflow-hidden" style="display: flex; flex-direction: column; height: calc(100vh - 160px); border: none; box-shadow: var(--shadow-xl);">
    <div class="card-header" style="background: white; border-bottom: 1px solid var(--border-base); padding: 1.5rem 2.5rem;">
        <div class="flex justify-between items-center w-full">
            <div>
                <h2 class="card-title" style="margin: 0; font-size: 1.5rem;">Inbox</h2>
                <p class="text-sm text-muted">Manage your active conversations</p>
            </div>
            <div class="flex gap-2">
                <a href="/dashboard" class="btn btn-outline btn-sm">
                    <span class="material-symbols-outlined">dashboard</span>
                </a>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto bg-gray-50">
        <?php if (empty($conversations)): ?>
            <div class="h-full flex flex-col justify-center items-center text-muted py-20">
                <div class="bg-white p-6 rounded-full shadow-sm mb-4">
                    <span class="material-symbols-outlined" style="font-size: 4rem;">chat_bubble_outline</span>
                </div>
                <h3 class="font-600 text-lg">No conversations yet</h3>
                <p class="text-sm">When you book a provider or start a job, chat will appear here.</p>
                <a href="/servants" class="btn btn-primary mt-6">Find Providers</a>
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-100 bg-white">
                <?php foreach ($conversations as $conv): ?>
                    <a href="/messages?job_id=<?= escape((string)$conv['job']['_id']); ?>" 
                       class="flex items-center gap-4 p-6 hover:bg-slate-50 transition-colors">
                        <div class="user-avatar-rect" style="width: 56px; height: 56px; background: var(--grad-primary); flex-shrink: 0;">
                            <?= mb_substr(escape($conv['other_party']['name'] ?? 'U'), 0, 1); ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="font-700 text-slate-900 truncate"><?= escape($conv['other_party']['name'] ?? 'Participant'); ?></h3>
                                <span class="text-xs text-muted"><?= escape(ucfirst($conv['job']['status'])); ?></span>
                            </div>
                            <p class="text-sm font-600 text-primary mb-1"><?= escape($conv['job']['service_type']); ?></p>
                            <p class="text-sm text-muted truncate"><?= escape($conv['last_message'] ?? 'Click to start chatting...'); ?></p>
                        </div>
                        <span class="material-symbols-outlined text-slate-300">chevron_right</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
