<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\Wishlist;
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

        $games = Game::getAll(null, [], $orderBy);

        // Zoznam game_id, ktoré má aktuálny používateľ vo wishliste
        $wishlistGameIds = [];
        if ($this->user->isLoggedIn()) {
            $items = Wishlist::forUser($this->user->getId());
            foreach ($items as $item) {
                /** @var Wishlist $item */
                $wishlistGameIds[] = $item->getGameId();
            }
        }

        return $this->html([
            'games'           => $games,
            'wishlistGameIds' => $wishlistGameIds,
            'order'           => $order,
            'dir'             => $dir,
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
