<section>
    <h1>Marketplace</h1>
    <p class="muted">Find trusted servants or post employer needs.</p>
</section>

<section class="grid">
    <?php if (empty($listings)): ?>
        <article class="card">
            <h2>No listings yet</h2>
            <p>Listings will appear here once available.</p>
        </article>
    <?php else: ?>
        <?php foreach ($listings as $item): ?>
            <article class="card">
                <h2><?= escape((string) ($item['title'] ?? 'Untitled')); ?></h2>
                <p><?= escape((string) ($item['description'] ?? '')); ?></p>
                <p class="muted">Location: <?= escape((string) ($item['location'] ?? 'Unknown')); ?></p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
