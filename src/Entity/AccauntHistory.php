<?php

namespace App\Entity;

use App\Repository\AccauntHistoryRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccauntHistoryRepository::class)]
class AccauntHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Accaunt::class, inversedBy: 'accauntHistories')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Accaunt $accaunt;

    #[ORM\Column(type: 'float', precision: 6, scale: 2)]
    private float $balance;

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

    public function setAccaunt(?Accaunt $accaunt): self
    {
        $this->accaunt = $accaunt;

        return $this;
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

    public function getCreated(): DateTime
    {
        return $this->created;
    }
}
