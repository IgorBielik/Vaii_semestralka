<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Game $game */
/** @var bool $isAdmin */
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?= htmlspecialchars($game->getName()) ?></h1>

        <?php if ($isAdmin): ?>
            <a href="<?= $link->url('game.edit', ['id' => $game->getId()]) ?>" class="btn btn-sm btn-primary">EDIT</a>
        <?php endif; ?>
    </div>

    <?php $description = trim($game->getDescription()); ?>
    <?php if ($description !== ''): ?>
        <div class="mb-3">
            <p><?= nl2br(htmlspecialchars($description)) ?></p>
        </div>
    <?php endif; ?>

    <?php
    // Build simple text lists for platforms and genres
    $platformNames = [];
    foreach ($game->getPlatforms() as $platform) {
        /** @var \App\Models\Platform $platform */
        $platformNames[] = $platform->getName();
    }

    $genreNames = [];
    foreach ($game->getGenres() as $genre) {
        /** @var \App\Models\Genre $genre */
        $genreNames[] = $genre->getName();
    }

    $platformText = implode(', ', $platformNames);
    $genreText    = implode(', ', $genreNames);
    ?>

    <div class="small text-muted mb-3">
        <div><strong>Release date:</strong> <?= htmlspecialchars($game->getGlobalReleaseDate() ?? 'TBA') ?></div>
        <div><strong>Price:</strong> <?= htmlspecialchars($game->getBasePriceEur() ?? 0) ?> &euro;</div>
        <div><strong>Publisher:</strong> <?= htmlspecialchars($game->getPublisher() ?? '') ?></div>

        <?php if ($platformText !== ''): ?>
            <div><strong>Platform:</strong> <?= htmlspecialchars($platformText) ?></div>
        <?php endif; ?>

        <?php if ($genreText !== ''): ?>
            <div><strong>Genres:</strong> <?= htmlspecialchars($genreText) ?></div>
        <?php endif; ?>

        <div class="mt-1">
            <?php if ($game->isDlc()): ?>
                <span class="me-2">DLC</span>
            <?php endif; ?>
            <?php if ($game->isEarlyAccess()): ?>
                <span>Early access</span>
            <?php endif; ?>
        </div>
    </div>

    <a href="<?= $link->url('home.index') ?>" class="btn btn-secondary mt-3">Back to home</a>
</div>
