<?php
/*vypracované pomocou AI*/
namespace App\Models;

use Framework\Core\Model;

class Genre extends Model
{
    protected static ?string $tableName = 'genre';
    protected static ?string $primaryKey = 'id';
    protected ?string $description = null;

    protected ?int $id = null;
    protected string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
