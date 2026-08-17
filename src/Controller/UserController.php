<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\SearchFormType;
use App\Form\Type\UserFormType;
use App\Service\FileUploader;
use App\Service\HoneypotValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/user/edit', name: 'edit_profile')]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to edit your profile.')]
    public function editProfile(
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
        UserPasswordHasherInterface $passwordHasher,
        HoneypotValidator $honeypotValidator
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($honeypotValidator->isSpam($request)) {
                return $this->redirectToRoute('show_profile');
            }
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, true);
                $user->setImageFileName($imageFileName);
            }

            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully.');

            return $this->redirectToRoute('show_profile');
        }

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory();

            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category?->getId()]);
        }

        return $this->render('user/edit_profile.html.twig', [
            'form' => $form->createView(),
            'search_form' => $searchForm->createView()
        ]);
    }

    #[Route('/user/show', name: 'show_profile')]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to view your profile.')]
    public function showProfile(EntityManagerInterface $entityManager, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory();

            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category?->getId()]);
        }

        return $this->render('user/show_profile.html.twig', [
            'user' => $user,
            'search_form' => $searchForm->createView()
        ]);
    }
}