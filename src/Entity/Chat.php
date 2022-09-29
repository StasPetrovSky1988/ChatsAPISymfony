<?php

namespace App\Entity;

use App\Dto\ChatDto;
use App\Repository\ChatRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PropertyAccess\Exception\AccessException;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'chats')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'chat', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    private function __construct()
    {
        $this->users = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->createdAt = Carbon::now()->toDateTimeImmutable();
    }

    public static function createNewFromUserIntent(User $user): self
    {
        $chat = new static();
        $chat->addParticipant($user);

        return $chat;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->users;
    }

    public function addParticipant(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeParticipant(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * Check am i connected in current chat
     */
    public function amIConnected(User $user): bool
    {
        return $this->users->contains($user);
    }

    /**
     * @return ChatDto
     */
    public function getDTO(): ChatDto
    {
        return new ChatDto($this);
    }

    /**
     * @param User $user
     * @param string $content
     * @return Message
     */
    public function addNewMessage(User $user, string $content): Message
    {
        if (!$this->amIConnected($user)) throw new AccessException("You are not joined to this chat");

        $message = Message::newMessage($user, $this, $content);
        $this->messages->add($message);

        return $message;
    }
}
