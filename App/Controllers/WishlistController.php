<?php

namespace App\Controllers;

use App\Models\Wishlist;
use App\Models\Game;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class WishlistController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        // Povoliť prístup len prihláseným používateľom
        return $this->user->isLoggedIn();
    }

    public function index(Request $request): Response
    {
        $userId = $this->user->getId();

        $items = Wishlist::forUser($userId);

        // Doplníme k wishlist položkám informácie o hre (názov, dátum vydania)
        $games = [];
        foreach ($items as $item) {
            /** @var Wishlist $item */
            $game = Game::getOne($item->getGameId());
            if ($game) {
                $games[] = [
                    'wishlist' => $item,
                    'game' => $game,
                ];
            }
        }

        return $this->html([
            'items' => $games,
        ]);
    }

    public function add(Request $request): Response
    {
        // spracujeme iba POST požiadavku (odoslaný formulár)
        if (!$request->isPost()) {
            return $this->redirect($this->url('home.index'));
        }

        $userId = $this->user->getId();

        // použijeme API Request::post()
        $post = $request->post();
        $gameId = isset($post['game_id']) ? (int)$post['game_id'] : 0;

        if ($gameId > 0) {
            Wishlist::addGame($userId, $gameId);
        }

        return $this->redirect($this->url('home.index'));
    }

    public function remove(Request $request): Response
    {
        // rovnako tu – povolíme iba POST
        if (!$request->isPost()) {
            return $this->redirect($this->url('wishlist.index'));
        }

        $userId = $this->user->getId();

        $post = $request->post();
        $gameId = isset($post['game_id']) ? (int)$post['game_id'] : 0;

        if ($gameId > 0) {
            Wishlist::removeGame($userId, $gameId);
        }

        return $this->redirect($this->url('wishlist.index'));
    }
}
