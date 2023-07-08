<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/message', name: 'message_', methods: ['OPTIONS', 'POST', 'GET'])]
class MessageController extends AbstractController
{
    private MessageRepository $messageRepository;
    private SerializerInterface $serializer;

    public function __construct(
        MessageRepository $messageRepository,
    )
    {
        $this->messageRepository = $messageRepository;
    }


    #[Route('/save', name: 'save', methods: ['POST'])]
    public function saveMessage(Request $request): JsonResponse
    {
        $message = $this->serializer->deserialize($request->getContent(), Message::class, 'json');

        $message->setCreateDate(new \DateTime());

        $this->messageRepository->save($message, true);

        return new JsonResponse(['uuid' => $message->getId()], 201);
    }


}
