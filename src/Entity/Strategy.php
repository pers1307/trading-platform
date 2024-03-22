<?php

namespace App\Entity;

use App\Repository\StrategyRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StrategyRepository::class)]
class Strategy
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSE = 'pause';
    public const STATUS_DECOMMISSION = 'decommission';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(type: 'text', length: 65535, nullable: false)]
    private string $description;

    #[ORM\Column(type: 'string', nullable: false, options: ['default' => 'active'], columnDefinition: "ENUM('active', 'pause', 'decommission')")]
    private string $status;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $created;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'])]
    private DateTime $updated;

    #[ORM\OneToMany(targetEntity: Trade::class, mappedBy: 'strategy')]
    private Collection $trades;

    #[ORM\OneToMany(targetEntity: RiskProfile::class, mappedBy: 'strategy')]
    private Collection $riskProfiles;

    public function __construct()
    {
        $this->title = '';
        $this->description = '';
        $this->created = new DateTime();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    /**
     * @return Collection<int, Trade>
     */
    public function getTrades(): Collection
    {
        return $this->trades;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, RiskProfile>
     */
    public function getRiskProfiles(): Collection
    {
        return $this->riskProfiles;
    }
}
