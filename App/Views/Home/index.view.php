<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
/** @var \App\Models\Game[] $games */
/** @var int[] $wishlistGameIds */
/** @var string $order */
/** @var string $dir */
?>

<div class="container mt-4">
    <h1 class="mb-4">Upcoming games</h1>

    <!-- Client-side search only: no form submit, just JS filtering -->
    <div class="row g-2 mb-3">
        <div class="col-md-8">
            <label for="search" class="form-label visually-hidden">Search by name, genre or platform</label>
            <input type="text" id="search" class="form-control" placeholder="Search by name, genre, platform...">
        </div>
    </div>

    <?php if (empty($games)) : ?>
        <p>No upcoming games found.</p>
    <?php else : ?>
        <?php
        // Server-side sorting only (JS handles filtering)
        $toggleDir = fn(string $col) => ($order === $col && strtolower($dir) === 'asc') ? 'desc' : 'asc';
        $sortUrl = function (string $col) use ($link, $toggleDir, $order) {
            $dir = $toggleDir($col);
            $params = ['order' => $col, 'dir' => $dir];
            return $link->url('home.index', $params);
        };
        ?>

        <div class="table-responsive">
            <table class="table align-middle" id="games-table">
                <thead>
                <tr>
                    <th scope="col">Cover</th>
                    <th scope="col">
                        <a href="<?= $sortUrl('name') ?>" class="text-decoration-none">
                            Name
                            <?php if ($order === 'name'): ?>
                                <?= strtolower($dir) === 'asc' ? '▲' : '▼' ?>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th scope="col">Tags</th>
                    <th scope="col">
                        <a href="<?= $sortUrl('price') ?>" class="text-decoration-none">
                            Price
                            <?php if ($order === 'price'): ?>
                                <?= strtolower($dir) === 'asc' ? '▲' : '▼' ?>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th scope="col">
                        <a href="<?= $sortUrl('date') ?>" class="text-decoration-none">
                            Release date
                            <?php if ($order === 'date'): ?>
                                <?= strtolower($dir) === 'asc' ? '▲' : '▼' ?>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th scope="col">Wishlist</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td style="width: 80px;">
                            <?php $imageUrl = $link->asset('images/vaiicko_logo.png'); ?>
                            <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>">
                                <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($game->getName()) ?>" class="img-thumbnail" style="max-width: 64px; max-height: 64px;">
                            </a>
                        </td>
                        <td>
                            <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>" class="fw-semibold text-decoration-none">
                                <?= htmlspecialchars($game->getName()) ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($game->isDlc()): ?>
                                <span class="badge bg-secondary me-1">DLC</span>
                            <?php endif; ?>
                            <?php if ($game->isEarlyAccess()): ?>
                                <span class="badge bg-warning text-dark">EA</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $price = $game->getBasePriceEur(); ?>
                            <?= $price !== null ? htmlspecialchars(number_format($price, 2)) . ' €' : 'N/A' ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($game->getGlobalReleaseDate() ?? 'TBA') ?>
                        </td>
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
                                <a href="<?= App\Configuration::LOGIN_URL ?>" class="btn btn-sm btn-outline-primary">Log in</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

