<?php
/*vypracované pomocou AI*/
namespace App\Models;

use Framework\Core\Model;
use Framework\DB\Connection;
use PDO;

class Game extends Model
{
    protected static ?string $tableName = 'game';
    protected static ?string $primaryKey = 'id';

    // Core fields mapped to DB columns
    protected ?int $id = null;
    protected string $name;
    protected ?string $publisher = null;
    protected ?string $global_release_date = null; // stored as Y-m-d string from DB
    protected ?float $base_price_eur = null;
    // store booleans as 0/1 ints to match tinyint DB columns and avoid '' issues
    protected int $is_dlc = 0;
    protected int $is_early_access = 0;
    protected ?string $description = null;
    protected ?string $image_url = null; // filename or path to cover image

    // Basic getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function getGlobalReleaseDate(): ?string
    {
        return $this->global_release_date;
    }

    public function getBasePriceEur(): ?float
    {
        return $this->base_price_eur;
    }

    public function isDlc(): bool
    {
        return (bool)$this->is_dlc;
    }

    public function isEarlyAccess(): bool
    {
        return (bool)$this->is_early_access;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    /**
     * Convenience for views: if image_url is null/empty, return empty string.
     */
    public function getImageUrlOrEmpty(): string
    {
        return $this->image_url ?: '';
    }

    // Setters with simple normalization/validation hooks
    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    public function setPublisher(?string $publisher): void
    {
        $this->publisher = $publisher !== null ? trim($publisher) : null;
    }

    public function setGlobalReleaseDate(?string $releaseDate): void
    {
        $this->global_release_date = $releaseDate;
    }

    public function setBasePriceEur(?float $price): void
    {
        $this->base_price_eur = $price;
    }

    public function setIsDlc(bool $isDlc): void
    {
        $this->is_dlc = $isDlc ? 1 : 0;
    }

    public function setIsEarlyAccess(bool $isEarly): void
    {
        $this->is_early_access = $isEarly ? 1 : 0;
    }

    public function setImageUrl(?string $url): void
    {
        $this->image_url = $url !== null ? trim($url) : null;
    }

    // --- New helpers for N:M relations ---

    /** @return Genre[] */
    public function getGenres(): array
    {
        if ($this->id === null) {
            return [];
        }
        return GameGenre::getGenresForGame($this->id);
    }

    /**
     * Replace all genre links for this game with the given list of genre IDs.
     *
     * @param int[] $genreIds
     */
    public function syncGenres(array $genreIds): void
    {
        if ($this->id === null) {
            return;
        }

        GameGenre::replaceForGame($this->id, $genreIds);
    }

    /** @return Platform[] */
    public function getPlatforms(): array
    {
        if ($this->id === null) {
            return [];
        }
        return GamePlatform::getPlatformsForGame($this->id);
    }

    /**
     * Replace all platform links for this game with the given list of platform IDs,
     * using the same release date / price for all, or leaving them null if not provided.
     *
     * @param int[] $platformIds
     * @param string|null $releaseDate
     * @param float|null $priceEur
     */
    public function syncPlatforms(array $platformIds, ?string $releaseDate = null, ?float $priceEur = null): void
    {
        if ($this->id === null) {
            return;
        }

        GamePlatform::replaceForGame($this->id, $platformIds, $releaseDate, $priceEur);
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Delete this game and all related records (game_genre, game_platform, wishlist).
     * Uses respective models to handle deletions.
     * Returns true on success, false if game has no ID.
     */
    public function deleteWithRelations(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $pdo = Connection::getInstance();
        $pdo->beginTransaction();
        try {
            // Delete relations using their respective models
            GameGenre::deleteByGame($this->id);
            GamePlatform::deleteByGame($this->id);
            Wishlist::deleteByGame($this->id);

            // Finally delete the game itself
            $this->delete();

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }


    /**
     * Filter games by selected genre IDs, platform IDs and optional search term.
     * All selected genres and all selected platforms must be present for a game to match.
     *
     * @param int[] $genreIds
     * @param int[] $platformIds
     * @param string|null $search
     * @return Game[]
     */
    public static function filterGames(array $genreIds = [], array $platformIds = [], ?string $search = null): array
    {
        $genreIds = array_values(array_filter(array_map('intval', $genreIds)));
        $platformIds = array_values(array_filter(array_map('intval', $platformIds)));
        $search = $search !== null ? trim($search) : '';

        $params = [];
        $joins = [];
        $wheres = [];
        $havingParts = [];

        if (!empty($genreIds)) {
            $placeholders = implode(',', array_fill(0, count($genreIds), '?'));
            $joins[] = "LEFT JOIN game_genre gg ON gg.game_id = g.id AND gg.genre_id IN ($placeholders)";
            $havingParts[] = 'COUNT(DISTINCT gg.genre_id) = ' . count($genreIds);
            $params = array_merge($params, $genreIds);
        }

        if (!empty($platformIds)) {
            $placeholders = implode(',', array_fill(0, count($platformIds), '?'));
            $joins[] = "LEFT JOIN game_platform gp ON gp.game_id = g.id AND gp.platform_id IN ($placeholders)";
            $havingParts[] = 'COUNT(DISTINCT gp.platform_id) = ' . count($platformIds);
            $params = array_merge($params, $platformIds);
        }

        if ($search !== '') {
            $wheres[] = '(g.name LIKE ? OR g.publisher LIKE ?)';

            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql = 'SELECT g.* FROM game g ' . implode(' ', $joins);
        if (!empty($wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $wheres);
        }
        if (!empty($havingParts)) {
            $sql .= ' GROUP BY g.id HAVING ' . implode(' AND ', $havingParts);
        } else {
            $sql .= ' GROUP BY g.id';
        }
        $sql .= ' ORDER BY (g.global_release_date IS NULL), g.global_release_date ASC';

        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, self::class);
    }
}
