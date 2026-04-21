<section class="card">
    <h1><?= escape((string) ($title ?? 'Not Found')); ?></h1>
    <p class="muted"><?= escape((string) ($message ?? 'The page you requested could not be found.')); ?></p>
    <p>
        <a href="/?page=dashboard">Go to dashboard</a>
    </p>
</section>
