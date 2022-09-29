<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\User;
use App\Repository\ChatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

class ChatController extends AbstractController
{

    /**
     * ChatController constructor.
     * @param ChatRepository $chatRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(private ChatRepository $chatRepository, private SerializerInterface $serializer)
    {

    }

    /**
     * Get all chat by current user
     * @param User|null $user
     * @return JsonResponse
     */
    #[Route('/get-chats', name: 'get_chats')]
    public function getChats(#[CurrentUser] ?User $user): JsonResponse
    {
        $chatsDto = [];

        foreach ($user->getChats() as $chat) {
            $chatsDto[] = $chat->getDTO();
        }

        return $this->json($this->serializer->serialize($chatsDto, 'json'));
    }

    /**
     * Get all chat's messages by id
     * @param User|null $user
     * @param int $idChat
     * @return JsonResponse
     */
    #[Route('/get-chat/{idChat}', name: 'get_messages')]
    public function getChat(#[CurrentUser] ?User $user, int $idChat): JsonResponse
    {
        $currentChat = $this->chatRepository->find($idChat);

        if (!$currentChat) throw new NotFoundHttpException("Chat not found");
        if (!$currentChat->amIConnected($user)) throw new AccessDeniedException("You don't have access to this chat");

        $chatDto = $currentChat->getDTO();

        return $this->json($this->serializer->serialize($chatDto, 'json'));
    }

    /**
     * Create new chat
     * @param User|null $user
     * @return JsonResponse
     */
    #[Route('/create-chat', name: 'create_chat')]
    public function createChat(#[CurrentUser] ?User $user): JsonResponse
    {
        $chat = Chat::createNewFromUserIntent($user);

        $this->chatRepository->add($chat);
        $this->chatRepository->flush();

        return $this->json($this->serializer->serialize($chat->getDTO(), 'json'));
    }

}
