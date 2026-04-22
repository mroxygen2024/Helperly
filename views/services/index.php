<section class="card">
    <h1>My Service Offerings</h1>
    <p class="muted">Define what services you provide, your pricing, and availability.</p>

    <!-- Post New Service -->
    <div style="margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-color, #eee);">
        <h3>Post a New Offering</h3>
        <form action="/services" method="POST" class="form-grid" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

            <label for="service_type">Service Type</label>
            <input id="service_type" name="service_type" type="text" value="<?= escape(old('service_type')); ?>" placeholder="e.g. House Cleaning, Gardening" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3" required><?= escape(old('description')); ?></textarea>

            <label for="price">Price / Rate</label>
            <input id="price" name="price" type="text" value="<?= escape(old('price')); ?>" placeholder="e.g. 500 BDT/hour or Fixed 2000 BDT" required>

            <label for="availability">Availability</label>
            <input id="availability" name="availability" type="text" value="<?= escape(old('availability')); ?>" placeholder="e.g. Mon-Fri 9am-5pm" required>

            <div style="grid-column: 1 / -1;">
                <button type="submit" class="btn">Post Offering</button>
            </div>
        </form>
    </div>

    <!-- List Existing Services -->
    <div>
        <h3>Your Current Offerings</h3>
        <?php if (empty($myServices)): ?>
            <p class="muted">You haven't posted any service offerings yet.</p>
        <?php else: ?>
            <div class="list-container">
                <?php foreach ($myServices as $service): ?>
                    <div class="card" style="margin-bottom: 1rem; border: 1px solid var(--border-color, #eee);">
                        <h4><?= escape((string)$service['service_type']); ?></h4>
                        <p><?= nl2br(escape((string)$service['description'])); ?></p>
                        <p><strong>Price:</strong> <?= escape((string)$service['price']); ?></p>
                        <p><strong>Availability:</strong> <?= escape((string)$service['availability']); ?></p>
                        <p class="muted" style="font-size: 0.8rem;">
                            Posted: <?= isset($service['created_at']) && $service['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $service['created_at']->toDateTime()->format('Y-m-d H:i') : 'N/A'; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
