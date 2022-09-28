<?php

namespace App\Entity;

use App\Dto\ChatDto;
use App\Repository\ChatRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
        if (!$this->createdAt) {
            $this->createdAt = Carbon::now()->toDateTimeImmutable();
        }
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

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setChat($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getChat() === $this) {
                $message->setChat(null);
            }
        }

        return $this;
    }

    /**
     * Check am i connected in current chat
     */
    public function amIConnected($user): bool
    {
        return $this->users->contains($user);
    }

    public function getDTO(): ChatDto
    {
        return new ChatDto($this);
    }

    public function addNewMessage(User $user, string $content): Message
    {
        $message = Message::newMessage($user, $this, $content);
        $this->addMessage($message);

        return $message;
    }
}
