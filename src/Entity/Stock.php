<?php

namespace App\Entity;

use App\Repository\StockRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $title;

    #[ORM\OneToMany(targetEntity: Trade::class, mappedBy: 'stock')]
    private Collection $trades;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private string $secId;

    #[ORM\Column(type: 'float', precision: 5, scale: 6, nullable: true)]
    private ?float $price;

    #[ORM\Column(type: 'float', precision: 5, scale: 6, nullable: true)]
    private ?float $open;

    #[ORM\Column(type: 'float', precision: 5, scale: 6, nullable: true)]
    private ?float $high;

    #[ORM\Column(type: 'float', precision: 5, scale: 6, nullable: true)]
    private ?float $low;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $lotSize;

    #[ORM\Column(type: 'float', precision: 5, scale: 6, nullable: false)]
    private float $minStep;

    #[ORM\Column(type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'])]
    private DateTime $updated;

    public function __construct()
    {
        $this->title = '';
        $this->secId = '';
        $this->lotSize = 0;
        $this->minStep = 1;
        $this->updated = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Trade>
     */
    public function getTrades(): Collection
    {
        return $this->trades;
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getLotSize(): int
    {
        return $this->lotSize;
    }

    public function setLotSize(int $lotSize): static
    {
        $this->lotSize = $lotSize;

        return $this;
    }

    public function getMinStep(): float
    {
        return $this->minStep;
    }

    public function setMinStep(float $minStep): static
    {
        $this->minStep = $minStep;

        return $this;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function setUpdated(DateTime $updated): static
    {
        $this->updated = $updated;

        return $this;
    }

    public function getOpen(): ?float
    {
        return $this->open;
    }

    public function setOpen(?float $open): static
    {
        $this->open = $open;

        return $this;
    }

    public function getHigh(): ?float
    {
        return $this->high;
    }

    public function setHigh(?float $high): static
    {
        $this->high = $high;

        return $this;
    }

    public function getLow(): ?float
    {
        return $this->low;
    }

    public function setLow(?float $low): static
    {
        $this->low = $low;

        return $this;
    }
}
