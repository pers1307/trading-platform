<?php

namespace App\Service\AccauntInflation;

use App\Entity\Accaunt;
use DateTimeInterface;

final readonly class AccauntInflationRequest
{
    public function __construct(
        public Accaunt $accaunt,
        public float $movementAmount,
        public DateTimeInterface $date,
    ) {
    }
}
