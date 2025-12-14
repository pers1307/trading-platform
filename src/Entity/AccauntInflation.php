<?php

namespace App\Entity;

use App\Repository\AccauntInflationRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccauntInflationRepository::class)]
#[ORM\Table(
    name: 'accaunt_inflation',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_accaunt_inflation_accaunt_date', columns: ['accaunt_id', 'date'])
    ],
    indexes: [
        new ORM\Index(name: 'idx_accaunt_inflation_date', columns: ['date'])
    ]
)]
class AccauntInflation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Accaunt::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Accaunt $accaunt;

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeInterface $date;

    #[ORM\Column(type: 'float', options: ['default' => 0])]
    private float $movementAmount = 0.0;

    #[ORM\Column(type: 'float')]
    private float $centralBankKeyRate;

    #[ORM\Column(type: 'float')]
    private float $accauntInflationBalance;

    #[ORM\Column(type: 'float')]
    private float $depositRate;

    #[ORM\Column(type: 'float')]
    private float $accauntDepositBalance;

    #[ORM\Column(type: 'float')]
    private float $accauntBalance;

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

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMovementAmount(): float
    {
        return $this->movementAmount;
    }

    public function setMovementAmount(float $movementAmount): self
    {
        $this->movementAmount = $movementAmount;

        return $this;
    }

    public function getCentralBankKeyRate(): float
    {
        return $this->centralBankKeyRate;
    }

    public function setCentralBankKeyRate(float $centralBankKeyRate): self
    {
        $this->centralBankKeyRate = $centralBankKeyRate;

        return $this;
    }

    public function getAccauntInflationBalance(): float
    {
        return $this->accauntInflationBalance;
    }

    public function setAccauntInflationBalance(float $accauntInflationBalance): self
    {
        $this->accauntInflationBalance = $accauntInflationBalance;

        return $this;
    }

    public function getDepositRate(): float
    {
        return $this->depositRate;
    }

    public function setDepositRate(float $depositRate): self
    {
        $this->depositRate = $depositRate;

        return $this;
    }

    public function getAccauntDepositBalance(): float
    {
        return $this->accauntDepositBalance;
    }

    public function setAccauntDepositBalance(float $accauntDepositBalance): self
    {
        $this->accauntDepositBalance = $accauntDepositBalance;

        return $this;
    }

    public function getAccauntBalance(): float
    {
        return $this->accauntBalance;
    }

    public function setAccauntBalance(float $accauntBalance): self
    {
        $this->accauntBalance = $accauntBalance;

        return $this;
    }
}
