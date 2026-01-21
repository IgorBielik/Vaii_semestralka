<?php
/*vypracované pomocou AI*/
namespace App\Models;

use Framework\Core\Model;
use Framework\DB\Connection;
use PDO;

class GamePlatform extends Model
{
    protected static ?string $tableName = 'game_platform';
    protected static ?string $primaryKey = "platform_id";

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

    public static function deleteByGame(int $gameId): void
    {
        $rows = static::getAll('game_id = :gid', ['gid' => $gameId]);
        foreach ($rows as $row) {
            /** @var self $row */
            $row->delete();
        }
    }

    public static function deleteByPlatform(int $platformId): void
    {
        $rows = static::getAll('platform_id = :pid', ['pid' => $platformId]);
        foreach ($rows as $row) {
            /** @var self $row */
            $row->delete();
        }
    }

    /** @return Platform[] */
    public static function getPlatformsForGame(int $gameId): array
    {
        if ($gameId <= 0) {
            return [];
        }
        $sql = 'SELECT p.* FROM platform p 
                JOIN game_platform gp ON gp.platform_id = p.id 
                WHERE gp.game_id = :gid
                ORDER BY p.name';
        $stmt = Connection::getInstance()->prepare($sql);
        $stmt->execute(['gid' => $gameId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Platform::class);
    }

    public static function replaceForGame(int $gameId, array $platformIds, ?string $releaseDate = null, ?float $priceEur = null): void
    {
        if ($gameId <= 0) {
            return;
        }

        $platformIds = array_values(array_unique(array_map('intval', $platformIds)));

        $pdo = Connection::getInstance();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('DELETE FROM game_platform WHERE game_id = :gid')->execute(['gid' => $gameId]);

            if (!empty($platformIds)) {
                $ins = $pdo->prepare('INSERT INTO game_platform (game_id, platform_id, release_date, price_eur) VALUES (:gid, :pid, :rdate, :price)');
                foreach ($platformIds as $pid) {
                    $ins->execute([
                        'gid' => $gameId,
                        'pid' => $pid,
                        'rdate' => $releaseDate,
                        'price' => $priceEur,
                    ]);
                }
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
