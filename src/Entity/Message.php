<?php

namespace App\Entity;

use App\Dto\ChatDto;
use App\Dto\ChatMessageDto;
use App\Repository\MessageRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chat $chat = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $_user = null;

    private function __construct()
    {
        if (!$this->createdAt) {
            $this->createdAt = Carbon::now()->toDateTimeImmutable();
        }
    }

    public static function newMessage(User $user, Chat $chat, string $text): self
    {
        $message = new static();
        $message->_user = $user;
        $message->chat = $chat;
        $message->content = $text;

        return $message;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChat(): ?Chat
    {
        return $this->chat;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getUser(): ?User
    {
        return $this->_user;
    }

    public function getDTO(): ChatMessageDto
    {
        return new ChatMessageDto($this);
    }
}
