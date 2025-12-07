<?php

/** @var array $items */
/** @var \Framework\Support\View $view */
/** @var \Framework\Support\LinkGenerator $link */


// Použijeme root layout (nemusíme meniť)
?>

<div class="container mt-4">
    <h1>My wishlist</h1>

    <?php if (empty($items)) : ?>
        <p>You don't have any games in your wishlist yet.</p>
        <a href="<?= $link->url('home.index') ?>" class="btn btn-primary mt-2">
            Browse games
        </a>
    <?php else : ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Game</th>
                <th>Release date</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $row): ?>
                <?php /** @var \App\Models\Wishlist $wishlist */
                $wishlist = $row['wishlist'];
                /** @var \App\Models\Game $game */
                $game = $row['game'];
                ?>
                <tr>
                    <td>
                        <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>">
                            <?= htmlspecialchars($game->getName()) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($game->getGlobalReleaseDate()) ?></td>
                    <td>
                        <form method="post" action="<?= $link->url('wishlist.remove') ?>" style="display:inline-block;">
                            <input type="hidden" name="game_id" value="<?= (int)$game->getId() ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
