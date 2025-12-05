<?php

namespace App\Models;

use Framework\Core\Model;

class Platform extends Model
{
    protected static ?string $tableName = 'game_platforms';
    protected static ?string $primaryKey = 'id';

    protected ?int $id = null;
    protected int $game_id;
    protected string $name;
    protected ?string $release_date = null; // Y-m-d per platform
    protected ?float $price_eur = null;

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

    public function getReleaseDate(): ?string
    {
        return $this->release_date;
    }

    public function getPriceEur(): ?float
    {
        return $this->price_eur;
    }

    public function setGameId(int $gameId): void
    {
        $this->game_id = $gameId;
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    public function setReleaseDate(?string $releaseDate): void
    {
        $this->release_date = $releaseDate;
    }

    public function setPriceEur(?float $price): void
    {
        $this->price_eur = $price;
    }

    public static function findByGame(int $gameId): array
    {
        return static::getAll('game_id = :gid', ['gid' => $gameId]);
    }
}

