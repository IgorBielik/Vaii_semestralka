<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Game $game */
/** @var bool $isAdmin */
?>

<div class="container mt-4">
    <h1><?= htmlspecialchars($game->getName()) ?></h1>

    <p><strong>Release date:</strong> <?= htmlspecialchars($game->getGlobalReleaseDate() ?? 'TBA') ?></p>
    <p><strong>Price:</strong> <?= htmlspecialchars($game->getBasePriceEur() ?? 0) ?> €</p>
    <p><strong>Publisher:</strong> <?= htmlspecialchars($game->getPublisher() ?? '') ?></p>
    <p><strong>DLC:</strong> <?= $game->isDlc() ? 'Yes' : 'No' ?></p>
    <p><strong>Early access:</strong> <?= $game->isEarlyAccess() ? 'Yes' : 'No' ?></p>

    <?php if ($isAdmin): ?>
        <hr>
        <h2>Edit game</h2>
        <form method="post" action="<?= $link->url('game.update') ?>">
            <input type="hidden" name="id" value="<?= (int)$game->getId() ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($game->getName()) ?>" required>
            </div>

            <div class="mb-3">
                <label for="release_date" class="form-label">Release date</label>
                <input type="date" class="form-control" id="release_date" name="release_date" value="<?= htmlspecialchars($game->getGlobalReleaseDate() ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="price_eur" class="form-label">Price (€)</label>
                <input type="number" step="0.01" class="form-control" id="price_eur" name="price_eur" value="<?= htmlspecialchars($game->getBasePriceEur() ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="publisher" class="form-label">Publisher</label>
                <input type="text" class="form-control" id="publisher" name="publisher" value="<?= htmlspecialchars($game->getPublisher() ?? '') ?>">
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="is_dlc" name="is_dlc" <?= $game->isDlc() ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_dlc">Is DLC</label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_early_access" name="is_early_access" <?= $game->isEarlyAccess() ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_early_access">Is Early Access</label>
            </div>

            <button type="submit" class="btn btn-primary">Save changes</button>
        </form>
    <?php endif; ?>

    <a href="<?= $link->url('home.index') ?>" class="btn btn-secondary mt-3">Back to home</a>
</div>
