<?php $isAvailableJobsPage = (($title ?? '') === 'Available Jobs'); ?>
<?php if ($isAvailableJobsPage): ?>
<?php
$jobCategories = isset($jobCategories) && is_array($jobCategories) ? $jobCategories : [];
$jobs = isset($jobs) && is_array($jobs) ? $jobs : [];
$jobCount = count($jobs);
?>
<div class="available-jobs-v2">
    <div class="max-w-[1400px] mx-auto">
        <div class="available-jobs-layout">
            <!-- Main Content (Jobs) -->
            <div class="jobs-content-area" data-job-grid>
                <header class="mb-10 space-y-8">
                    <div class="text-center">
                        <h1 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">Available Jobs</h1>
                        <p class="text-gray-500 font-medium">Discover opportunities that match your skills</p>
                    </div>
                    <div class="flex items-center justify-start">
                        <div class="flex gap-2 bg-gray-100 p-1.5 rounded-2xl inline-flex shadow-inner" role="tablist">
                            <button type="button" class="px-8 py-2.5 rounded-xl text-sm font-bold bg-white shadow-sm text-primary transition-all duration-300" data-job-tab="all" onclick="setTab('all')">All Jobs</button>
                            <button type="button" class="px-8 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:text-gray-900 transition-all duration-300" data-job-tab="best-match" onclick="setTab('best-match')">Best Match</button>
                        </div>
                    </div>
                </header>

                <?php if (empty($jobs)): ?>
                    <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-gray-300">
                        <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">work_off</span>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No jobs available right now</h3>
                        <p class="text-gray-500">Check back later or adjust your filters.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                        <?php
                        $categoryLabel = trim((string) ($job['service_type'] ?? 'Uncategorized'));
                        $categorySlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $categoryLabel) ?? 'uncategorized');
                        ?>
                        <article class="job-card-v2 bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:border-primary-100 transition-all cursor-pointer relative overflow-hidden group" 
                            data-job-card 
                            data-category="<?= escape($categorySlug); ?>"
                            data-location="<?= escape(strtolower((string)($job['location'] ?? ''))); ?>"
                            data-budget="<?= (float)($job['total_cost'] ?? 0); ?>"
                            onclick="location.href='/jobs/detail?id=<?= escape((string) $job['_id']); ?>'">
                            
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <span class="category-badge inline-block px-3 py-1 rounded-full bg-primary-50 text-primary text-[10px] font-extrabold uppercase mb-3"><?= escape($categoryLabel); ?></span>
                                    <h3 class="text-2xl font-extrabold text-gray-900 group-hover:text-primary transition-colors"><?= escape((string) ($job['service_type'] ?? 'Job')); ?></h3>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-black text-primary leading-none"><?= number_format((float) ($job['total_cost'] ?? 0), 0); ?> <span class="text-xs font-bold text-gray-400">ETB</span></div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Total Budget</div>
                                </div>
                            </div>

                            <p class="text-gray-600 text-sm mb-6 leading-relaxed line-clamp-2"><?= escape((string)($job['instructions'] ?? 'No description provided.')); ?></p>
                            
                            <div class="flex flex-wrap items-center gap-6 pt-6 border-t border-gray-50">
                                <div class="flex items-center gap-2 text-gray-500">
                                    <span class="material-symbols-outlined text-sm">location_on</span>
                                    <span class="text-xs font-bold"><?= escape((string) ($job['location'] ?? 'N/A')); ?></span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-500">
                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                    <span class="text-xs font-bold"><?= isset($job['created_at']) ? date('M d, Y', strtotime($job['created_at'])) : 'Recently'; ?></span>
                                </div>
                                <?php if (isset($job['match_score'])): ?>
                                    <div class="ml-auto flex items-center gap-2 bg-success-light px-3 py-1 rounded-full">
                                        <div class="w-2 h-2 rounded-full bg-success"></div>
                                        <span class="text-[10px] font-black text-success-dark uppercase"><?= round($job['match_score']); ?>% Match</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Filters Sidebar -->
            <aside class="filters-sidebar-area">
                <div class="filters-sticky-wrapper">
                    <div class="modern-filter-card">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-xl font-black text-gray-900">Filters</h2>
                            <button type="button" class="text-xs font-bold text-primary hover:underline transition-all" onclick="resetFilters()">Reset All</button>
                        </div>
                        
                        <!-- Search -->
                        <div class="filter-section">
                            <label class="filter-label">Keyword</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                                <input type="text" placeholder="Search jobs..." class="filter-input pl-10" data-keyword-filter>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="filter-section">
                            <label class="filter-label">Location</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">location_on</span>
                                <input type="text" placeholder="City or region..." class="filter-input pl-10" data-location-filter>
                            </div>
                        </div>

                        <!-- Budget -->
                        <div class="filter-section">
                            <div class="flex justify-between items-center mb-3">
                                <label class="filter-label mb-0">Max Budget</label>
                                <span class="text-xs font-black text-primary" id="budget-val">10,000+ ETB</span>
                            </div>
                            <input type="range" min="0" max="10000" step="500" value="10000" class="w-full accent-primary h-1.5 bg-gray-100 rounded-lg appearance-none cursor-pointer" data-budget-range-input>
                            <div class="flex justify-between mt-2 text-[10px] font-bold text-gray-400 uppercase">
                                <span>0</span>
                                <span>10k+</span>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="filter-section">
                            <label class="filter-label">Categories</label>
                            <div class="category-checkbox-group">
                                <?php $allCategories = require 'config/categories.php'; ?>
                                <?php foreach ($allCategories as $slug => $label): ?>
                                    <label class="category-checkbox-label">
                                        <input type="checkbox" data-category-filter="<?= escape($slug); ?>">
                                        <span><?= escape($label); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="button" class="w-full bg-primary text-white py-4 rounded-2xl text-sm font-black hover:bg-primary-hover shadow-lg shadow-primary-glow transition-all active:scale-95" onclick="render()">Apply Filters</button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<script>
    let activeTab = 'all';
    function setTab(tab) {
        activeTab = tab;
        document.querySelectorAll('[data-job-tab]').forEach(btn => {
            const isActive = btn.dataset.jobTab === tab;
            btn.classList.toggle('bg-white', isActive);
            btn.classList.toggle('shadow-sm', isActive);
            btn.classList.toggle('text-primary', isActive);
            btn.classList.toggle('text-gray-500', !isActive);
        });
        render();
    }
    const render = () => {
        const keyword = document.querySelector('[data-keyword-filter]').value.toLowerCase();
        const location = document.querySelector('[data-location-filter]').value.toLowerCase();
        const budget = parseFloat(document.querySelector('[data-budget-range-input]').value);
        const activeCats = Array.from(document.querySelectorAll('[data-category-filter]:checked')).map(cb => cb.dataset.categoryFilter);
        
        document.getElementById('budget-val').textContent = (budget >= 10000 ? '10,000+' : budget.toLocaleString()) + ' ETB';
        
        document.querySelectorAll('[data-job-card]').forEach(card => {
            const cardCategory = card.dataset.category;
            const cardText = card.textContent.toLowerCase();
            const cardLocation = card.dataset.location;
            const cardBudget = parseFloat(card.dataset.budget);

            const matchesCategory = activeCats.length === 0 || activeCats.includes(cardCategory);
            const matchesKeyword = !keyword || cardText.includes(keyword);
            const matchesLocation = !location || cardLocation.includes(location);
            const matchesBudget = cardBudget <= budget;

            card.style.display = (matchesCategory && matchesKeyword && matchesLocation && matchesBudget) ? 'block' : 'none';
        });
    };
    function resetFilters() {
        document.querySelector('[data-keyword-filter]').value = '';
        document.querySelector('[data-location-filter]').value = '';
        document.querySelector('[data-budget-range-input]').value = 10000;
        document.querySelectorAll('[data-category-filter]').forEach(cb => cb.checked = false);
        render();
    }
    
    // Add event listeners for instant feedback
    document.querySelectorAll('.filter-input, [data-budget-range-input]').forEach(el => {
        el.addEventListener('input', render);
    });
    document.querySelectorAll('[data-category-filter]').forEach(cb => {
        cb.addEventListener('change', render);
    });
</script>

<?php else: ?>
<!-- Legacy View -->
<div class="container py-8">
    <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
        <h2 class="text-2xl font-bold text-gray-900 mb-2"><?= escape($title); ?></h2>
        <p class="text-gray-500"><?= escape($subtitle ?? ''); ?></p>
        <div class="mt-8 space-y-4 max-w-2xl mx-auto">
            <?php foreach ($jobs ?? [] as $job): ?>
                 <div class="p-4 border rounded-xl text-left hover:border-primary transition-all cursor-pointer" onclick="location.href='/jobs/detail?id=<?= escape((string) $job['_id']); ?>'">
                    <h3 class="font-bold"><?= escape((string) ($job['service_type'] ?? 'Job')); ?></h3>
                    <p class="text-sm text-gray-500"><?= escape((string) ($job['location'] ?? 'N/A')); ?></p>
                 </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
