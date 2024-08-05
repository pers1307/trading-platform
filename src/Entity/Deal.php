<?php

namespace App\Entity;

use App\Repository\DealRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: DealRepository::class)]
class Deal
{
    public const TYPE_LONG = 'long';
    public const TYPE_SHORT = 'short';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $transactionId;

    #[ORM\ManyToOne(targetEntity: Accaunt::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "NO ACTION")]
    private Accaunt $accaunt;

    #[ORM\ManyToOne(targetEntity: Stock::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "NO ACTION")]
    private ?Stock $stock;

    // Поле на случай, если инструмент определить не удалось
    #[ORM\Column(length: 255, nullable: true)]
    private string $secId;

    #[ORM\Column(type: 'string', nullable: false, options: ['default' => 'long'], columnDefinition: "ENUM('long', 'short')")]
    private string $type;

    #[ORM\Column(type: 'float', precision: 5, scale: 6, nullable: false)]
    private float $price;

    #[ORM\Column]
    private int $lots;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private DateTime $dateTime;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $created;

    public function __construct()
    {
        $this->created = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccaunt(): Accaunt
    {
        return $this->accaunt;
    }

    public function setAccaunt(Accaunt $accaunt): self
    {
        $this->accaunt = $accaunt;

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getSecId(): string
    {
        return $this->secId;
    }

    public function setSecId(string $secId): static
    {
        $this->secId = $secId;

        return $this;
    }

    public function getStockSecId(): string
    {
        if (!is_null($this->stock)) {
            return $this->stock->getSecId();
        }

        return $this->secId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @throws Exception
     */
    public function setType(string $type): static
    {
        if (
            self::TYPE_LONG !== $type
            && self::TYPE_SHORT !== $type
        ) {
            throw new Exception("Не допустимый тип");
        }

        $this->type = $type;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getLots(): int
    {
        return $this->lots;
    }

    public function setLots(int $lots): static
    {
        $this->lots = $lots;

        return $this;
    }

    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(DateTime $dateTime): static
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getTransactionId(): int
    {
        return $this->transactionId;
    }

    public function setTransactionId(int $transactionId): static
    {
        $this->transactionId = $transactionId;

        return $this;
    }
}
