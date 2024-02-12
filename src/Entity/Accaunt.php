<?php

namespace App\Entity;

use App\Repository\AccauntRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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
    private DateTime $created;

    #[ORM\OneToMany(targetEntity: AccauntHistory::class, mappedBy: 'accaunt')]
    private Collection $accauntHistories;

    public function __construct()
    {
        $this->title = '';
        $this->brockerTitle = '';
        $this->accauntHistories = new ArrayCollection();
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

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @return Collection<int, AccauntHistory>
     */
    public function getAccauntHistories(): Collection
    {
        return $this->accauntHistories;
    }
}
