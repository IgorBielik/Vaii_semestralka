<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Genre[] $genres */
/** @var \App\Models\Platform[] $platforms */
?>

<div class="container mt-4">
    <h1>Create new game</h1>

    <form method="post" action="<?= $link->url('game.store') ?>" enctype="multipart/form-data">
        <div class="game-create-container mt-3">
            <!-- Left: cover image picker -->
            <div class="game-create-left">
                <label for="cover_image" class="form-label">Cover image</label>
                <div class="game-create-image-wrapper" id="cover-image-wrapper">
                    <span class="game-create-image-placeholder">Click to choose image</span>
                    <img id="cover-image-preview" src="" alt="Cover preview" style="display: none;">
                </div>
                <input type="file" name="cover_image" id="cover_image" accept="image/*" class="d-none">
            </div>

            <!-- Right: main fields -->
            <div class="game-create-right">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="publisher" class="form-label">Publisher</label>
                        <input type="text" name="publisher" id="publisher" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label for="global_release_date" class="form-label">Global release date</label>
                        <input type="date" name="global_release_date" id="global_release_date" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label for="base_price_eur" class="form-label">Base price (€)</label>
                        <input type="number" step="0.01" min="0" name="base_price_eur" id="base_price_eur" class="form-control">
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" id="is_dlc" name="is_dlc">
                            <label class="form-check-label" for="is_dlc">Is DLC</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_early_access" name="is_early_access">
                            <label class="form-check-label" for="is_early_access">Early access</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="genres" class="form-label">Genres</label>
                        <select name="genres[]" id="genres" class="form-select" multiple>
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?= (int)$genre->getId() ?>"><?= htmlspecialchars($genre->getName()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="platforms" class="form-label">Platforms</label>
                        <select name="platforms[]" id="platforms" class="form-select" multiple>
                            <?php foreach ($platforms as $platform): ?>
                                <option value="<?= (int)$platform->getId() ?>"><?= htmlspecialchars($platform->getName()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control" placeholder="Enter game description..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="<?= $link->url('home.index') ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create game</button>
        </div>
    </form>
</div>
