<?php

namespace App\Models;

use Framework\Core\Model;
use Framework\DB\Connection;

class GamePlatform extends Model
{
    protected static ?string $tableName = 'game_platform';
    protected static ?string $primaryKey = null; // composite key (game_id, platform_id)

    // DB columns
    protected int $game_id;
    protected int $platform_id;
    protected ?string $release_date = null; // YYYY-MM-DD or null
    protected ?float $price_eur = null;

    public function getGameId(): int
    {
        return $this->game_id;
    }

    public function getPlatformId(): int
    {
        return $this->platform_id;
    }

    public function setGameId(int $gameId): void
    {
        $this->game_id = $gameId;
    }

    public function setPlatformId(int $platformId): void
    {
        $this->platform_id = $platformId;
    }

    // Optional getters/setters for extra fields if needed later
    public function getReleaseDate(): ?string
    {
        return $this->release_date;
    }

    public function setReleaseDate(?string $date): void
    {
        $this->release_date = $date;
    }

    public function getPriceEur(): ?float
    {
        return $this->price_eur;
    }

    public function setPriceEur(?float $price): void
    {
        $this->price_eur = $price;
    }

    public static function deleteByPlatform(int $platformId): void
    {
        $rows = static::getAll('platform_id = :pid', ['pid' => $platformId]);
        foreach ($rows as $row) {
            /** @var self $row */
            $row->delete();
        }
    }

    public static function deleteByGame(int $gameId): void
    {
        $rows = static::getAll('game_id = :gid', ['gid' => $gameId]);
        foreach ($rows as $row) {
            /** @var self $row */
            $row->delete();
        }
    }

    public static function deleteAllByPlatform(int $platformId): void
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('DELETE FROM game_platform WHERE platform_id = :pid');
        $stmt->execute(['pid' => $platformId]);
    }
}
