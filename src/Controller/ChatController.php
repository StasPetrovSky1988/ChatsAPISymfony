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

class ChatController extends AbstractController
{
    /**
     * Get all chat by current user
     *
     * @param User|null $user
     * @return JsonResponse
     */
    #[Route('/get-chats', name: 'get_chats')]
    public function getChats(#[CurrentUser] ?User $user): JsonResponse
    {
        $chats = [];

        foreach ($user->getChats() as $chat) {
            $chats[] = $chat->getId();
        }

        return $this->json(json_encode($chats));
    }

    /**
     * Get all chat's messages by id
     *
     * @param User|null $user
     * @param int $idChat
     * @param ChatRepository $chatRepository
     * @return JsonResponse
     */
    #[Route('/get-messages/{idChat}', name: 'get_messages')]
    public function getMessages(#[CurrentUser] ?User $user, int $idChat, ChatRepository $chatRepository): JsonResponse
    {
        $currentChat = $chatRepository->find($idChat);
        $messages = [];

        if (!$currentChat) throw new NotFoundHttpException("Chat not found");
        if (!$currentChat->amIConnected($user)) throw new AccessDeniedException("You don't have access to this chat");

        foreach ($currentChat->getMessages() as $message) {
            $messages[$message->getId()]['createdAt'] = $message->getCreatedAt();
            $messages[$message->getId()]['content'] = $message->getContent();
        }

        return $this->json([json_encode($messages)]);
    }

    /**
     * Create new chat
     *
     * @param User|null $user
     * @param ChatRepository $chatRepository
     * @return JsonResponse
     */
    #[Route('/create-chat', name: 'create_chat')]
    public function createChat(#[CurrentUser] ?User $user, ChatRepository $chatRepository): JsonResponse
    {
        $chat = new Chat();
        $chat->addUser($user);

        $chatRepository->add($chat, true);

        return $this->json(['id' => $chat->getId()]);
    }

}
