<?php

/** @var string $contentHTML */
/** @var \Framework\Auth\AppUser $user */
/** @var \Framework\Support\LinkGenerator $link */
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <title><?= App\Configuration::APP_NAME ?></title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $link->asset('favicons/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $link->asset('favicons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $link->asset('favicons/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= $link->asset('favicons/site.webmanifest') ?>">
    <link rel="shortcut icon" href="<?= $link->asset('favicons/favicon.ico') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS 5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $link->asset('css/styl.css') ?>">
    <link rel="stylesheet" href="<?= $link->asset('css/gameCreate.css') ?>">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<header>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="<?= $link->url('home.index') ?>">
                Up
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <?php if ($user->isLoggedIn()) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $link->url('wishlist.index') ?>">Wishlist</a>
                        </li>
                        <?php if ($user->getRole() === 'admin') : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $link->url('admin.index') ?>">Manage</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $link->url('game.create') ?>">Add game</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <?php if ($user->isLoggedIn()) { ?>
                    <ul class="navbar-nav ms-auto align-items-center mb-2 mb-md-0">
                        <li class="nav-item me-md-3 mb-2 mb-md-0">
                            <span class="navbar-text"><strong><?= $user->getName() ?></strong></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $link->url('auth.logout') ?>">Log out</a>
                        </li>
                    </ul>
                <?php } else { ?>
                    <ul class="navbar-nav ms-auto mb-2 mb-md-0">
                        <li class="nav-item">
                            <a class="nav-link auth-link" href="<?= App\Configuration::LOGIN_URL ?>">Log in</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link auth-link" href="<?= $link->url('auth.register') ?>">Register</a>
                        </li>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </nav>
</header>

<main class="flex-grow-1 py-3">
    <div class="container-fluid">
        <div class="web-content">
            <?= $contentHTML ?>
        </div>
    </div>
</main>

<footer class="mt-auto py-3 bg-white border-top small text-muted text-center">
    <div class="container">
        &copy; <?= date('Y') ?> <?= App\Configuration::APP_NAME ?>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="<?= $link->asset('js/addRemoveWish.js') ?>"></script>
<script src="<?= $link->asset('js/admin.js') ?>" defer></script>
<script src="<?= $link->asset('js/filters.js') ?>" defer></script>
</body>
</html>
