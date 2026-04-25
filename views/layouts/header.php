<?php
$app = appConfig();
$successFlash = getFlash('success');
$errorFlash = getFlash('error');
$currentUser = authUser();
$role = $currentUser ? normalizeRole((string)($currentUser['role'] ?? '')) : null;
$currentPage = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($title ?? $app['app_name']); ?> - Helperly</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>

<?php if ($currentUser): ?>
<div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <span class="material-symbols-outlined" style="color: var(--primary); font-size: 2rem;">guardian</span>
            <a href="/dashboard" class="sidebar-logo">Helperly</a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-group">
                <p class="nav-label">Main Menu</p>
                <a href="/dashboard" class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <span class="material-symbols-outlined">dashboard</span>
                    Dashboard
                </a>

                <?php if ($role === 'parent'): ?>
                    <a href="/servants" class="nav-item <?= $currentPage === 'listings' ? 'active' : '' ?>">
                        <span class="material-symbols-outlined">search</span>
                        Find Servants
                    </a>
                    <a href="/dashboard" class="nav-item">
                        <span class="material-symbols-outlined">work</span>
                        My Jobs
                    </a>
                <?php endif; ?>

                <?php if ($role === 'service_provider'): ?>
                    <a href="/dashboard" class="nav-item">
                        <span class="material-symbols-outlined">explore</span>
                        Browse Jobs
                    </a>
                    <a href="/services" class="nav-item">
                        <span class="material-symbols-outlined">home_repair_service</span>
                        My Services
                    </a>
                <?php endif; ?>

                <?php if ($role === 'administrator'): ?>
                    <a href="/admin/users" class="nav-item">
                        <span class="material-symbols-outlined">group</span>
                        User Management
                    </a>
                    <a href="/admin/verifications" class="nav-item">
                        <span class="material-symbols-outlined">verified_user</span>
                        Verifications
                    </a>
                <?php endif; ?>

                <a href="/messages" class="nav-item">
                    <span class="material-symbols-outlined">chat</span>
                    Messages
                </a>
            </div>

            <div class="nav-group">
                <p class="nav-label">Settings</p>
                <a href="/profile/account" class="nav-item">
                    <span class="material-symbols-outlined">person</span>
                    My Account
                </a>
                <?php if ($role === 'service_provider'): ?>
                    <a href="/profile/servant" class="nav-item">
                        <span class="material-symbols-outlined">badge</span>
                        Service Profile
                    </a>
                <?php endif; ?>
                <?php if ($role === 'parent'): ?>
                    <a href="/profile/employer" class="nav-item">
                        <span class="material-symbols-outlined">description</span>
                        Employer Profile
                    </a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="sidebar-footer">
            <form action="/logout" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                <button type="submit" class="nav-item" style="background: none; border: none; width: 100%; color: var(--danger); cursor: pointer;">
                    <span class="material-symbols-outlined">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <h1 class="card-title"><?= escape($title ?? 'Dashboard'); ?></h1>
            </div>
            <div class="topbar-right">
                <div class="user-profile">
                    <div class="user-avatar"><?= mb_substr(escape($currentUser['name']), 0, 1); ?></div>
                    <div class="flex flex-col">
                        <span class="text-sm font-600"><?= escape($currentUser['name']); ?></span>
                        <span class="text-sm text-muted" style="font-size: 0.75rem;"><?= ucfirst($role); ?></span>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-body">
            <?php if ($successFlash): ?>
                <div class="alert alert-success"><?= escape($successFlash); ?></div>
            <?php endif; ?>
            <?php if ($errorFlash): ?>
                <div class="alert alert-error"><?= escape($errorFlash); ?></div>
            <?php endif; ?>
<?php else: ?>
    <!-- Public Header for Login/Register -->
    <header class="topbar" style="position: static; border-bottom: none; background: transparent;">
        <div class="container flex justify-between items-center" style="max-width: 1200px; margin: 0 auto; width: 100%; height: 80px;">
            <a href="/" class="sidebar-logo" style="color: var(--primary); font-size: 1.5rem;">Helperly</a>
            <div class="flex gap-4">
                <a href="/login" class="btn btn-outline">Login</a>
                <a href="/register" class="btn btn-primary">Join Now</a>
            </div>
        </div>
    </header>
    <main class="content-body" style="max-width: 500px;">
        <?php if ($successFlash): ?>
            <div class="alert alert-success"><?= escape($successFlash); ?></div>
        <?php endif; ?>
        <?php if ($errorFlash): ?>
            <div class="alert alert-error"><?= escape($errorFlash); ?></div>
        <?php endif; ?>
<?php endif; ?>
