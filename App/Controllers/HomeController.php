<?php
/*vypracované pomocou AI*/
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
        [$order, $dir, $orderBy] = $this->resolveSort($request);

        [$genreIds, $platformIds, $search, $page] = $this->readFilters($request);

        $allGames = $this->fetchGames($genreIds, $platformIds, $search, $orderBy);
        [$games, $page, $totalPages] = $this->paginateGames($allGames, $page, 5);

        [$wishlistIds, $wishlistMap] = $this->buildWishlistMap();

        $genres    = Genre::getAll();
        $platforms = Platform::getAll();

        $baseParams = $this->buildBaseParams($order, $dir, $genreIds, $platformIds, $search);
        $sortLinks  = $this->buildSortLinks($baseParams, $order, $dir);
        $pagination = $this->buildPagination($baseParams, $page, $totalPages);

        return $this->html([
            'games'             => $games,
            'wishlistGameIds'   => $wishlistIds,
            'wishlistMap'       => $wishlistMap,
            'order'             => $order,
            'dir'               => $dir,
            'genres'            => $genres,
            'platforms'         => $platforms,
            'selectedGenres'    => $genreIds,
            'selectedPlatforms' => $platformIds,
            'searchTerm'        => $search,
            'page'              => $page,
            'totalPages'        => $totalPages,
            'sortLinks'         => $sortLinks,
            'pagination'        => $pagination,
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

    private function resolveSort(Request $request): array
    {
        $orderParam = $request->get('order') ?? 'date';
        $dirParam   = strtolower($request->get('dir') ?? 'asc');
        $dir        = $dirParam === 'desc' ? 'desc' : 'asc';
        $dirSql     = strtoupper($dir);

        switch ($orderParam) {
            case 'name':
                return ['name', $dir, "name $dirSql"];
            case 'price':
                return ['price', $dir, "base_price_eur $dirSql"];
            case 'date':
            default:
                return ['date', $dir, "(global_release_date IS NULL), global_release_date $dirSql"];
        }
    }

    private function readFilters(Request $request): array
    {
        $genreIds    = array_values(array_filter(array_map('intval', (array)($request->get('genres') ?? []))));
        $platformIds = array_values(array_filter(array_map('intval', (array)($request->get('platforms') ?? []))));
        $searchRaw   = $request->get('search');
        $search      = $searchRaw !== null ? trim((string)$searchRaw) : null;
        $page        = max(1, (int)($request->get('page') ?? 1));

        return [$genreIds, $platformIds, $search, $page];
    }

    private function fetchGames(array $genreIds, array $platformIds, ?string $search, string $orderBy): array
    {
        $hasFilters = !empty($genreIds) || !empty($platformIds) || ($search !== null && $search !== '');
        return $hasFilters
            ? Game::filterGames($genreIds, $platformIds, $search)
            : Game::getAll(null, [], $orderBy);
    }

    private function paginateGames(array $allGames, int $page, int $perPage): array
    {
        $totalGames = count($allGames);
        $totalPages = max(1, (int)ceil($totalGames / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;
        $games      = array_slice($allGames, $offset, $perPage);

        return [$games, $page, $totalPages];
    }

    private function buildWishlistMap(): array
    {
        $ids  = [];
        $map  = [];

        if ($this->user->isLoggedIn()) {
            $items = Wishlist::forUser($this->user->getId());
            foreach ($items as $item) {
                /** @var Wishlist $item */
                $gid      = $item->getGameId();
                $ids[]    = $gid;
                $map[$gid] = true;
            }
        }

        return [$ids, $map];
    }

    private function buildBaseParams(string $order, string $dir, array $genreIds, array $platformIds, ?string $search): array
    {
        $params = ['order' => $order, 'dir' => $dir];
        if ($search !== null && $search !== '') {
            $params['search'] = $search;
        }
        if (!empty($genreIds)) {
            $params['genres'] = $genreIds;
        }
        if (!empty($platformIds)) {
            $params['platforms'] = $platformIds;
        }
        return $params;
    }

    private function buildSortLinks(array $baseParams, string $order, string $dir): array
    {
        $links = [];
        foreach (['name', 'price', 'date'] as $col) {
            $nextDir   = ($order === $col && $dir === 'asc') ? 'desc' : 'asc';
            $links[$col] = $this->url('home.index', array_merge($baseParams, [
                'order' => $col,
                'dir'   => $nextDir,
                'page'  => 1,
            ]));
        }
        return $links;
    }

    private function buildPagination(array $baseParams, int $page, int $totalPages): array
    {
        $buildUrl = fn(int $targetPage) => $this->url('home.index', array_merge($baseParams, ['page' => $targetPage]));

        $pages = [];
        for ($p = 1; $p <= $totalPages; $p++) {
            $pages[] = [
                'number'   => $p,
                'url'      => $buildUrl($p),
                'isActive' => $p === $page,
            ];
        }

        return [
            'prev' => [
                'url'      => $page > 1 ? $buildUrl($page - 1) : '#',
                'disabled' => $page <= 1,
            ],
            'next' => [
                'url'      => $page < $totalPages ? $buildUrl($page + 1) : '#',
                'disabled' => $page >= $totalPages,
            ],
            'pages' => $pages,
        ];
    }
}
