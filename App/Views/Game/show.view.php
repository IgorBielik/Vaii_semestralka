<?php
/*vypracované pomocou AI*/
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Game $game */
/** @var bool $isAdmin */
/** @var string $platformText */
/** @var string $genreText */
/** @var string $imageUrl */
/** @var string $releaseDate */
/** @var string $priceFormatted */
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?= htmlspecialchars($game->getName()) ?></h1>

        <?php if ($isAdmin): ?>
            <a href="<?= $link->url('game.edit', ['id' => $game->getId()]) ?>" class="btn btn-sm btn-primary">EDIT</a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <!-- Cover image -->
        <div class="col-md-4">
            <div class="border rounded bg-white p-2 d-flex justify-content-center align-items-center" style="min-height: 220px;">
                <?php if ($imageUrl !== ''): ?>
                    <img src="<?= htmlspecialchars($imageUrl) ?>"
                         alt="<?= htmlspecialchars($game->getName()) ?>"
                         class="img-fluid rounded">
                <?php else: ?>
                    <span class="text-muted">Img</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Game info -->
        <div class="col-md-8">
            <?php $description = trim($game->getDescription()); ?>
            <?php if ($description !== ''): ?>
                <div class="mb-3">
                    <h5>Description</h5>
                    <p><?= nl2br(htmlspecialchars($description)) ?></p>
                </div>
            <?php endif; ?>

            <div class="row small text-muted mb-3">
                <div class="col-sm-6 mb-2">
                    <strong>Release date:</strong>
                    <span class="ms-1"><?= htmlspecialchars($releaseDate) ?></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <strong>Price:</strong>
                    <span class="ms-1"><?= htmlspecialchars($priceFormatted) ?></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <strong>Publisher:</strong>
                    <span class="ms-1"><?= htmlspecialchars($game->getPublisher() ?? '') ?></span>
                </div>

                <?php if ($platformText !== ''): ?>
                    <div class="col-sm-12 mb-2">
                        <strong>Platforms:</strong>
                        <span class="ms-1"><?= htmlspecialchars($platformText) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($genreText !== ''): ?>
                    <div class="col-sm-12 mb-2">
                        <strong>Genres:</strong>
                        <span class="ms-1"><?= htmlspecialchars($genreText) ?></span>
                    </div>
                <?php endif; ?>

                <div class="col-sm-12 mt-1">
                    <?php if ($game->isDlc()): ?>
                        <span class="badge bg-secondary me-1">DLC</span>
                    <?php endif; ?>
                    <?php if ($game->isEarlyAccess()): ?>
                        <span class="badge bg-warning text-dark">Early access</span>
                    <?php endif; ?>
                </div>
            </div>

            <a href="<?= $link->url('home.index') ?>" class="btn btn-secondary mt-2">Back to home</a>
        </div>
    </div>
</div>
