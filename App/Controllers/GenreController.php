<?php
/*vypracované pomocou AI*/
namespace App\Controllers;

use App\Models\Genre;
use App\Models\GameGenre;
use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class GenreController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        return $this->user->isLoggedIn()
            && $this->user->getRole() === 'admin';
    }

    public function index(Request $request): Response
    {
        // Správa žánrov prebieha v AdminController@index, tak sem presmerujeme
        return $this->redirect($this->url('admin.index'));
    }

    public function store(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->redirect($this->url('admin.index'));
        }

        $name = trim((string)($request->post('name') ?? ''));
        if ($name === '') {
            return $this->redirect($this->url('admin.index'));
        }

        $genre = new Genre();
        $genre->setName($name);
        $genre->save();

        return $this->redirect($this->url('admin.index'));
    }

    public function update(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->redirect($this->url('admin.index'));
        }

        $id = (int)($request->post('id') ?? 0);
        $name = trim((string)($request->post('name') ?? ''));

        if ($id <= 0 || $name === '') {
            return $this->redirect($this->url('admin.index'));
        }

        $genre = Genre::getOne($id);
        if (!$genre) {
            return $this->redirect($this->url('admin.index'));
        }

        $genre->setName($name);
        $genre->save();

        return $this->redirect($this->url('admin.index'));
    }

    public function delete(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->redirect($this->url('admin.index'));
        }

        $id = (int)($request->post('id') ?? 0);
        if ($id <= 0) {
            return $this->redirect($this->url('admin.index'));
        }

        $genre = Genre::getOne($id);
        if ($genre) {
            // Najprv odstránime všetky väzby v game_genre (priame SQL cez GameGenre)
            GameGenre::deleteByGenre($id);
            // Potom samotný žáner
            $genre->delete();
        }

        return $this->redirect($this->url('admin.index'));
    }
}
