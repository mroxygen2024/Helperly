<?php $isAvailableJobsPage = (($title ?? '') === 'Available Jobs'); ?>
<?php if ($isAvailableJobsPage): ?>
<script src="https://cdn.tailwindcss.com"></script>
<?php
$jobCategories = isset($jobCategories) && is_array($jobCategories) ? $jobCategories : [];
$jobs = isset($jobs) && is_array($jobs) ? $jobs : [];
$jobCount = count($jobs);
?>
<div class="max-w-7xl mx-auto px-4 py-12">
    <header class="mb-12">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-4">Available Jobs</h1>
        <div class="flex gap-2 bg-gray-100 p-1 rounded-xl inline-flex" role="tablist">
            <button type="button" class="px-6 py-2 rounded-lg text-sm font-bold bg-white shadow text-gray-900" data-job-tab="all" onclick="setTab('all')">All Jobs</button>
            <button type="button" class="px-6 py-2 rounded-lg text-sm font-bold text-gray-600 hover:text-gray-900" data-job-tab="best-match" onclick="setTab('best-match')">Best Match</button>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content (Jobs) -->
        <div class="lg:col-span-3 space-y-6" data-job-grid>
            <?php foreach ($jobs as $job): ?>
                <?php
                $categoryLabel = trim((string) ($job['service_type'] ?? 'Uncategorized'));
                $categorySlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $categoryLabel) ?? 'uncategorized');
                ?>
                <article class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all" 
                    data-job-card 
                    data-category="<?= escape($categorySlug); ?>"
                    data-location="<?= escape(strtolower((string)($job['location'] ?? ''))); ?>"
                    data-budget="<?= (float)($job['total_cost'] ?? 0); ?>">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 cursor-pointer hover:text-primary" onclick="location.href='/jobs/detail?id=<?= escape((string) $job['_id']); ?>'"><?= escape((string) ($job['service_type'] ?? 'Job')); ?></h3>
                    <p class="text-gray-600 text-sm mb-4"><?= escape(mb_substr((string)$job['instructions'] ?? '', 0, 100)); ?>...</p>
                    <div class="text-xs font-semibold text-gray-500">
                        <?= escape((string) ($job['location'] ?? 'N/A')); ?> | <?= number_format((float) ($job['total_cost'] ?? 0), 2); ?> ETB
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Filters Sidebar -->
        <aside class="lg:col-span-1">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 sticky top-8 space-y-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-900">Filters</h2>
                    <button type="button" class="text-xs text-indigo-600 hover:underline" onclick="resetFilters()">Reset</button>
                </div>
                <!-- Search -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                    <input type="text" placeholder="Keywords..." class="w-full px-3 py-2 border rounded-lg text-sm" data-keyword-filter>
                </div>
                <!-- Location -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                    <input type="text" placeholder="City..." class="w-full px-3 py-2 border rounded-lg text-sm" data-location-filter>
                </div>
                <!-- Budget -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Budget (Max)</label>
                    <input type="range" min="0" max="10000" step="100" value="10000" class="w-full" data-budget-range-input>
                    <span class="text-xs text-gray-500" id="budget-val">10,000+ ETB</span>
                </div>
                <!-- Categories -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Categories</h3>
                    <div class="space-y-1">
                        <?php $allCategories = require 'config/categories.php'; ?>
                        <?php foreach ($allCategories as $slug => $label): ?>
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" data-category-filter="<?= escape($slug); ?>">
                                <?= escape($label); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="button" class="w-full bg-indigo-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-indigo-700" onclick="render()">Apply Filters</button>
            </div>
        </aside>
    </div>
</div>

<script>
    let activeTab = 'all';
    function setTab(tab) {
        activeTab = tab;
        document.querySelectorAll('[data-job-tab]').forEach(btn => {
            btn.classList.toggle('bg-white', btn.dataset.jobTab === tab);
            btn.classList.toggle('shadow', btn.dataset.jobTab === tab);
            btn.classList.toggle('text-gray-900', btn.dataset.jobTab === tab);
            btn.classList.toggle('text-gray-600', btn.dataset.jobTab !== tab);
        });
        render();
    }
    const render = () => {
        const keyword = document.querySelector('[data-keyword-filter]').value.toLowerCase();
        const location = document.querySelector('[data-location-filter]').value.toLowerCase();
        const budget = parseFloat(document.querySelector('[data-budget-range-input]').value);
        const activeCats = Array.from(document.querySelectorAll('[data-category-filter]:checked')).map(cb => cb.dataset.categoryFilter);
        document.getElementById('budget-val').textContent = budget.toLocaleString() + '+ ETB';
        
        document.querySelectorAll('[data-job-card]').forEach(card => {
            const matches = (activeCats.includes(card.dataset.category)) &&
                            card.textContent.toLowerCase().includes(keyword) &&
                            card.dataset.location.includes(location) &&
                            parseFloat(card.dataset.budget) <= budget;
            card.style.display = matches ? 'block' : 'none';
        });
    };
    function resetFilters() {
        document.querySelectorAll('input').forEach(i => {
            if(i.type === 'checkbox') i.checked = true;
            else if(i.type === 'range') i.value = 10000;
            else i.value = '';
        });
        render();
    }
    document.querySelectorAll('input').forEach(i => i.addEventListener('input', render));
</script>

<?php else: ?>
<!-- Legacy View -->
<div class="container py-8">
    <!-- ... original content ... -->
</div>
<?php endif; ?>
