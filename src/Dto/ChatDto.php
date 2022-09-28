<?php

namespace App\Dto;

use App\Entity\Chat;
use DateTimeImmutable;

class ChatDto
{
    public int $id;

    public DateTimeImmutable $cratedAt;

    /** @var ChatParticipantDto[] $participants; */
    public array $participants;

    /** @var ChatMessageDto[] $messages; */
    public array $messages;

    public function __construct(Chat $chat)
    {
        $this->id = $chat->getId();
        $this->cratedAt = $chat->getCreatedAt();

        foreach ($chat->getMessages() as $message) {
            $this->messages[] = $message->getDTO();
        }

        foreach ($chat->getParticipants() as $participant) {
            $this->participants[] = $participant->getDTO();
        }
    }
}