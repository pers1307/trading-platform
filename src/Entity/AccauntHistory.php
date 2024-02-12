<?php

namespace App\Entity;

use App\Repository\AccauntHistoryRepository;
use DateTimeImmutable;
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
    private float $value;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $created;

    public function __construct()
    {
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

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }
}
