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

<?php if ($currentUser): 
    $navItems = [];
    
    // Define navigation based on role
    if ($role === 'parent') {
        $navItems = [
            'dashboard' => ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'dashboard'],
            'search' => ['label' => 'Find Help', 'url' => '/servants', 'icon' => 'search'],
            'messages' => ['label' => 'Messages', 'url' => '/messages', 'icon' => 'chat'],
        ];
    } elseif ($role === 'provider') {
        $navItems = [
            'dashboard' => ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'dashboard'],
            'profile' => ['label' => 'My Profile', 'url' => '/profile/servant', 'icon' => 'badge'],
            'messages' => ['label' => 'Messages', 'url' => '/messages', 'icon' => 'chat'],
        ];
    } elseif ($role === 'administrator') {
        $navItems = [
            'dashboard' => ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'dashboard'],
            'users' => ['label' => 'User Management', 'url' => '/admin/users', 'icon' => 'group'],
            'providers' => ['label' => 'Providers', 'url' => '/admin/providers', 'icon' => 'badge'],
            'verifications' => ['label' => 'Verifications', 'url' => '/admin/verifications', 'icon' => 'verified_user'],
            'jobs' => ['label' => 'Job Management', 'url' => '/admin/jobs', 'icon' => 'work'],
        ];
    }

    // Determine active item
    $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
    $pathOnly = explode('?', $currentUri)[0];
    $activeKey = '';
    
    foreach ($navItems as $key => $item) {
        $baseUrl = explode('#', $item['url'])[0];
        if ($pathOnly === $baseUrl) {
            $activeKey = $key;
            break;
        }
    }
    
    // Default to dashboard if no match
    if ($activeKey === '' && $pathOnly === '/dashboard') {
        $activeKey = 'dashboard';
    }
?>
<div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <span class="material-symbols-outlined logo-icon">guardian</span>
                <a href="/dashboard" class="sidebar-logo">Helperly</a>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-group">
                <p class="nav-label">Menu</p>
                <?php foreach ($navItems as $key => $item): ?>
                    <a href="<?= escape($item['url']); ?>" class="nav-item <?= $activeKey === $key ? 'active' : '' ?>">
                        <span class="material-symbols-outlined"><?= escape($item['icon']); ?></span>
                        <?= escape($item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="nav-group mt-auto">
                <p class="nav-label">Settings</p>
                <a href="/profile/account" class="nav-item <?= $pathOnly === '/profile/account' ? 'active' : '' ?>">
                    <span class="material-symbols-outlined">settings</span>
                    Account Settings
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <form action="/logout" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()); ?>">
                <button type="submit" class="logout-btn">
                    <span class="material-symbols-outlined">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="sidebar-toggle-btn" aria-label="Toggle Navigation">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h1 class="page-title"><?= escape($title ?? 'Dashboard'); ?></h1>
            </div>
            <div class="topbar-right">
                <div class="flex items-center gap-4 bg-white p-2 pl-4 rounded-full border shadow-sm">
                    <div class="text-right user-info">
                        <p class="text-sm font-bold text-main m-0 leading-tight"><?= escape($currentUser['name']); ?></p>
                        <p class="text-xs font-bold text-primary uppercase m-0 leading-tight"><?= ucfirst(str_replace('_', ' ', $role)); ?></p>
                    </div>
                    <div class="avatar"><?= mb_substr(escape($currentUser['name']), 0, 1); ?></div>
                </div>
            </div>
        </header>

        <main class="content-body">
            <?php if ($successFlash): ?>
                <div class="alert alert-success">
                    <span class="material-symbols-outlined">check_circle</span>
                    <?= escape($successFlash); ?>
                </div>
            <?php endif; ?>
            <?php if ($errorFlash): ?>
                <div class="alert alert-error">
                    <span class="material-symbols-outlined">error</span>
                    <?= escape($errorFlash); ?>
                </div>
            <?php endif; ?>
<?php else: ?>
    <!-- Public Header for Login/Register -->
    <header class="topbar border-none bg-transparent">
        <div class="flex justify-between items-center w-full max-w-7xl mx-auto px-6">
            <a href="/" class="sidebar-logo text-primary text-2xl font-extrabold">Helperly</a>
            <div class="flex gap-4">
                <a href="/login" class="btn btn-outline rounded-full px-6">Login</a>
                <a href="/register" class="btn btn-primary rounded-full px-6">Join Now</a>
            </div>
        </div>
    </header>
    <main class="content-body" style="max-width: 600px;">
        <?php if ($successFlash): ?>
            <div class="alert alert-success"><?= escape($successFlash); ?></div>
        <?php endif; ?>
        <?php if ($errorFlash): ?>
            <div class="alert alert-error"><?= escape($errorFlash); ?></div>
        <?php endif; ?>
<?php endif; ?>
