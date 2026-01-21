<?php
/*vypracované pomocou AI*/
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
/** @var \App\Models\Game[] $games */
/** @var int[] $wishlistGameIds */
/** @var array<int,bool> $wishlistMap */
/** @var string $order */
/** @var string $dir */
/** @var \App\Models\Genre[] $genres */
/** @var \App\Models\Platform[] $platforms */
/** @var int $page */
/** @var int $totalPages */
/** @var array<string,string> $sortLinks */
/** @var array{prev: array{url:string,disabled:bool}, next: array{url:string,disabled:bool}, pages: array<int,array{number:int,url:string,isActive:bool}>} $pagination */
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

                <div class="table-responsive-custom">
                    <table class="table align-middle" id="games-table">

                        <thead>
                        <tr>
                            <th class="d-none d-lg-table-cell">Cover</th>

                            <th class="d-none d-md-table-cell">
                                <a href="<?= $sortLinks['name'] ?>" class="text-decoration-none">
                                    Name
                                    <?php if ($order === 'name'): ?>
                                        <?= $dir === 'asc' ? '▲' : '▼' ?>
                                    <?php endif; ?>
                                </a>
                            </th>

                            <th class="d-none d-lg-table-cell">Tags</th>

                            <th class="d-none d-lg-table-cell">
                                <a href="<?= $sortLinks['price'] ?>" class="text-decoration-none">
                                    Price
                                    <?php if ($order === 'price'): ?>
                                        <?= $dir === 'asc' ? '▲' : '▼' ?>
                                    <?php endif; ?>
                                </a>
                            </th>

                            <th class="d-none d-lg-table-cell">
                                <a href="<?= $sortLinks['date'] ?>" class="text-decoration-none">
                                    Release date
                                    <?php if ($order === 'date'): ?>
                                        <?= $dir === 'asc' ? '▲' : '▼' ?>
                                    <?php endif; ?>
                                </a>
                            </th>

                            <th class="d-none d-md-table-cell">Wishlist</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <!-- Game cover -->
                                <td class="d-none d-lg-table-cell">
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

                                <!-- Mobile-only: Cover image as grid -->
                                <td class="d-lg-none p-2">
                                    <?php $img = $game->getImageUrlOrEmpty(); ?>
                                    <?php if ($img !== ''): ?>
                                        <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>">
                                            <img src="<?= htmlspecialchars($img) ?>"
                                                 alt="<?= htmlspecialchars($game->getName()) ?>"
                                                 class="img-fluid"
                                                 style="max-width: 100%; height: auto;">
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>"
                                           class="d-flex align-items-center justify-content-center bg-light text-decoration-none"
                                           style="min-height: 150px;">
                                            Img
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <!-- Name -->
                                <td class="d-none d-md-table-cell">
                                    <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>"
                                       class="fw-semibold text-decoration-none">
                                        <?= htmlspecialchars($game->getName()) ?>
                                    </a>
                                </td>

                                <!-- Tags -->
                                <td class="d-none d-lg-table-cell">
                                    <?php if ($game->isDlc()): ?>
                                        <span class="badge bg-secondary me-1">DLC</span>
                                    <?php endif; ?>

                                    <?php if ($game->isEarlyAccess()): ?>
                                        <span class="badge bg-warning text-dark">EA</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Price -->
                                <td class="d-none d-lg-table-cell">
                                    <?php $price = $game->getBasePriceEur(); ?>
                                    <?= $price !== null ? number_format($price, 2) . ' €' : 'N/A' ?>
                                </td>

                                <!-- Release date -->
                                <td class="d-none d-lg-table-cell">
                                    <?= htmlspecialchars($game->getGlobalReleaseDate() ?? 'TBA') ?>
                                </td>

                                <!-- Wishlist button -->
                                <td class="d-none d-md-table-cell">
                                    <?php if ($user->isLoggedIn()): ?>

                                        <?php $inWishlist = isset($wishlistMap[$game->getId()]); ?>

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

                            <!-- Previous page -->
                            <li class="page-item <?= $pagination['prev']['disabled'] ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $pagination['prev']['url'] ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php foreach ($pagination['pages'] as $pg): ?>
                                <li class="page-item <?= $pg['isActive'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $pg['url'] ?>"><?= $pg['number'] ?></a>
                                </li>
                            <?php endforeach; ?>

                            <!-- Next page -->
                            <li class="page-item <?= $pagination['next']['disabled'] ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $pagination['next']['url'] ?>" aria-label="Next">
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
