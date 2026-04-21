<?php
/*
 |--------------------------------------------------------------------------
 | views/ purpose
 |--------------------------------------------------------------------------
 | Presentation-only templates. Keep business logic out of this folder.
 */

$app = appConfig();
$successFlash = getFlash('success');
$errorFlash = getFlash('error');
$currentUser = authUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($title ?? $app['app_name']); ?> - <?= escape($app['app_name']); ?></title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<header class="topbar">
    <div class="container topbar-inner">
        <div class="brand-block">
            <a class="brand" href="/?page=dashboard">Servant Marketplace</a>
            <p class="brand-tagline">Trusted help, faster hiring, smoother coordination.</p>
        </div>
        <nav>
            <?php if ($currentUser): ?>
                <span class="muted">Signed in as <?= escape($currentUser['name']); ?></span>
                <a href="/?page=dashboard">Dashboard</a>
                <a href="/?page=profiles">Profiles</a>
                <a href="/?page=listings">Listings</a>
                <?php if (normalizeRole((string) ($currentUser['role'] ?? '')) === 'service_provider'): ?>
                    <a href="/servant/requests">Requests</a>
                <?php endif; ?>
                <form action="/logout" method="POST" class="inline-form">
                    <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                    <button type="submit" class="btn btn-secondary">Logout</button>
                </form>
            <?php else: ?>
                <a href="/?page=login">Login</a>
                <a href="/?page=register">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
    <?php if ($successFlash): ?>
        <div class="alert success"><?= escape($successFlash); ?></div>
    <?php endif; ?>
    <?php if ($errorFlash): ?>
        <div class="alert error"><?= escape($errorFlash); ?></div>
    <?php endif; ?>
