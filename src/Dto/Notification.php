<?php

namespace App\Dto;

class Notification
{
    public function __construct(
        private readonly string $title,
        private readonly string $text,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
