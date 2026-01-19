<?php

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

        // Ostatné akcie (create, store, update, edit) len pre adminov
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
            // Ak hra neexistuje, môžeme presmerovať na home alebo zobraziť error view
            return $this->redirect($this->url('home.index'));
        }

        $identity = $this->user->getIdentity();
        $isAdmin = $identity instanceof User && $identity->isAdmin();

        return $this->html([
            'game' => $game,
            'isAdmin' => $isAdmin,
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

        return $this->html([
            'game' => $game,
            'genres' => $genres,
            'platforms' => $platforms,
        ], '/edit'); // App/Views/Game/edit.view.php
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
}
