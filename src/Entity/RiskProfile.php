<?php

namespace App\Entity;

use App\Repository\RiskProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RiskProfileRepository::class)]
class RiskProfile
{
    public const TYPE_DEPOSIT = 'deposite';
    public const TYPE_TRADE = 'trade';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Accaunt::class, inversedBy: 'riskProfiles')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Accaunt $accaunt;

    #[ORM\ManyToOne(targetEntity: Strategy::class, inversedBy: 'riskProfile')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Strategy $strategy;

    #[ORM\Column(type: 'float', precision: 6, scale: 2)]
    private float $balance;

    #[ORM\Column(type: 'string', nullable: false, options: ['default' => 'deposite'], columnDefinition: "ENUM('deposite', 'trade')")]
    private string $type;

    #[ORM\Column(type: 'float', precision: 6, scale: 2)]
    private float $persent;

    public function __construct()
    {
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getPersent(): float
    {
        return $this->persent;
    }

    public function setPersent(float $persent): static
    {
        $this->persent = $persent;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccaunt(): Accaunt
    {
        return $this->accaunt;
    }

    public function setAccaunt(Accaunt $accaunt): static
    {
        $this->accaunt = $accaunt;

        return $this;
    }

    public function getStrategy(): Strategy
    {
        return $this->strategy;
    }

    public function setStrategy(Strategy $strategy): static
    {
        $this->strategy = $strategy;

        return $this;
    }
}
