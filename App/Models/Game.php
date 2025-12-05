<?php

namespace App\Models;

use Framework\Core\Model;

class Game extends Model
{
    protected static ?string $tableName = 'games';
    protected static ?string $primaryKey = 'id';

    // Core fields mapped to DB columns
    protected ?int $id = null;
    protected string $title;
    protected ?string $publisher = null;
    protected ?string $release_date = null; // stored as Y-m-d string from DB
    protected ?float $price_eur = null;
    protected bool $is_dlc = false;
    protected bool $is_early_access = false;

    // Basic getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function getReleaseDate(): ?string
    {
        return $this->release_date;
    }

    public function getPriceEur(): ?float
    {
        return $this->price_eur;
    }

    public function isDlc(): bool
    {
        return $this->is_dlc;
    }

    public function isEarlyAccess(): bool
    {
        return $this->is_early_access;
    }

    // Setters with simple normalization/validation hooks
    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function setPublisher(?string $publisher): void
    {
        $this->publisher = $publisher !== null ? trim($publisher) : null;
    }

    public function setReleaseDate(?string $releaseDate): void
    {
        $this->release_date = $releaseDate;
    }

    public function setPriceEur(?float $price): void
    {
        $this->price_eur = $price;
    }

    public function setIsDlc(bool $isDlc): void
    {
        $this->is_dlc = $isDlc;
    }

    public function setIsEarlyAccess(bool $isEarly): void
    {
        $this->is_early_access = $isEarly;
    }
}

