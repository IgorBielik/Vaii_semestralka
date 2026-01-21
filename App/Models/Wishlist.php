<?php
/*vypracované pomocou AI*/
namespace App\Models;

use Framework\Core\Model;

class Wishlist extends Model
{
    protected static ?string $tableName = 'wishlist';
    protected static ?string $primaryKey = 'id';

    protected ?int $id = null;
    protected int $user_id;
    protected int $game_id;
    protected ?string $added_at = null; // datetime string

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getGameId(): int
    {
        return $this->game_id;
    }

    public function getAddedAt(): ?string
    {
        return $this->added_at;
    }

    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }

    public function setGameId(int $gameId): void
    {
        $this->game_id = $gameId;
    }

    public function setAddedAt(?string $addedAt): void
    {
        $this->added_at = $addedAt;
    }

    public static function forUser(int $userId): array
    {
        return static::getAll('user_id = :uid', ['uid' => $userId]);
    }

    public static function exists(int $userId, int $gameId): bool
    {
        $items = static::getAll('user_id = :uid AND game_id = :gid', ['uid' => $userId, 'gid' => $gameId], limit: 1);
        return !empty($items);
    }

    public static function addGame(int $userId, int $gameId): bool
    {
        // Ak už hra vo wishliste je, nič nerobíme
        if (static::exists($userId, $gameId)) {
            return false;
        }

        $wishlist = new self();
        $wishlist->setUserId($userId);
        $wishlist->setGameId($gameId);
        $wishlist->setAddedAt(date('Y-m-d H:i:s'));
        $wishlist->save();
        return true;
    }

    public static function removeGame(int $userId, int $gameId): bool
    {
        // Nájdeme záznam pre daného používateľa a hru
        $items = static::getAll('user_id = :uid AND game_id = :gid', ['uid' => $userId, 'gid' => $gameId], limit: 1);
        if (empty($items)) {
            return false;
        }

        /** @var self $item */
        $item = $items[0];
        $item->delete();
        return true;
    }

    /**
     * Delete all wishlist entries for a specific game.
     */
    public static function deleteByGame(int $gameId): void
    {
        $items = static::getAll('game_id = :gid', ['gid' => $gameId]);
        foreach ($items as $item) {
            /** @var self $item */
            $item->delete();
        }
    }
}
