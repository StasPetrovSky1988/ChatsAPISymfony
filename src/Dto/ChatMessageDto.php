<?php

namespace App\Dto;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\User;
use DateTimeImmutable;

class ChatMessageDto
{
    public int $id;
    public DateTimeImmutable $createdAt;
    public string $content;
    public string $username;
    public int $chatId;

    public function __construct(Message $message)
    {
        $this->id = $message->getId();
        $this->createdAt = $message->getCreatedAt();
        $this->content = $message->getContent();
        $this->username = $message->getUser()->getName();
        $this->chatId = $message->getChat()->getId();
    }
}