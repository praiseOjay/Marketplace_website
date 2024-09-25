<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\Type\AdvertFormType;
use App\Form\Type\SearchFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdvertController extends AbstractController
{

    #[Route('/advert/new', name: 'new_advert')]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to access this page!')]
    public function newAdvert(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader) : Response
    {
        //creates a new Advert object and a form using the AdvertFormType form type.
        $advert = new Advert();
        $form = $this->createForm(AdvertFormType::class, $advert);
        $form->handleRequest($request);

        // checks if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // gets the data from the form
            $adverts = $form->getData();
            // gets the user
            $user =  $this->getUser();

            // sets the data from the form to the Advert object
            $advert->setUser($user);
            $advert->setTitle($adverts->getTitle());
            $advert->setDescription($adverts->getDescription());
            $advert->setCategory($adverts->getCategory());
            $advert->setPrice($adverts->getPrice());
            $advert->setLocation($adverts->getLocation());
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $advert->setImageFileName($imageFileName);
            }
            $advert->setTimeStamp(new \DateTime());
            $advert->setIsPublished(false);

            // saves the data to the database
            $entityManager->persist($advert);
            $entityManager->flush();

            // redirects to the user advert page
            return $this->redirectToRoute('user_advert');
        }

        // creates the search form
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request); // Handle the submitted form
        // checks if the search form is submitted and valid
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // gets the data from the form
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory('id');

            // redirects to the live search page
            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category]);
        }

        // renders the new_advert.html.twig template with the form and search form
        return $this->render('advert/new_advert.html.twig', [
            'form' => $form->createView(),
            'search_form' => $searchForm->createView()
        ]);
    }

    #[Route('/advert/show', name: 'show_advert')]
    public function showAdvert(EntityManagerInterface $entityManager, Request $request) : Response
    {
        // Retrieve the advert ID from the request
        $advertID = $request->query->get('id');
        // Retrieve the advert from the database
        $advert = $entityManager->getRepository(Advert::class)->find($advertID);
        // Check if the advert exists
        if (!$advert) {
            throw $this->createNotFoundException(
                'No Advert was found'
            );
        }

        // Create the search form
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request); // Handle the submitted form
        // Check if the search form is submitted and valid
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // Get the data from the form
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory('id');

            // Redirect to the live search page
            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category]);
        }

        // Render the show_advert.html.twig template with the advert and search form
        return $this->render('advert/show_advert.html.twig', [
            'advert' => $advert,
            'search_form' => $searchForm->createView(),
        ]);
    }

    #[Route('/advert/index', name: 'home')]
    public function allAdverts(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator) : Response
    {
        // Get all the adverts and paginate them
        $pagination = $paginator->paginate(
            // Doctrine Query
            $entityManager->getRepository(Advert::class)->allAdvertsQuery(),
            // Define the page parameter
            $request->query->getInt('page', 1),
            5
        );

        // Create the search form
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request); // Handle the submitted form
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // Get the data from the form
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory('id');

            // Redirect to the live search page
            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category]);
        }

        // Render the index.html.twig template with the pagination and search form
        return $this->render('advert/index.html.twig', [
            'search_form' => $searchForm->createView(),
            'pagination' => $pagination,
        ]);
    }


    #[Route('/advert/my_adverts', name: 'user_advert')]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to access this page!')]
    public function userAdvert(Request $request, PaginatorInterface $paginator) : Response
    {
        // Get the user and the user's adverts
        $user = $this->getUser();
        $adverts = $user->getAdverts();

        // Paginate the adverts
        $pagination = $paginator->paginate(
            $adverts,
            $request->query->getInt('page', 1),
            5
        );

        // Create the search form
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

        // Render the user_advert.html.twig template with the pagination and search form
        return $this->render('advert/user_advert.html.twig', [
            'pagination' => $pagination,
            'search_form' => $searchForm->createView(),
        ]);
    }


    #[Route('/advert/edit', name: 'edit_advert')]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to access this page!')]
    public function editAdvert(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        // Retrieve the advert ID from the request
        $user = $this->getUser();
        $adverts = $user->getAdverts();
        $advertId = $adverts[0]->getId();


        // Retrieve the advert from the database
        $advert = $entityManager->getRepository(Advert::class)->find($advertId);
        // Create the form and populate it with the current values of the advert
        $form = $this->createForm(AdvertFormType::class, $advert);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the data from the form
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $advert->setImageFileName($imageFileName);
            }
            $advert->setTimeStamp(new \DateTime());
            $advert->setIsPublished(false);
            // Update the advert with the new values from the form
            $entityManager->flush();

            // Redirect to the show advert page or any other desired page
            return $this->redirectToRoute('user_advert');
        }

        // Create the search form
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request); // Handle the submitted form
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // Get the data from the form
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory('id');

            // Redirect to the live search page
            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category]);
        }

        // Render the edit_advert.html.twig template with the form and search form
        return $this->render('advert/edit_advert.html.twig', [
            'form' => $form->createView(),
            'search_form' => $searchForm->createView()
        ]);
    }


    #[Route('/advert/delete', name: 'delete_advert')]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to access this page!')]
    public function deleteAdvert(EntityManagerInterface $entityManager): Response
    {
        // Retrieve the advert ID from the request
        //$advertId = $request->query->get('id');
        $user = $this->getUser();
        $adverts = $user->getAdverts();
        $advertId = $adverts[0]->getId();

        // Retrieve the advert from the database
        $advert = $entityManager->getRepository(Advert::class)->find($advertId);

        if ($advert) {
            // Remove the advert from the database
            $entityManager->remove($advert);
            $entityManager->flush();
        }

        // Redirect to the index page or any other desired page
        return $this->redirectToRoute('user_advert');
    }
}