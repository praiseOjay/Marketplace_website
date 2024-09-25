<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\Type\RegistrationFormType;
use App\Form\Type\SearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // create a new User entity
        $user = new User();
        // create the form
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        // process the form
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // set the default image
            $user->setImageFileName('default_image.png');

            // save the User
            $entityManager->persist($user);
            $entityManager->flush();

            // redirect to the login page
            return $this->redirectToRoute('app_login');
        }

        // create the search form
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request); // Handle the submitted form
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // Get the data from the form
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory('id');

            // Redirect to the live search page
            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category->getId()]);
        }

        // render the registration form
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'search_form' => $searchForm->createView()
        ]);
    }
}
