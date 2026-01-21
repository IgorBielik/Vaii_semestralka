<?php
/*vypracované pomocou AI*/
namespace App\Controllers;

use App\Models\Wishlist;
use App\Models\Game;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\JsonResponse;

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

        // Doplníme k wishlist položkám informácie o hre a pripravíme dáta pre view
        $games = [];
        foreach ($items as $item) {
            /** @var Wishlist $item */
            $game = Game::getOne($item->getGameId());
            if ($game) {
                $games[] = [
                    'wishlist'   => $item,
                    'game'       => $game,
                    'name'       => $game->getName(),
                    'nameLower'  => strtolower($game->getName()),
                    'releaseDate'=> $game->getGlobalReleaseDate() ?? '',
                    'imageUrl'   => $game->getImageUrl() ?? '',
                    'removeUrl'  => $this->url('wishlist.remove'),
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

        $added = false;
        if ($gameId > 0) {
            $added = Wishlist::addGame($userId, $gameId);
        }

        // Ak ide o AJAX (fetch), vrátime JSON namiesto redirectu
        if ($request->isAjax()) {
            return new JsonResponse([
                'success' => $added,
                'inWishlist' => $added,
            ]);
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

        $removed = false;
        if ($gameId > 0) {
            $removed = Wishlist::removeGame($userId, $gameId);
        }

        if ($request->isAjax()) {
            return new JsonResponse([
                'success' => $removed,
                'inWishlist' => !$removed,
            ]);
        }

        return $this->redirect($this->url('wishlist.index'));
    }
}
