<?php $isAvailableJobsPage = (($title ?? '') === 'Available Jobs'); ?>
<?php if ($isAvailableJobsPage): ?>
<?php
$jobCategories = isset($jobCategories) && is_array($jobCategories) ? $jobCategories : [];
$jobs = isset($jobs) && is_array($jobs) ? $jobs : [];
$jobCount = count($jobs);
?>
<div class="container py-12 available-jobs-v2">
    <div class="max-w-7xl mx-auto">
        <!-- Simplified Header -->
        <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mb-12">
            <div class="max-w-2xl">
                <h1 class="text-4xl md:text-5xl font-900 tracking-tight text-slate-900 mb-3"><?= escape($title ?? 'Jobs'); ?></h1>
                <p class="text-lg text-slate-500 font-500 leading-relaxed"><?= escape($subtitle ?? 'Find the perfect opportunities that match your skills.'); ?></p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="available-jobs-tab-wrap bg-slate-100 p-1.5 rounded-2xl inline-flex" role="tablist">
                    <button type="button" class="tab-btn is-active px-6 py-2.5 rounded-xl text-sm font-800 transition-all" data-job-tab="all" aria-pressed="true">All Jobs</button>
                    <button type="button" class="tab-btn px-6 py-2.5 rounded-xl text-sm font-800 text-slate-500 transition-all" data-job-tab="best-match" aria-pressed="false">Best Match</button>
                </div>
                <div class="hidden md:block h-10 w-px bg-slate-200"></div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-800 text-slate-400 uppercase tracking-widest mb-1">Open Now</span>
                    <span class="text-xl font-900 text-slate-900" data-job-total-count><?= (int) $jobCount; ?> Opportunities</span>
                </div>
            </div>
        </header>

        <?php if ($jobCount === 0): ?>
            <div class="card p-20 text-center border-dashed border-2 border-slate-200 bg-slate-50/50 rounded-[40px]">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full shadow-sm mb-8 text-slate-300">
                    <span class="material-symbols-outlined" style="font-size: 3.5rem;">work_history</span>
                </div>
                <h2 class="text-3xl font-900 text-slate-900 mb-3">No jobs available yet</h2>
                <p class="text-slate-500 max-w-sm mx-auto mb-0 text-lg">Check back soon! New opportunities are posted every day.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Main Content (Jobs) -->
                <div class="lg:col-span-8 xl:col-span-9 flex flex-col gap-6">
                    <div class="flex items-center justify-between px-2 mb-2">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-success rounded-full animate-pulse"></span>
                            <span class="text-[11px] font-800 text-slate-400 uppercase tracking-widest" data-job-result-count><?= (int) $jobCount; ?> jobs shown</span>
                        </div>
                        <div class="best-match-hint hidden items-center gap-2 px-3 py-1.5 bg-primary-50 rounded-full border border-primary-100" data-best-match-info>
                            <span class="material-symbols-outlined text-primary" style="font-size: 16px;">verified</span>
                            <span class="text-[11px] font-800 text-primary-700 uppercase">Personalized for you</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-6" data-job-grid>
                        <?php foreach ($jobs as $job): ?>
                            <?php
                            $categoryLabel = trim((string) ($job['service_type'] ?? 'Uncategorized'));
                            $categorySlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $categoryLabel) ?? 'uncategorized');
                            $categorySlug = trim($categorySlug, '-') ?: 'uncategorized';
                            $createdAtValue = 0;
                            if (isset($job['created_at']) && $job['created_at'] instanceof \MongoDB\BSON\UTCDateTime) {
                                $createdAtValue = (int) $job['created_at']->toDateTime()->format('U');
                            } elseif (isset($job['created_at']) && is_string($job['created_at'])) {
                                $createdAtValue = strtotime($job['created_at']) ?: 0;
                            }

                            $instructions = (string)($job['instructions'] ?? '');
                            $instructionsSnippet = mb_strlen($instructions) > 160 ? mb_substr($instructions, 0, 157) . '...' : $instructions;
                            $matchScore = (int) ($job['match_score'] ?? 0);
                            ?>
                            <article class="job-card-v2 group bg-white rounded-[32px] p-8 hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 border border-slate-100 relative overflow-hidden" 
                                data-job-card 
                                data-category="<?= escape($categorySlug); ?>" 
                                data-match-score="<?= $matchScore; ?>" 
                                data-created-at="<?= (int) $createdAtValue; ?>"
                                data-location="<?= escape(strtolower((string)($job['location'] ?? ''))); ?>"
                                data-budget="<?= (float)($job['total_cost'] ?? 0); ?>">
                                <div class="flex flex-col md:flex-row justify-between gap-8">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-3 mb-5">
                                            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-800 uppercase tracking-wider"><?= escape($categoryLabel); ?></span>
                                            <div class="flex items-center gap-2 px-2.5 py-1 bg-success/5 text-success rounded-lg border border-success/10">
                                                <span class="text-[10px] font-900"><?= $matchScore; ?>% Match</span>
                                                <div class="w-10 h-1 bg-success/20 rounded-full overflow-hidden hidden sm:block">
                                                    <div class="h-full bg-success" style="width: <?= $matchScore; ?>%"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <h3 class="text-2xl font-900 text-slate-900 mb-4 group-hover:text-primary transition-colors cursor-pointer" onclick="location.href='/jobs/detail?id=<?= escape((string) $job['_id']); ?>'"><?= escape((string) ($job['service_type'] ?? 'Job')); ?></h3>
                                        
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-6 gap-x-8 mb-6">
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-800 text-slate-400 uppercase tracking-widest mb-1">Budget</span>
                                                <span class="text-base font-800 text-slate-900"><?= number_format((float) ($job['total_cost'] ?? 0), 2); ?> ETB</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-800 text-slate-400 uppercase tracking-widest mb-1">Duration</span>
                                                <span class="text-base font-800 text-slate-900"><?= (float) ($job['duration'] ?? 0); ?> Hours</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-800 text-slate-400 uppercase tracking-widest mb-1">Location</span>
                                                <span class="text-base font-800 text-slate-900 truncate"><?= escape((string) ($job['location'] ?? 'N/A')); ?></span>
                                            </div>
                                        </div>

                                        <?php if (!empty($instructionsSnippet)): ?>
                                            <p class="text-slate-500 text-sm leading-relaxed mb-0 line-clamp-2 pr-4"><?= escape($instructionsSnippet); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex flex-row md:flex-col justify-between md:justify-end items-center md:items-end gap-4 shrink-0 pt-6 md:pt-0 border-t md:border-t-0 border-slate-50">
                                        <div class="text-right hidden md:block">
                                            <span class="text-[10px] font-800 text-slate-400 uppercase tracking-widest block mb-1">Posted On</span>
                                            <span class="text-sm font-700 text-slate-600"><?= isset($job['created_at']) ? (is_string($job['created_at']) ? date('M d, Y', strtotime($job['created_at'])) : ($job['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $job['created_at']->toDateTime()->format('M d, Y') : 'N/A')) : 'N/A'; ?></span>
                                        </div>
                                        <div class="flex gap-2 w-full md:w-auto">
                                            <a href="/jobs/detail?id=<?= escape((string) $job['_id']); ?>" class="flex-1 md:flex-none px-6 py-3 bg-slate-50 text-slate-700 rounded-xl text-xs font-800 hover:bg-slate-100 transition-all text-center">Details</a>
                                            <a href="/messages?job_id=<?= escape((string) $job['_id']); ?>" class="flex-1 md:flex-none px-6 py-3 bg-primary text-white rounded-xl text-xs font-800 hover:shadow-lg hover:shadow-primary/20 transition-all text-center">Chat Now</a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Empty State for Filters -->
                    <div class="hidden py-20 text-center bg-white border border-slate-100 rounded-[40px] shadow-sm" data-job-no-results>
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                            <span class="material-symbols-outlined" style="font-size: 2.5rem;">search_off</span>
                        </div>
                        <h3 class="text-2xl font-900 text-slate-900 mb-2">No jobs match your filters</h3>
                        <p class="text-slate-500 max-w-xs mx-auto mb-0">Try adjusting your category or switch back to "All Jobs" to see more results.</p>
                    </div>
                </div>

                <!-- Filters Sidebar -->
                <aside class="lg:col-span-4 xl:col-span-3">
                    <div class="sticky top-10 flex flex-col gap-8">
                        <div class="bg-white rounded-[32px] p-8 shadow-sm border border-slate-100">
                            <div class="mb-8">
                                <h2 class="text-xl font-900 text-slate-900 mb-1">Browse by Category</h2>
                                <p class="text-xs font-600 text-slate-400 uppercase tracking-wider">Find work you excel at</p>
                            </div>

                            <div class="flex flex-col gap-2" data-job-category-filters>
                                <button type="button" class="category-filter-btn is-active group w-full flex items-center justify-between p-4 rounded-2xl transition-all hover:bg-slate-50" data-category-filter="all">
                                    <span class="text-sm font-800 text-slate-700 group-[.is-active]:text-primary transition-colors">All Categories</span>
                                    <span class="px-2 py-1 bg-slate-100 rounded-lg text-[10px] font-800 text-slate-500 group-[.is-active]:bg-primary-50 group-[.is-active]:text-primary-700 transition-all">All</span>
                                </button>
                                <?php foreach ($jobCategories as $category): ?>
                                    <button type="button" class="category-filter-btn group w-full flex items-center justify-between p-4 rounded-2xl transition-all hover:bg-slate-50" data-category-filter="<?= escape((string) ($category['slug'] ?? '')); ?>">
                                        <span class="text-sm font-800 text-slate-700 group-[.is-active]:text-primary transition-colors"><?= escape((string) ($category['label'] ?? 'Category')); ?></span>
                                        <span class="px-2 py-1 bg-slate-100 rounded-lg text-[10px] font-800 text-slate-500 group-[.is-active]:bg-primary-50 group-[.is-active]:text-primary-700 transition-all"><?= (int) ($category['count'] ?? 0); ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <!-- Mini Pro Tip Card -->
                            <div class="mt-10 p-6 bg-primary-50/50 rounded-2xl border border-primary-100/50">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="material-symbols-outlined text-primary text-[20px]">tips_and_updates</span>
                                    <h4 class="text-xs font-900 text-primary-900 uppercase tracking-widest m-0">Pro Tip</h4>
                                </div>
                                <p class="text-xs text-primary-800/70 font-600 leading-relaxed mb-4">Complete your servant profile to unlock high-score job matches tailored to your specific skills.</p>
                                <a href="/profile/servant" class="text-[10px] font-900 text-primary uppercase tracking-widest hover:underline">Update Profile &rarr;</a>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <script>
                (() => {
                    const grid = document.querySelector('[data-job-grid]');
                    const noResults = document.querySelector('[data-job-no-results]');
                    const resultCount = document.querySelector('[data-job-result-count]');
                    const bestMatchInfo = document.querySelector('[data-best-match-info]');
                    if (!grid) return;

                    const cards = Array.from(grid.querySelectorAll('[data-job-card]'));
                    const tabButtons = Array.from(document.querySelectorAll('[data-job-tab]'));
                    const categoryButtons = Array.from(document.querySelectorAll('[data-category-filter]'));

                    let activeTab = 'all';
                    let activeCategory = 'all';

                    const setActiveTab = (tab) => {
                        activeTab = tab;
                        tabButtons.forEach(btn => {
                            const isActive = btn.dataset.jobTab === tab;
                            btn.classList.toggle('is-active', isActive);
                            btn.classList.toggle('bg-white', isActive);
                            btn.classList.toggle('shadow-sm', isActive);
                            btn.classList.toggle('text-primary', isActive);
                            btn.classList.toggle('text-slate-500', !isActive);
                            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                        });
                        if (bestMatchInfo) bestMatchInfo.classList.toggle('hidden', tab !== 'best-match');
                        if (bestMatchInfo) bestMatchInfo.classList.toggle('flex', tab === 'best-match');
                    };

                    const setActiveCategory = (category) => {
                        activeCategory = category;
                        categoryButtons.forEach(btn => {
                            btn.classList.toggle('is-active', btn.dataset.categoryFilter === category);
                        });
                    };

                    const render = () => {
                        const visibleCards = cards
                            .filter(card => activeCategory === 'all' || card.dataset.category === activeCategory)
                            .sort((a, b) => {
                                if (activeTab === 'best-match') {
                                    const scoreDiff = Number(b.dataset.matchScore || 0) - Number(a.dataset.matchScore || 0);
                                    if (scoreDiff !== 0) return scoreDiff;
                                }
                                return Number(b.dataset.createdAt || 0) - Number(a.dataset.createdAt || 0);
                            });

                        cards.forEach(card => card.classList.add('hidden'));
                        visibleCards.forEach(card => {
                            card.classList.remove('hidden');
                            grid.appendChild(card);
                        });

                        if (noResults) noResults.classList.toggle('hidden', visibleCards.length !== 0);
                        if (resultCount) resultCount.textContent = `${visibleCards.length} job${visibleCards.length === 1 ? '' : 's'} shown`;
                    };

                    tabButtons.forEach(btn => {
                        btn.addEventListener('click', () => {
                            setActiveTab(btn.dataset.jobTab);
                            render();
                        });
                    });

                    categoryButtons.forEach(btn => {
                        btn.addEventListener('click', () => {
                            setActiveCategory(btn.dataset.categoryFilter);
                            render();
                        });
                    });

                    setActiveTab('all');
                    setActiveCategory('all');
                    render();
                })();
            </script>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>
<div class="container py-8">
    <div class="max-w-5xl mx-auto">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-800"><?= escape($title ?? 'Jobs'); ?></h1>
                <p class="text-muted mt-1"><?= escape($subtitle ?? 'Manage and track your work and applications.'); ?></p>
            </div>
            <?php if (($user['role'] ?? '') === 'parent'): ?>
                <a href="/dashboard" class="btn btn-primary">
                    <span class="material-symbols-outlined">add</span> Post New Job
                </a>
            <?php endif; ?>
        </header>

        <?php if (empty($jobs) && empty($applications)): ?>
            <div class="card p-16 text-center border-dashed border-2 bg-slate-50/50">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full shadow-sm mb-6 text-slate-300">
                    <span class="material-symbols-outlined" style="font-size: 3rem;">work_history</span>
                </div>
                <h2 class="text-2xl font-800 text-slate-900 mb-2">No active work found</h2>
                <p class="text-slate-500 max-w-sm mx-auto mb-8">When you have active jobs or pending applications, they will be tracked here in detail.</p>
                <div class="flex justify-center gap-4">
                    <a href="/dashboard" class="btn btn-primary px-8 font-700">Go to Dashboard</a>
                    <?php if (($user['role'] ?? '') === 'parent'): ?>
                        <a href="/servants" class="btn btn-outline px-8 font-700">Find Providers</a>
                    <?php else: ?>
                        <a href="/jobs/available" class="btn btn-outline px-8 font-700">Browse Jobs</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="flex flex-col gap-4">
                <!-- Job List -->
                <?php if (!empty($jobs)): ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="card p-6 hover:shadow-md transition-all cursor-pointer border-l-4 border-l-primary" onclick="location.href='/jobs/detail?id=<?= escape((string)$job['_id']); ?>'">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="badge badge-<?= $job['status'] === 'completed' ? 'success' : ($job['status'] === 'active' ? 'warning' : 'info'); ?>">
                                            <?= escape(ucfirst($job['status'])); ?>
                                        </span>
                                        <span class="text-xs text-muted font-600 uppercase tracking-widest">Job #<?= substr((string)$job['_id'], -6); ?></span>
                                    </div>
                                    <h3 class="text-xl font-800"><?= escape($job['service_type']); ?></h3>
                                    <p class="text-muted flex items-center gap-1 mt-1 text-sm">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                                        <?= escape($job['location']); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-800 text-primary"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> ETB</p>
                                    <p class="text-xs text-muted mt-1"><?= (float)($job['duration'] ?? 0); ?> hours</p>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-between items-center border-t pt-4">
                                <div class="flex items-center gap-4">
                                    <div class="text-xs">
                                        <p class="text-muted font-600 mb-0.5">Posted On</p>
                                        <p class="font-700"><?= isset($job['created_at']) ? (is_string($job['created_at']) ? date('M d, Y', strtotime($job['created_at'])) : ($job['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $job['created_at']->toDateTime()->format('M d, Y') : 'N/A')) : 'N/A'; ?></p>
                                    </div>
                                    <?php if (isset($job['time'])): ?>
                                        <div class="text-xs">
                                            <p class="text-muted font-600 mb-0.5">Scheduled For</p>
                                            <p class="font-700"><?= is_string($job['time']) ? date('M d, Y', strtotime($job['time'])) : ($job['time'] instanceof \MongoDB\BSON\UTCDateTime ? $job['time']->toDateTime()->format('M d, Y h:i A') : 'N/A'); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex gap-2">
                                    <a href="/jobs/detail?id=<?= escape((string)$job['_id']); ?>" class="btn btn-outline btn-sm">View Details</a>
                                    <a href="/messages?job_id=<?= escape((string)$job['_id']); ?>" class="btn btn-ghost btn-sm">Chat</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Applications List -->
                <?php if (!empty($applications)): ?>
                    <?php foreach ($applications as $app): ?>
                        <div class="card p-6 hover:shadow-md transition-all cursor-pointer border-l-4 border-l-info" onclick="location.href='/jobs/detail?id=<?= escape((string)$app['job_id']); ?>'">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="badge badge-<?= $app['status'] === 'accepted' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'info'); ?>">
                                            Application <?= escape(ucfirst($app['status'])); ?>
                                        </span>
                                        <span class="text-xs text-muted font-600 uppercase tracking-widest">App #<?= substr((string)$app['_id'], -6); ?></span>
                                    </div>
                                    <h3 class="text-xl font-800"><?= escape($app['job_data']['service_type'] ?? 'Job'); ?></h3>
                                    <p class="text-muted flex items-center gap-1 mt-1 text-sm">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                                        <?= escape($app['job_data']['location'] ?? 'N/A'); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-800 text-info">Pending</p>
                                    <p class="text-xs text-muted mt-1">Applied <?= isset($app['created_at']) ? (is_string($app['created_at']) ? date('M d', strtotime($app['created_at'])) : ($app['created_at'] instanceof \MongoDB\BSON\UTCDateTime ? $app['created_at']->toDateTime()->format('M d') : 'N/A')) : 'N/A'; ?></p>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-between items-center border-t pt-4">
                                <div class="text-xs">
                                    <p class="text-muted font-600 mb-0.5">Budget</p>
                                    <p class="font-700"><?= number_format((float)($app['job_data']['total_cost'] ?? 0), 2); ?> ETB</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="/jobs/detail?id=<?= escape((string)$app['job_id']); ?>" class="btn btn-outline btn-sm">View Job Posting</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
