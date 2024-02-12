<?php

namespace App\Entity;

use App\Repository\AccauntRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccauntRepository::class)]
class Accaunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255)]
    private string $brockerTitle;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $created;

    #[ORM\OneToMany(targetEntity: AccauntHistory::class, mappedBy: 'accauntHistory')]
    private Collection $accauntHistories;

    public function __construct()
    {
        $this->title = '';
        $this->brockerTitle = '';
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

    public function getBrockerTitle(): ?string
    {
        return $this->brockerTitle;
    }

    public function setBrockerTitle(string $brockerTitle): static
    {
        $this->brockerTitle = $brockerTitle;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, AccauntHistory>
     */
    public function getAccauntHistories(): Collection
    {
        return $this->accauntHistories;
    }
}
