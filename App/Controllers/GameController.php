<?php
/*vypracované pomocou AI*/
namespace App\Controllers;

use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class GameController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        // Detail hry (show, index) je verejný
        if (in_array($action, ['show', 'index'], true)) {
            return true;
        }

        // Ostatné akcie (create, store, update, edit, delete) len pre adminov
        $identity = $this->user->getIdentity();
        return $identity instanceof User && $identity->isAdmin();
    }

    // default action – môžeme napr. presmerovať na home alebo zoznam hier
    public function index(Request $request): Response
    {
        return $this->redirect($this->url('home.index'));
    }

    public function show(Request $request): Response
    {
        $id = (int)($request->get('id') ?? 0);
        if ($id <= 0) {
            return $this->redirect($this->url('home.index'));
        }

        $game = Game::getOne($id);
        if (!$game) {
            return $this->redirect($this->url('home.index'));
        }

        $identity = $this->user->getIdentity();
        $isAdmin = $identity instanceof User && $identity->isAdmin();

        return $this->html([
            'game' => $game,
            'isAdmin' => $isAdmin,
            'platformText' => $this->getGamePlatformText($game),
            'genreText' => $this->getGameGenreText($game),
            'imageUrl' => $this->getGameImageUrl($game),
            'releaseDate' => $this->getGameReleaseDate($game),
            'priceFormatted' => $this->getGamePriceFormatted($game),
        ]);
    }

    public function update(Request $request): Response
    {
        // povolíme iba POST
        if (!$request->isPost()) {
            return $this->redirect($this->url('home.index'));
        }

        $identity = $this->user->getIdentity();
        if (!($identity instanceof User) || !$identity->isAdmin()) {
            return $this->redirect($this->url('home.index'));
        }

        $id = (int)($request->post('id') ?? 0);
        if ($id <= 0) {
            return $this->redirect($this->url('home.index'));
        }

        $game = Game::getOne($id);
        if (!$game) {
            return $this->redirect($this->url('home.index'));
        }

        $this->fillGameFromRequest($game, $request);

        // After saving basic fields, also sync genres and platforms from the edit form
        $genreIds = $request->post('genres') ?? [];
        $platformIds = $request->post('platforms') ?? [];

        $game->save();

        if (!empty($genreIds)) {
            $game->syncGenres($genreIds);
        } else {
            // if nothing selected, clear relations
            $game->syncGenres([]);
        }

        if (!empty($platformIds)) {
            $game->syncPlatforms($platformIds, null, null);
        } else {
            $game->syncPlatforms([], null, null);
        }

        return $this->redirect($this->url('game.show', ['id' => $game->getId()]));
    }

    public function create(Request $request): Response
    {
        // len admin (authorize to vyrieši)
        $genres = Genre::getAll(orderBy: 'name');
        $platforms = Platform::getAll(orderBy: 'name');

        // použijeme explicitný názov view, ktorý zodpovedá App/Views/Game/create.view.php
        return $this->html([
            'genres' => $genres,
            'platforms' => $platforms,
        ], '/create');
    }

    public function store(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->redirect($this->url('game.create'));
        }

        $data = $request->post();

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            // jednoduchý fallback: vrátime späť na formulár
            return $this->redirect($this->url('game.create'));
        }

        $game = new Game();
        $game->setName($name);
        $game->setPublisher($data['publisher'] ?? null);
        $game->setGlobalReleaseDate($data['global_release_date'] ?: null);
        $game->setBasePriceEur(($data['base_price_eur'] ?? '') !== '' ? (float)$data['base_price_eur'] : null);
        $game->setIsDlc(!empty($data['is_dlc']));
        $game->setIsEarlyAccess(!empty($data['is_early_access']));
        // new: save description from create form if provided
        if (isset($data['description'])) {
            $game->setDescription($data['description']);
        }

        // Cover image URL from text input; store null when empty
        $imageUrl = isset($data['cover_image']) && trim($data['cover_image']) !== '' ? trim($data['cover_image']) : null;
        $game->setImageUrl($imageUrl);

        $game->save();

        // asociácie žánrov a platforiem (bez per-platform dátumu/ceny)
        $genreIds = $data['genres'] ?? [];
        $platformIds = $data['platforms'] ?? [];

        if (!empty($genreIds)) {
            $game->syncGenres($genreIds);
        }

        if (!empty($platformIds)) {
            // použijeme null pre release_date a price_eur, kým nebudeš chcieť riešiť per-platform údaje
            $game->syncPlatforms($platformIds, null, null);
        }

        return $this->redirect($this->url('game.show', ['id' => $game->getId()]));
    }

    public function edit(Request $request): Response
    {
        // len admin (authorize to vyrieši)
        $id = (int)($request->get('id') ?? 0);
        if ($id <= 0) {
            return $this->redirect($this->url('home.index'));
        }

        $game = Game::getOne($id);
        if (!$game) {
            return $this->redirect($this->url('home.index'));
        }

        $genres = Genre::getAll(orderBy: 'name');
        $platforms = Platform::getAll(orderBy: 'name');

        // Prepare current genre and platform IDs for pre-selecting options
        $currentGenreIds = [];
        foreach ($game->getGenres() as $g) {
            $currentGenreIds[] = $g->getId();
        }

        $currentPlatformIds = [];
        foreach ($game->getPlatforms() as $p) {
            $currentPlatformIds[] = $p->getId();
        }

        return $this->html([
            'game' => $game,
            'genres' => $genres,
            'platforms' => $platforms,
            'currentGenreIds' => $currentGenreIds,
            'currentPlatformIds' => $currentPlatformIds,
        ], '/edit');
    }

    public function delete(Request $request): Response
    {
        // Only POST allowed
        if (!$request->isPost()) {
            return $this->redirect($this->url('home.index'));
        }

        $identity = $this->user->getIdentity();
        if (!($identity instanceof User) || !$identity->isAdmin()) {
            return $this->redirect($this->url('home.index'));
        }

        $id = (int)($request->post('id') ?? 0);
        if ($id <= 0) {
            return $this->redirect($this->url('home.index'));
        }

        $game = Game::getOne($id);
        if (!$game) {
            return $this->redirect($this->url('home.index'));
        }

        // Delete game with all relations (game_genre, game_platform, wishlist)
        $game->deleteWithRelations();

        // Redirect to home after deletion
        return $this->redirect($this->url('home.index'));
    }

    private function fillGameFromRequest(Game $game, Request $request): void
    {
        $post = $request->post();

        $game->setName($post['name'] ?? '');
        $game->setPublisher($post['publisher'] ?? null);
        $game->setGlobalReleaseDate($post['global_release_date'] ?? null);
        $game->setBasePriceEur(isset($post['base_price_eur']) && $post['base_price_eur'] !== '' ? (float)$post['base_price_eur'] : null);
        $game->setIsDlc(!empty($post['is_dlc']));
        $game->setIsEarlyAccess(!empty($post['is_early_access']));
        $game->setDescription($post['description'] ?? '');

        // Cover image URL from text input; store null when empty
        $imageUrl = isset($post['cover_image']) && trim($post['cover_image']) !== '' ? trim($post['cover_image']) : null;
        $game->setImageUrl($imageUrl);
    }

    /**
     * Get formatted platform names for display (comma-separated).
     */
    private function getGamePlatformText(Game $game): string
    {
        $platformNames = [];
        foreach ($game->getPlatforms() as $platform) {
            $platformNames[] = $platform->getName();
        }
        return implode(', ', $platformNames);
    }

    /**
     * Get formatted genre names for display (comma-separated).
     */
    private function getGameGenreText(Game $game): string
    {
        $genreNames = [];
        foreach ($game->getGenres() as $genre) {
            $genreNames[] = $genre->getName();
        }
        return implode(', ', $genreNames);
    }

    /**
     * Get game image URL, fallback to empty string if none.
     */
    private function getGameImageUrl(Game $game): string
    {
        return $game->getImageUrl() ?? '';
    }

    /**
     * Get formatted release date or 'TBA' if not set.
     */
    private function getGameReleaseDate(Game $game): string
    {
        return $game->getGlobalReleaseDate() ?? 'TBA';
    }

    /**
     * Get formatted price with currency or 'N/A'.
     */
    private function getGamePriceFormatted(Game $game): string
    {
        $price = $game->getBasePriceEur();
        return $price !== null ? number_format($price, 2) . ' €' : 'N/A';
    }
}
