<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\SearchFormType;
use App\Form\Type\UserFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user/edit', name: 'edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager,FileUploader $fileUploader): Response
    {
        // Get the logged-in userId from the session
        $userID = $this->getUser();

        // Get the logged-in user from the database
        $user = $entityManager->getRepository(User::class)->find($userID->getID());
        // Create the form
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        // Check if the form has been submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the data from the form
            $userInfo = $form->getData();
            // Set the data on the user
            $user->setUsername($userInfo->getUsername());
            $user->setEmail($userInfo->getEmail());
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $user->setImageFileName($imageFileName);
            }
            // Save the user's data to the database
            $entityManager->persist($user);
            $entityManager->flush();
            // Redirect to the user adverts page
            return $this->redirectToRoute('user_advert');
        }

        // Create the search form
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request); // Handle the submitted form
        // Check if the search form has been submitted and is valid
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // Get the data from the form
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory('id');

            // Redirect to the live search page
            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category]);
        }

        // Render the edit profile page
        return $this->render('user/edit_profile.html.twig', [
            'form' => $form->createView(),
            'search_form' => $searchForm->createView()
        ]);
    }

    #[Route('/user/show', name: 'show_profile')]
    public function showProfile(EntityManagerInterface $entityManager): Response
    {
        // Get the logged-in userId from the session
        $userID = $this->getUser();

        // Get the logged-in user from the database
        $user = $entityManager->getRepository(User::class)->find($userID->getID());
        // Create the search form
        $searchForm = $this->createForm(SearchFormType::class);

        // Render the show profile page with the user's data and search form
        return $this->render('user/show_profile.html.twig', [
            'user' => $user,
            'search_form' => $searchForm->createView()
        ]);
    }
}