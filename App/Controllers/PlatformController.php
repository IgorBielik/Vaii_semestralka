<?php

namespace App\Controllers;

use App\Models\Platform;
use App\Models\GamePlatform;
use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class PlatformController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        $identity = $this->user->getIdentity();
        return $identity instanceof User && $identity->isAdmin();
    }

    public function index(Request $request): Response
    {
        // Správa platforiem prebieha v AdminController@index, tak sem presmerujeme
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

        $platform = new Platform();
        $platform->setName($name);
        $platform->save();

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

        $platform = Platform::getOne($id);
        if (!$platform) {
            return $this->redirect($this->url('admin.index'));
        }

        $platform->setName($name);
        $platform->save();

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

        $platform = Platform::getOne($id);
        if ($platform) {
            // Najprv odstránime všetky väzby v game_platform (priame SQL cez GamePlatform)
            GamePlatform::deleteAllByPlatform($id);
            // Potom samotnú platformu
            $platform->delete();
        }

        return $this->redirect($this->url('admin.index'));
    }
}
