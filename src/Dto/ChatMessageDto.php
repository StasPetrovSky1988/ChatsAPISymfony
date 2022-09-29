<?php

namespace App\Dto;

use App\Entity\Message;
use DateTimeImmutable;

class ChatMessageDto
{
    public int $id;

    public DateTimeImmutable $createdAt;

    public string $content;

    public int $chatId;

    /** @var ChatParticipantDto $user; */
    public ChatParticipantDto $user;

    public function __construct(Message $message)
    {
        $this->id = $message->getId();
        $this->createdAt = $message->getCreatedAt();
        $this->content = $message->getContent();
        $this->user = $message->getUser()->getDTO();
        $this->chatId = $message->getChat()->getId();
    }
}