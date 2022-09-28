<?php

namespace App\DTO;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\User;
use DateTimeImmutable;

class MessageDTO
{
    public int $id;
    public DateTimeImmutable $createdAt;
    public string $content;
    public User $user;
    public Chat $chat;

    public function __construct(Message $message)
    {
        $this->id = $message->getId();
        $this->createdAt = $message->getCreatedAt();
        $this->content = $message->getContent();
        $this->user = $message->getUser();
        $this->chat = $message->getChat();
    }
}