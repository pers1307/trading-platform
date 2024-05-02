<?php

namespace App\Entity;

use App\Repository\TradeCloseWarningRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TradeCloseWarningRepository::class)]
class TradeCloseWarning
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Trade::class, inversedBy: 'tradeCloseWarning')]
    #[ORM\JoinColumn(name: 'trade_id', referencedColumnName: 'id')]
    private ?Trade $trade = null;

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

    public function getTrade(): ?Trade
    {
        return $this->trade;
    }

    public function setTrade(?Trade $trade): static
    {
        $this->trade = $trade;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }
}
