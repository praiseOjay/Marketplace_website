<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LiveSearchController extends AbstractController
{
    #[Route('/search', name: 'live_search')]
    public function searchAdvert(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $title = trim((string) $request->query->get('title', ''));
        $category = (int) $request->query->get('category', 0);

        $categories = $entityManager->getRepository(Categories::class)->findAll();

        if (empty($title) && $category === 0) {
            return $this->redirectToRoute('home');
        }

        if (!empty($title) && $category === 0) {
            $query = $entityManager->getRepository(Advert::class)->titleSearchQuery($title);
        } else {
            $query = $entityManager->getRepository(Advert::class)->filteredSearchQuery($title, $category);
        }

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('advert/filter_search.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
            'search_title' => $title,
            'search_category' => $category,
        ]);
    }
}