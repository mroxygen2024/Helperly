<div class="grid grid-cols-3 gap-6">
    <div class="col-span-1">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Add New Service</h2>
            </div>
            <form action="/services" method="POST" class="flex flex-col gap-4">
                <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

                <div class="form-group">
                    <label class="label">Service Title</label>
                    <input name="service_type" type="text" class="input" placeholder="e.g. House Cleaning" required>
                </div>

                <div class="form-group">
                    <label class="label">Price / Rate</label>
                    <input name="price" type="text" class="input" placeholder="e.g. 500 ETB/hour" required>
                </div>

                <div class="form-group">
                    <label class="label">Work Availability</label>
                    <input name="availability" type="text" class="input" placeholder="e.g. Mon-Fri, 9am-5pm" required>
                </div>

                <div class="form-group">
                    <label class="label">Detailed Description</label>
                    <textarea name="description" class="textarea" rows="4" placeholder="Tell customers what's included..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    <span class="material-symbols-outlined">add_circle</span>
                    Publish Offering
                </button>
            </form>
        </div>
    </div>

    <div class="col-span-2">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">My Published Services</h2>
            </div>

            <?php if (empty($myServices)): ?>
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-muted" style="font-size: 3rem;">inventory_2</span>
                    <p class="text-muted mt-2">You haven't listed any specific services yet.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach ($myServices as $service): ?>
                        <div class="card border mb-0">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-600 text-lg"><?= escape((string)$service['service_type']); ?></h3>
                                <div class="text-info font-700"><?= escape((string)$service['price']); ?></div>
                            </div>
                            <p class="text-sm text-muted mb-4"><?= nl2br(escape((string)$service['description'])); ?></p>
                            
                            <div class="flex items-center gap-4 text-xs text-muted border-t pt-3">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">schedule</span>
                                    <?= escape((string)$service['availability']); ?>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">event_available</span>
                                    <?= isset($service['created_at']) && $service['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $service['created_at']->toDateTime()->format('M d, Y') : 'N/A'; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
