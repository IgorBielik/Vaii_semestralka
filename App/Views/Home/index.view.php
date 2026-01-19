<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
/** @var \App\Models\Game[] $games */
/** @var int[] $wishlistGameIds */
/** @var string $order */
/** @var string $dir */
/** @var \App\Models\Genre[] $genres */
/** @var \App\Models\Platform[] $platforms */
/** @var int $page */
/** @var int $totalPages */
?>

<div class="container web-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Upcoming games</h1>
        <!-- Button to open left filter panel -->
        <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#filtersOffcanvas" aria-controls="filtersOffcanvas">
            ☰ Filters
        </button>
    </div>

    <div class="row">
        <!-- Left filter column on large screens -->
        <aside class="col-lg-3 d-none d-lg-block mb-3">
            <form method="get" action="<?= $link->url('home.index') ?>" class="card shadow-sm">
                <div class="card-header fw-semibold">Filters</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" id="search" name="search" class="form-control"
                               value="<?= htmlspecialchars($searchTerm ?? '') ?>"
                               placeholder="Search by name or publisher...">
                    </div>

                    <!-- Genres -->
                    <div class="mb-3">
                        <h6>Genres</h6>
                        <div class="d-flex flex-column gap-1" id="genre-filters">
                            <?php foreach ($genres as $genre): ?>
                                <?php $gid = (int)$genre->getId(); ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="genres[]"
                                           value="<?= $gid ?>"
                                           id="genre-<?= $gid ?>"
                                           <?= in_array($gid, $selectedGenres ?? [], true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="genre-<?= $gid ?>">
                                        <?= htmlspecialchars($genre->getName()) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Platforms -->
                    <div>
                        <h6>Platforms</h6>
                        <div class="d-flex flex-column gap-1" id="platform-filters">
                            <?php foreach ($platforms as $platform): ?>
                                <?php $pid = (int)$platform->getId(); ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="platforms[]"
                                           value="<?= $pid ?>"
                                           id="platform-<?= $pid ?>"
                                           <?= in_array($pid, $selectedPlatforms ?? [], true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="platform-<?= $pid ?>">
                                        <?= htmlspecialchars($platform->getName()) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    </div>
                </div>
            </form>
        </aside>

        <!-- Games table column -->
        <section class="col-12 col-lg-9">
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
                                    <?php $img = $game->getImageUrlOrEmpty(); ?>
                                    <?php if ($img !== ''): ?>
                                        <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>">
                                            <img src="<?= htmlspecialchars($img) ?>"
                                                 alt="<?= htmlspecialchars($game->getName()) ?>"
                                                 class="img-fluid"
                                                 style="max-width: 80px;">
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>" class="text-decoration-none">
                                            Img
                                        </a>
                                    <?php endif; ?>
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

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Games pagination" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <?php
                            // Helper to build page URL while preserving filters and sort
                            $buildPageUrl = function (int $targetPage) use ($link, $order, $dir, $selectedGenres, $selectedPlatforms, $searchTerm): string {
                                $params = [
                                    'page' => $targetPage,
                                    'order' => $order,
                                    'dir' => strtolower($dir),
                                ];
                                if (!empty($searchTerm)) {
                                    $params['search'] = $searchTerm;
                                }
                                foreach ($selectedGenres ?? [] as $gid) {
                                    $params['genres'][] = $gid;
                                }
                                foreach ($selectedPlatforms ?? [] as $pid) {
                                    $params['platforms'][] = $pid;
                                }
                                return $link->url('home.index', $params);
                            };
                            ?>

                            <!-- Previous page -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $page > 1 ? $buildPageUrl($page - 1) : '#' ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $buildPageUrl($p) ?>"><?= $p ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next page -->
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $page < $totalPages ? $buildPageUrl($page + 1) : '#' ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>

    <!-- Offcanvas filters for mobile/tablet -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="filtersOffcanvas" aria-labelledby="filtersOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filtersOffcanvasLabel">Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mb-3">
                <label for="search-offcanvas" class="form-label">Search</label>
                <input type="text" id="search-offcanvas" class="form-control"
                       placeholder="Search by name, genre, platform...">
            </div>
            <div class="mb-3">
                <h6>Genres</h6>
                <div class="d-flex flex-column gap-1" id="genre-filters-offcanvas">
                    <?php foreach ($genres as $genre): ?>
                        <div class="form-check">
                            <input class="form-check-input genre-filter" type="checkbox"
                                   data-id="<?= (int)$genre->getId() ?>"
                                   id="oc-genre-<?= (int)$genre->getId() ?>">
                            <label class="form-check-label" for="oc-genre-<?= (int)$genre->getId() ?>">
                                <?= htmlspecialchars($genre->getName()) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <h6>Platforms</h6>
                <div class="d-flex flex-column gap-1" id="platform-filters-offcanvas">
                    <?php foreach ($platforms as $platform): ?>
                        <div class="form-check">
                            <input class="form-check-input platform-filter" type="checkbox"
                                   data-id="<?= (int)$platform->getId() ?>"
                                   id="oc-platform-<?= (int)$platform->getId() ?>">
                            <label class="form-check-label" for="oc-platform-<?= (int)$platform->getId() ?>">
                                <?= htmlspecialchars($platform->getName()) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
