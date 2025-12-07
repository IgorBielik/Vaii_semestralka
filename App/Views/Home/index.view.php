<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
/** @var \App\Models\Game[] $games */
/** @var int[] $wishlistGameIds */
/** @var string $order */
/** @var string $dir */
?>

<div class="container web-content">

    <h1 class="mb-4">Upcoming games</h1>

    <!-- Client-side search -->
    <div class="row g-2 mb-3">
        <div class="col-md-8">
            <label for="search" class="form-label visually-hidden">Search</label>
            <input type="text" id="search" class="form-control" placeholder="Search by name, genre, platform...">
        </div>
    </div>

    <?php if (empty($games)) : ?>
        <p>No upcoming games found.</p>

    <?php else : ?>

        <?php
        $toggleDir = fn(string $col) => ($order === $col && strtolower($dir) === 'asc') ? 'desc' : 'asc';
        $sortUrl = function (string $col) use ($link, $toggleDir, $order) {
            $dir = $toggleDir($col);
            return $link->url('home.index', ['order' => $col, 'dir' => $dir]);
        };
        ?>

        <!-- Using custom responsive wrapper from CSS -->
        <div class="table-responsive-custom">
            <table class="table align-middle" id="games-table">

                <thead>
                <tr>
                    <th>Cover</th>

                    <th>
                        <a href="<?= $sortUrl('name') ?>" class="text-decoration-none">
                            Name
                            <?php if ($order === 'name'): ?>
                                <?= strtolower($dir) === 'asc' ? '▲' : '▼' ?>
                            <?php endif; ?>
                        </a>
                    </th>

                    <th>Tags</th>

                    <th>
                        <a href="<?= $sortUrl('price') ?>" class="text-decoration-none">
                            Price
                            <?php if ($order === 'price'): ?>
                                <?= strtolower($dir) === 'asc' ? '▲' : '▼' ?>
                            <?php endif; ?>
                        </a>
                    </th>

                    <th>
                        <a href="<?= $sortUrl('date') ?>" class="text-decoration-none">
                            Release date
                            <?php if ($order === 'date'): ?>
                                <?= strtolower($dir) === 'asc' ? '▲' : '▼' ?>
                            <?php endif; ?>
                        </a>
                    </th>

                    <th>Wishlist</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($games as $game): ?>
                    <tr>

                        <!-- Game cover -->
                        <td>
                            <?php $imageUrl = $link->asset('images/vaiicko_logo.png'); ?>
                            <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>">
                                <img src="<?= $imageUrl ?>"
                                     alt="<?= htmlspecialchars($game->getName()) ?>"
                                     class="img-fluid"
                                     style="max-width: 80px;">
                            </a>
                        </td>

                        <!-- Name -->
                        <td>
                            <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>"
                               class="fw-semibold text-decoration-none">
                                <?= htmlspecialchars($game->getName()) ?>
                            </a>
                        </td>

                        <!-- Tags -->
                        <td>
                            <?php if ($game->isDlc()): ?>
                                <span class="badge bg-secondary me-1">DLC</span>
                            <?php endif; ?>

                            <?php if ($game->isEarlyAccess()): ?>
                                <span class="badge bg-warning text-dark">EA</span>
                            <?php endif; ?>
                        </td>

                        <!-- Price -->
                        <td>
                            <?php $price = $game->getBasePriceEur(); ?>
                            <?= $price !== null ? number_format($price, 2) . ' €' : 'N/A' ?>
                        </td>

                        <!-- Release date -->
                        <td>
                            <?= htmlspecialchars($game->getGlobalReleaseDate() ?? 'TBA') ?>
                        </td>

                        <!-- Wishlist button -->
                        <td>
                            <?php if ($user->isLoggedIn()): ?>

                                <?php $inWishlist = in_array($game->getId(), $wishlistGameIds, true); ?>

                                <?php if ($inWishlist): ?>
                                    <form method="post" action="<?= $link->url('wishlist.remove') ?>" class="d-inline">
                                        <input type="hidden" name="game_id" value="<?= (int)$game->getId() ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="<?= $link->url('wishlist.add') ?>" class="d-inline">
                                        <input type="hidden" name="game_id" value="<?= (int)$game->getId() ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Add</button>
                                    </form>
                                <?php endif; ?>

                            <?php else: ?>

                                <a href="<?= App\Configuration::LOGIN_URL ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    Log in
                                </a>

                            <?php endif; ?>

                        </td>

                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    <?php endif; ?>
</div>
