<?php
/*vypracované pomocou AI*/
namespace App\Models;

use Framework\Core\Model;
use Framework\DB\Connection;
use PDO;

class GameGenre extends Model
{
    protected static ?string $tableName = 'game_genre';
    protected static ?string $primaryKey = "genre_id";

    protected int $game_id;
    protected int $genre_id;

    public function getGameId(): int
    {
        return $this->game_id;
    }

    public function getGenreId(): int
    {
        return $this->genre_id;
    }

    public function setGameId(int $gameId): void
    {
        $this->game_id = $gameId;
    }

    public function setGenreId(int $genreId): void
    {
        $this->genre_id = $genreId;
    }

    public static function deleteByGame(int $gameId): void
    {
        $rows = static::getAll('game_id = :gid', ['gid' => $gameId]);
        foreach ($rows as $row) {
            /** @var self $row */
            $row->delete();
        }
    }

    public static function deleteByGenre(int $genreId): void
    {
        $rows = static::getAll('genre_id = :gid', ['gid' => $genreId]);
        foreach ($rows as $row) {
            /** @var self $row */
            $row->delete();
        }
        /*$pdo = Connection::getInstance();
        $stmt = $pdo->prepare('DELETE FROM game_genre WHERE genre_id = :gid');
        $stmt->execute(['gid' => $genreId]);*/
    }

    public static function getGenresForGame(int $gameId): array
    {
        if ($gameId <= 0) {
            return [];
        }

        $sql = 'SELECT g.* FROM genre g
                JOIN game_genre gg ON gg.genre_id = g.id
                WHERE gg.game_id = :gid
                ORDER BY g.name';
        $stmt = Connection::getInstance()->prepare($sql);
        $stmt->execute(['gid' => $gameId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Genre::class);
    }

    public static function replaceForGame(int $gameId, array $genreIds): void
    {
        if ($gameId <= 0) {
            return;
        }

        $genreIds = array_values(array_unique(array_map('intval', $genreIds)));

        $pdo = Connection::getInstance();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('DELETE FROM game_genre WHERE game_id = :gid')->execute(['gid' => $gameId]);

            if (!empty($genreIds)) {
                $ins = $pdo->prepare('INSERT INTO game_genre (game_id, genre_id) VALUES (:gid, :genre_id)');
                foreach ($genreIds as $gid) {
                    $ins->execute(['gid' => $gameId, 'genre_id' => $gid]);
                }
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
