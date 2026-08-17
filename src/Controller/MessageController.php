<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Message;
use App\Entity\User;
use App\Form\Type\MessageFormType;
use App\Form\Type\SearchFormType;
use App\Repository\MessageRepository;
use App\Service\HoneypotValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MessageController extends AbstractController
{
    #[Route('/messages', name: 'user_inbox')]
    #[IsGranted('ROLE_USER', message: 'You must be logged in to view your inbox.')]
    public function inbox(MessageRepository $messageRepository, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $conversations = $messageRepository->findConversationsForUser($currentUser);

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory();

            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category?->getId()]);
        }

        return $this->render('message/inbox.html.twig', [
            'conversations' => $conversations,
            'search_form' => $searchForm->createView(),
        ]);
    }

    #[Route('/messages/send/{advertId}', name: 'send_message')]
    #[IsGranted('ROLE_USER', message: 'You must be logged in to send a message.')]
    public function sendMessage(
        int $advertId,
        Request $request,
        EntityManagerInterface $entityManager,
        HoneypotValidator $honeypotValidator
    ): Response {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $advert = $entityManager->getRepository(Advert::class)->find($advertId);

        if (!$advert) {
            throw $this->createNotFoundException('Advert not found');
        }

        $recipient = $advert->getUser();
        if ($recipient === $currentUser) {
            $this->addFlash('warning', 'You cannot message yourself about your own advert.');
            return $this->redirectToRoute('show_advert', ['slug' => $advert->getSlug()]);
        }

        $message = new Message();
        $message->setSender($currentUser);
        $message->setRecipient($recipient);
        $message->setAdvert($advert);

        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($honeypotValidator->isSpam($request)) {
                return $this->redirectToRoute('message_thread', ['otherUserId' => $recipient->getId()]);
            }

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Your message has been sent to ' . $recipient->getUsername() . '!');
            return $this->redirectToRoute('message_thread', ['otherUserId' => $recipient->getId()]);
        }

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        return $this->render('message/send.html.twig', [
            'advert' => $advert,
            'recipient' => $recipient,
            'form' => $form->createView(),
            'search_form' => $searchForm->createView(),
        ]);
    }

    #[Route('/messages/thread/{otherUserId}', name: 'message_thread')]
    #[IsGranted('ROLE_USER', message: 'You must be logged in to view message threads.')]
    public function messageThread(
        int $otherUserId,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        HoneypotValidator $honeypotValidator
    ): Response {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $otherUser = $entityManager->getRepository(User::class)->find($otherUserId);

        if (!$otherUser) {
            throw $this->createNotFoundException('User not found.');
        }

        $messages = $messageRepository->findThread($currentUser, $otherUser);
        foreach ($messages as $msg) {
            if ($msg->getRecipient() === $currentUser && !$msg->getIsRead()) {
                $msg->setIsRead(true);
            }
        }
        $entityManager->flush();

        $reply = new Message();
        $reply->setSender($currentUser);
        $reply->setRecipient($otherUser);

        $form = $this->createForm(MessageFormType::class, $reply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($honeypotValidator->isSpam($request)) {
                return $this->redirectToRoute('message_thread', ['otherUserId' => $otherUserId]);
            }

            $entityManager->persist($reply);
            $entityManager->flush();

            return $this->redirectToRoute('message_thread', ['otherUserId' => $otherUserId]);
        }

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        return $this->render('message/thread.html.twig', [
            'otherUser' => $otherUser,
            'messages' => $messages,
            'form' => $form->createView(),
            'search_form' => $searchForm->createView(),
        ]);
    }
}
