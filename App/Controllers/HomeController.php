<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\Wishlist;
use App\Models\Genre;
use App\Models\Platform;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class HomeController
 * Handles actions related to the home page and other public actions.
 *
 * This controller includes actions that are accessible to all users, including a default landing page and a contact
 * page. It provides a mechanism for authorizing actions based on user permissions.
 *
 * @package App\Controllers
 */
class HomeController extends BaseController
{
    /**
     * Authorizes controller actions based on the specified action name.
     *
     * In this implementation, all actions are authorized unconditionally.
     *
     * @param string $action The action name to authorize.
     * @return bool Returns true, allowing all actions.
     */
    public function authorize(Request $request, string $action): bool
    {
        return true;
    }

    /**
     * Displays the default home page.
     *
     * This action serves the main HTML view of the home page.
     *
     * @return Response The response object containing the rendered HTML for the home page.
     */
    public function index(Request $request): Response
    {
        $order = $request->get('order') ?? 'date';   // name|price|date
        $dir   = strtolower($request->get('dir') ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

        // základný ORDER BY podľa parametrov
        switch ($order) {
            case 'name':
                $orderBy = "name $dir";
                break;
            case 'price':
                $orderBy = "base_price_eur $dir";
                break;
            case 'date':
            default:
                // najprv hry s najbližším dátumom, NULL (TBA) na koniec
                $orderBy = "(global_release_date IS NULL), global_release_date $dir";
                $order   = 'date';
                break;
        }

        // Pagination params
        $perPage = 5;
        $page = max(1, (int)($request->get('page') ?? 1));

        // Filters from GET
        $genreIds = $request->get('genres') ?? [];
        $platformIds = $request->get('platforms') ?? [];
        $search = $request->get('search') ?? null;

        $genreIds = array_values(array_filter(array_map('intval', (array)$genreIds)));
        $platformIds = array_values(array_filter(array_map('intval', (array)$platformIds)));
        $search = $search !== null ? trim((string)$search) : null;

        // Get all matching games (for now) and then paginate in PHP
        if (!empty($genreIds) || !empty($platformIds) || ($search !== null && $search !== '')) {
            $allGames = Game::filterGames($genreIds, $platformIds, $search);
        } else {
            $allGames = Game::getAll(null, [], $orderBy);
        }

        $totalGames = count($allGames);
        $totalPages = max(1, (int)ceil($totalGames / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;
        $games = array_slice($allGames, $offset, $perPage);

        // Zoznam game_id, ktoré má aktuálny používateľ vo wishliste
        $wishlistGameIds = [];
        if ($this->user->isLoggedIn()) {
            $items = Wishlist::forUser($this->user->getId());
            foreach ($items as $item) {
                /** @var Wishlist $item */
                $wishlistGameIds[] = $item->getGameId();
            }
        }

        // Pre filter navbar na hlavnej stránke – všetky žánre a platformy
        $genres    = Genre::getAll();
        $platforms = Platform::getAll();

        return $this->html([
            'games'             => $games,
            'wishlistGameIds'   => $wishlistGameIds,
            'order'             => $order,
            'dir'               => $dir,
            'genres'            => $genres,
            'platforms'         => $platforms,
            'selectedGenres'    => $genreIds,
            'selectedPlatforms' => $platformIds,
            'searchTerm'        => $search,
            'page'              => $page,
            'totalPages'        => $totalPages,
        ]);
    }

    /**
     * Displays the contact page.
     *
     * This action serves the HTML view for the contact page, which is accessible to all users without any
     * authorization.
     *
     * @return Response The response object containing the rendered HTML for the contact page.
     */
    public function contact(Request $request): Response
    {
        return $this->html();
    }
}
