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
    } elseif ($role === 'service_provider') {
        $navItems = [
            'dashboard' => ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'dashboard'],
            'profile' => ['label' => 'My Profile', 'url' => '/profile/servant', 'icon' => 'badge'],
            'messages' => ['label' => 'Messages', 'url' => '/messages', 'icon' => 'chat'],
        ];
    } elseif ($role === 'administrator') {
        $navItems = [
            'dashboard' => ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'dashboard'],
            'users' => ['label' => 'User Management', 'url' => '/admin/users', 'icon' => 'group'],
            'verifications' => ['label' => 'Verifications', 'url' => '/admin/verifications', 'icon' => 'verified_user'],
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
            <div class="topbar-left">
                <button id="sidebar-toggle" class="sidebar-toggle-btn">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h1 class="page-title"><?= escape($title ?? 'Dashboard'); ?></h1>
            </div>
            <div class="topbar-right">
                <div class="user-profile-header">
                    <div class="user-info">
                        <span class="user-name"><?= escape($currentUser['name']); ?></span>
                        <span class="user-role"><?= ucfirst(str_replace('_', ' ', $role)); ?></span>
                    </div>
                    <div class="user-avatar-rect"><?= mb_substr(escape($currentUser['name']), 0, 1); ?></div>
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
        <div class="flex justify-between items-center" style="max-width: 1200px; margin: 0 auto; width: 100%; height: 80px; padding: 0 2rem;">
            <a href="/" class="sidebar-logo" style="color: var(--primary); font-size: 1.75rem;">Helperly</a>
            <div class="flex gap-4">
                <a href="/login" class="btn btn-outline" style="border-radius: 99px;">Login</a>
                <a href="/register" class="btn btn-primary" style="border-radius: 99px;">Join Now</a>
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
