<?php

namespace App\Controller;

use App\Form\Type\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        // Create search form
        $search_form = $this->createForm(SearchFormType::class);

        // Render login page with last username and error message
        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'search_form' => $search_form->createView(),
          ]);
      }
}
