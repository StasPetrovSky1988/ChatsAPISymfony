<?php

namespace App\DTO;

use App\Entity\Chat;
use DateTimeImmutable;

class ChatDTO
{
    public int $id;
    public DateTimeImmutable $cratedAt;

    public function __construct(Chat $chat)
    {
        $this->id = $chat->getId();
        $this->cratedAt = $chat->getCreatedAt();
    }
}