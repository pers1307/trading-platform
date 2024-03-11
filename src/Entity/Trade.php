<?php

namespace App\Entity;

use App\Repository\TradeRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TradeRepository::class)]
class Trade
{
    public const TYPE_LONG = 'long';
    public const TYPE_SHORT = 'short';

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSE = 'close';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Stock::class, inversedBy: 'trades')]
    #[ORM\JoinColumn(nullable: false, onDelete: "NO ACTION")]
    private Stock $stock;

    #[ORM\ManyToOne(targetEntity: Accaunt::class, inversedBy: 'trades')]
    #[ORM\JoinColumn(nullable: false, onDelete: "NO ACTION")]
    private Accaunt $accaunt;

    #[ORM\ManyToOne(targetEntity: Strategy::class, inversedBy: 'trades')]
    #[ORM\JoinColumn(nullable: false, onDelete: "NO ACTION")]
    private Strategy $strategy;

    #[ORM\Column(type: 'string', nullable: false, options: ['default' => 'long'], columnDefinition: "ENUM('long', 'short')")]
    private string $type;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private DateTime $openDateTime;

    #[ORM\Column(type: 'float', precision: 5, scale: 6)]
    private float $openPrice;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $closeDateTime;

    #[ORM\Column(type: 'float', precision: 5, scale: 6, nullable: true)]
    private ?float $closePrice;

    #[ORM\Column(type: 'float', precision: 5, scale: 6, nullable: true)]
    private ?float $stopLoss;

    #[ORM\Column(type: 'float', precision: 5, scale: 6, nullable: true)]
    private ?float $takeProfit;

    #[ORM\Column]
    private int $lots;

    #[ORM\Column(type: 'string', nullable: false, options: ['default' => 'open'], columnDefinition: "ENUM('open', 'close')")]
    private string $status;

    #[ORM\Column(type: 'text', length: 65535, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $created;

    public function __construct()
    {
        $this->openDateTime = new DateTime();
        $this->created = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStock(): Stock
    {
        return $this->stock;
    }

    public function setStock(Stock $stock): self
    {
        $this->stock = $stock;

        return $this;
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

    public function getStrategy(): Strategy
    {
        return $this->strategy;
    }

    public function setStrategy(Strategy $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @throws \Exception
     */
    public function setType(string $type): static
    {
        if (
            self::TYPE_LONG !== $type
            && self::TYPE_SHORT !== $type
        ) {
            throw new \Exception("Не допустимый тип");
        }
        
        $this->type = $type;

        return $this;
    }

    public function getOpenDateTime(): DateTime
    {
        return $this->openDateTime;
    }

    public function setOpenDateTime(DateTime $openDateTime): static
    {
        $this->openDateTime = $openDateTime;

        return $this;
    }

    public function getOpenPrice(): float
    {
        return $this->openPrice;
    }

    public function setOpenPrice(float $openPrice): static
    {
        $this->openPrice = $openPrice;

        return $this;
    }

    public function getCloseDateTime(): ?DateTime
    {
        return $this->closeDateTime;
    }

    public function setCloseDateTime(?DateTime $closeDateTime): static
    {
        $this->closeDateTime = $closeDateTime;

        return $this;
    }

    public function getClosePrice(): ?float
    {
        return $this->closePrice;
    }

    public function setClosePrice(?float $closePrice): static
    {
        $this->closePrice = $closePrice;

        return $this;
    }

    public function getStopLoss(): ?float
    {
        return $this->stopLoss;
    }

    public function setStopLoss(?float $stopLoss): static
    {
        $this->stopLoss = $stopLoss;

        return $this;
    }

    public function getTakeProfit(): ?float
    {
        return $this->takeProfit;
    }

    public function setTakeProfit(?float $takeProfit): static
    {
        $this->takeProfit = $takeProfit;

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

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @throws \Exception
     */
    public function setStatus(string $status): static
    {
        if (
            self::STATUS_OPEN !== $status
            && self::STATUS_CLOSE !== $status
        ) {
            throw new \Exception("Не допустимый статус");
        }

        $this->status = $status;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }
}
