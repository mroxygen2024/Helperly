<div class="container py-8">
    <div class="max-w-3xl mx-auto">
        <nav class="mb-6">
            <a href="/dashboard" class="flex items-center text-primary font-600 hover:underline gap-1">
                <span class="material-symbols-outlined">arrow_back</span>
                Back to Dashboard
            </a>
        </nav>

        <div class="card p-8 shadow-premium border-none">
            <div class="mb-8">
                <h1 class="text-3xl font-800 mb-2">Apply for <?= escape($job['service_type']); ?></h1>
                <p class="text-muted">Submit your application to the client.</p>
            </div>

            <div class="bg-primary-50 border-2 border-primary-100 rounded-2xl p-6 mb-8 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase text-primary-400 tracking-widest mb-1">Total Budget</p>
                    <p class="text-3xl font-black text-primary m-0"><?= number_format((float)($job['total_cost'] ?? 0), 2); ?> <span class="text-sm">ETB</span></p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-primary-600 m-0"><?= (float)($job['duration'] ?? 0); ?> Hours</p>
                    <p class="text-xs font-bold text-primary-400">Estimated Duration</p>
                </div>
            </div>

            <form action="/jobs/apply" method="POST" class="flex flex-col gap-6">
                <input type="hidden" name="csrf_token" value="<?= escape($csrfToken); ?>">
                <input type="hidden" name="job_id" value="<?= escape((string)$job['_id']); ?>">

                <p class="text-sm text-muted m-0">
                    Your provider profile details will be sent to the parent with this application.
                </p>

                <div class="pt-6 border-t mt-4 flex gap-4">
                    <button type="submit" class="btn btn-primary btn-lg flex-1 shadow-premium">
                        Apply Now
                        <span class="material-symbols-outlined ml-2">send</span>
                    </button>
                    <a href="/dashboard" class="btn btn-outline btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
