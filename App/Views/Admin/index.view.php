<?php
/*vypracované pomocou AI*/
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
/** @var \App\Models\Platform[] $platforms */
/** @var \App\Models\Genre[] $genres */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1>Admin dashboard</h1>
            <p>
                Welcome, <strong><?= htmlspecialchars($user->getName()) ?></strong>!<br>
                Manage platform and genre dictionaries used in the catalog.
            </p>
        </div>
    </div>

    <div class="row">
        <!-- Platforms column -->
        <div class="col-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Platforms</span>
                    <div class="w-50">
                        <label for="platform-search" class="form-label visually-hidden">Search platforms</label>
                        <input type="text" id="platform-search" class="form-control form-control-sm" placeholder="Search platforms...">
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($platforms)): ?>
                        <p>No platforms defined yet.</p>
                    <?php else: ?>
                        <div class="admin-list-scroll">
                            <table class="table table-sm align-middle" id="platform-table">
                                <thead>
                                <tr>
                                    <th style="width: 70%">Name</th>
                                    <th style="width: 30%" class="text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($platforms as $platform): ?>
                                    <tr data-name="<?= htmlspecialchars(strtolower($platform->getName())) ?>">
                                        <td>
                                            <form class="d-flex" method="post" action="<?= $link->url('platform.update') ?>">
                                                <input type="hidden" name="id" value="<?= (int)$platform->getId() ?>">
                                                <div class="flex-grow-1">
                                                    <label class="form-label visually-hidden" for="platform-name-<?= (int)$platform->getId() ?>">Platform name</label>
                                                    <input type="text" id="platform-name-<?= (int)$platform->getId() ?>" name="name" class="form-control form-control-sm" value="<?= htmlspecialchars($platform->getName()) ?>" required>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-primary ms-2">Save</button>
                                            </form>
                                        </td>
                                        <td class="text-end">
                                            <form method="post" action="<?= $link->url('platform.delete') ?>" onsubmit="return confirm('Delete this platform?');">
                                                <input type="hidden" name="id" value="<?= (int)$platform->getId() ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <hr>
                    <h5>Add new platform</h5>
                    <form method="post" action="<?= $link->url('platform.store') ?>" class="row g-2 mt-1">
                        <div class="col-8">
                            <label class="form-label visually-hidden" for="platform-name-new">New platform name</label>
                            <input type="text" id="platform-name-new" name="name" class="form-control form-control-sm" placeholder="Platform name" required>
                        </div>
                        <div class="col-4 text-end">
                            <button type="submit" class="btn btn-sm btn-success w-100">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Genres column -->
        <div class="col-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Genres</span>
                    <div class="w-50">
                        <label for="genre-search" class="form-label visually-hidden">Search genres</label>
                        <input type="text" id="genre-search" class="form-control form-control-sm" placeholder="Search genres...">
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($genres)): ?>
                        <p>No genres defined yet.</p>
                    <?php else: ?>
                        <div class="admin-list-scroll">
                            <table class="table table-sm align-middle" id="genre-table">
                                <thead>
                                <tr>
                                    <th style="width: 70%">Name</th>
                                    <th style="width: 30%" class="text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($genres as $genre): ?>
                                    <tr data-name="<?= htmlspecialchars(strtolower($genre->getName())) ?>">
                                        <td>
                                            <form class="d-flex" method="post" action="<?= $link->url('genre.update') ?>">
                                                <input type="hidden" name="id" value="<?= (int)$genre->getId() ?>">
                                                <div class="flex-grow-1">
                                                    <label class="form-label visually-hidden" for="genre-name-<?= (int)$genre->getId() ?>">Genre name</label>
                                                    <input type="text" id="genre-name-<?= (int)$genre->getId() ?>" name="name" class="form-control form-control-sm" value="<?= htmlspecialchars($genre->getName()) ?>" required>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-primary ms-2">Save</button>
                                            </form>
                                        </td>
                                        <td class="text-end">
                                            <form method="post" action="<?= $link->url('genre.delete') ?>" onsubmit="return confirm('Delete this genre?');">
                                                <input type="hidden" name="id" value="<?= (int)$genre->getId() ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <hr>
                    <h5>Add new genre</h5>
                    <form method="post" action="<?= $link->url('genre.store') ?>" class="row g-2 mt-1">
                        <div class="col-8">
                            <label class="form-label visually-hidden" for="genre-name-new">New genre name</label>
                            <input type="text" id="genre-name-new" name="name" class="form-control form-control-sm" placeholder="Genre name" required>
                        </div>
                        <div class="col-4 text-end">
                            <button type="submit" class="btn btn-sm btn-success w-100">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

