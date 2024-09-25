<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\Type\SearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class LiveSearchController extends AbstractController
{
      #[Route('/search', name: 'live_search')]
      public function searchAdvert(EntityManagerInterface $entityManager, Request $request,PaginatorInterface $paginator): Response
      {
          // get search parameters
          $title = $request->get('title');
          $category = $request->get('category');

          // check if search parameters are empty
          if (!$title && $category == 0)
          {
              // redirect to homepage
              return $this->redirectToRoute('home');
          } else if($title && $category == 0) {//check if title search parameter is not empty
              // get search results with pagination
              $pagination = $paginator->paginate(
                  $entityManager->getRepository(Advert::class)->titleSearchQuery($title),
                  $request->query->getInt('page', 1),
                  4
              );
              // get search form
              $searchForm = $this->createForm(SearchFormType::class);

              // render search results
              return $this->render('advert/filter_search.html.twig', [
                  'pagination' => $pagination,
                  'search_form' => $searchForm->createView(),
              ]);
          } else{//check if category search parameter is not empty
              // get search results with pagination
              $pagination = $paginator->paginate(
                  $entityManager->getRepository(Advert::class)->filteredSearchQuery($title, $category),
                  $request->query->getInt('page', 1),
                  4
              );

              // get search form
              $searchForm = $this->createForm(SearchFormType::class);

              // render search results
              return $this->render('advert/filter_search.html.twig', [
                  'pagination' => $pagination,
                  'search_form' => $searchForm->createView()
              ]);
          }

      }
}