<?php

namespace App\Models;

use Framework\Core\Model;

class Genre extends Model
{
    protected static ?string $tableName = 'game_genres';
    protected static ?string $primaryKey = 'id';

    protected ?int $id = null;
    protected int $game_id;
    protected string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameId(): int
    {
        return $this->game_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setGameId(int $gameId): void
    {
        $this->game_id = $gameId;
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    public static function findByGame(int $gameId): array
    {
        return static::getAll('game_id = :gid', ['gid' => $gameId]);
    }
}
