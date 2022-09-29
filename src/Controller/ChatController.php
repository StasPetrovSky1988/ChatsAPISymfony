<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

class ChatController extends AbstractController
{

    public function __construct(
        private ChatRepository $chatRepository,
        private SerializerInterface $serializer,
        private UserRepository $userRepository,
        private ManagerRegistry $registry,
    ) { }

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

        $this->registry->getManager()->flush();

        return $this->json($this->serializer->serialize($chat->getDTO(), 'json'));
    }

    /**
     * join to chat
     * @param User|null $user
     * @param int $idChat
     * @return JsonResponse
     */
    #[Route('/join-chat/{idChat}')]
    public function joinChat(#[CurrentUser] ?User $user, int $idChat): JsonResponse
    {
        $chat = $this->chatRepository->find($idChat);
        if (!$chat) throw new NotFoundHttpException("Chat not found");

        $chat->addParticipant($user);
        $this->registry->getManager()->flush();

        return $this->json($this->serializer->serialize(true, 'json'));
    }

    /**
     * Join other participant to the chat
     * @param int $idChat
     * @param int $idUser
     * @return JsonResponse
     */
    #[Route('/join-participant/{idChat}/{idUser}')]
    public function joinParticipant(int $idChat, int $idUser): JsonResponse
    {
        $chat = $this->chatRepository->find($idChat);
        if (!$chat) throw new NotFoundHttpException("Chat not found");

        $participant = $this->userRepository->find($idUser);
        if (!$participant) throw new NotFoundHttpException("User not found");

        $chat->addParticipant($participant);
        $this->registry->getManager()->flush();

        return $this->json($this->serializer->serialize(true, 'json'));
    }

}
