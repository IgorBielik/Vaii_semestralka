<?php

namespace App\Models;

use Framework\Core\Model;
use Framework\DB\Connection;

class GameGenre extends Model
{
    protected static ?string $tableName = 'game_genre';
    protected static ?string $primaryKey = null; // composite key (game_id, genre_id)

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

    public static function deleteByGenre(int $genreId): void
    {
        $rows = static::getAll('genre_id = :gid', ['gid' => $genreId]);
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

    public static function deleteAllByGenre(int $genreId): void
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('DELETE FROM game_genre WHERE genre_id = :gid');
        $stmt->execute(['gid' => $genreId]);
    }
}
