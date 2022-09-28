<?php

namespace App\Dto;

use App\Entity\User;

class ChatParticipantDto
{
    public string $id;
    public string $name;

    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->name = $user->getName();
    }
}