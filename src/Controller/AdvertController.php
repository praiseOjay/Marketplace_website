<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Categories;
use App\Entity\User;
use App\Form\Type\AdvertFormType;
use App\Form\Type\SearchFormType;
use App\Security\Voter\AdvertVoter;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdvertController extends AbstractController
{
    #[Route('/advert/new', name: 'new_advert')]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to access this page!')]
    public function newAdvert(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $advert = new Advert();
        $form = $this->createForm(AdvertFormType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $advert->setUser($user);
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $advert->setImageFileName($imageFileName);
            }
            $advert->setTimeStamp(new \DateTime());
            $advert->generateSlug();

            $entityManager->persist($advert);
            $entityManager->flush();

            return $this->redirectToRoute('user_advert');
        }

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory();

            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category?->getId()]);
        }

        return $this->render('advert/new_advert.html.twig', [
            'form' => $form->createView(),
            'search_form' => $searchForm->createView()
        ]);
    }

    #[Route('/advert/show/{slug?}', name: 'show_advert')]
    public function showAdvert(?string $slug, EntityManagerInterface $entityManager, Request $request): Response
    {
        $advertID = $request->query->get('id');
        $repo = $entityManager->getRepository(Advert::class);

        $advert = null;
        if ($slug) {
            $advert = $repo->findOneBy(['slug' => $slug]);
        }
        if (!$advert && $advertID) {
            $advert = $repo->find($advertID);
        }

        if (!$advert) {
            throw $this->createNotFoundException('No Advert was found');
        }

        $this->denyAccessUnlessGranted(AdvertVoter::SHOW, $advert);

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory();

            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category?->getId()]);
        }

        return $this->render('advert/show_advert.html.twig', [
            'advert' => $advert,
            'search_form' => $searchForm->createView(),
        ]);
    }

    #[Route('/', name: 'root_home')]
    #[Route('/advert/index', name: 'home')]
    public function allAdverts(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $entityManager->getRepository(Advert::class)->allAdvertsQuery(),
            $request->query->getInt('page', 1),
            6
        );

        $categories = $entityManager->getRepository(Categories::class)->findAll();

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory();

            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category?->getId()]);
        }

        return $this->render('advert/index.html.twig', [
            'search_form' => $searchForm->createView(),
            'pagination' => $pagination,
            'categories' => $categories,
        ]);
    }

    #[Route('/advert/my_adverts', name: 'user_advert')]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to access this page!')]
    public function userAdvert(Request $request, PaginatorInterface $paginator, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $adverts = $user->getAdverts();

        $categories = $entityManager->getRepository(Categories::class)->findAll();

        $pagination = $paginator->paginate(
            $adverts,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('advert/user_advert.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
        ]);
    }

    #[Route('/advert/saved', name: 'user_favorites')]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to view your saved adverts!')]
    public function userFavorites(Request $request, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $favorites = $user->getFavoriteAdverts();

        $pagination = $paginator->paginate(
            $favorites,
            $request->query->getInt('page', 1),
            6
        );

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory();

            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category?->getId()]);
        }

        return $this->render('advert/user_favorites.html.twig', [
            'pagination' => $pagination,
            'search_form' => $searchForm->createView(),
        ]);
    }

    #[Route('/advert/edit/{id?}', name: 'edit_advert')]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to access this page!')]
    public function editAdvert(?int $id, Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $advertId = $id ?? $request->query->get('id');

        if (!$advertId) {
            $userAdverts = $user->getAdverts();
            if ($userAdverts->count() > 0) {
                $advertId = $userAdverts->first()->getId();
            }
        }

        $advert = $entityManager->getRepository(Advert::class)->find($advertId);
        if (!$advert) {
            throw $this->createNotFoundException('Advert not found.');
        }

        $this->denyAccessUnlessGranted(AdvertVoter::EDIT, $advert);

        $form = $this->createForm(AdvertFormType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $advert->setImageFileName($imageFileName);
            }
            $advert->setTimeStamp(new \DateTime());
            $advert->generateSlug();
            $entityManager->flush();

            return $this->redirectToRoute('user_advert');
        }

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $ad = $searchForm->getData();
            $title = $ad->getTitle();
            $category = $ad->getCategory();

            return $this->redirectToRoute('live_search', ['title' => $title, 'category' => $category?->getId()]);
        }

        return $this->render('advert/edit_advert.html.twig', [
            'form' => $form->createView(),
            'search_form' => $searchForm->createView(),
            'advert' => $advert,
        ]);
    }

    #[Route('/advert/delete/{id?}', name: 'delete_advert', methods: ['POST', 'GET'])]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to access this page!')]
    public function deleteAdvert(?int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $advertId = $id ?? $request->query->get('id');
        /** @var User $user */
        $user = $this->getUser();

        if (!$advertId) {
            $userAdverts = $user->getAdverts();
            if ($userAdverts->count() > 0) {
                $advertId = $userAdverts->first()->getId();
            }
        }

        $advert = $entityManager->getRepository(Advert::class)->find($advertId);
        if ($advert) {
            $this->denyAccessUnlessGranted(AdvertVoter::DELETE, $advert);
            $entityManager->remove($advert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_advert');
    }

    #[Route('/advert/{id}/favorite', name: 'favorite_advert', methods: ['POST', 'GET'])]
    #[IsGranted('ROLE_USER', message: 'You need to be logged in to favorite an advert!')]
    public function toggleFavorite(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $advert = $entityManager->getRepository(Advert::class)->find($id);

        if (!$advert) {
            throw $this->createNotFoundException('Advert not found');
        }

        if ($user->getFavoriteAdverts()->contains($advert)) {
            $user->removeFavoriteAdvert($advert);
            $isFavorite = false;
        } else {
            $user->addFavoriteAdvert($advert);
            $isFavorite = true;
        }

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'isFavorite' => $isFavorite,
                'count' => $advert->getFavoritedBy()->count()
            ]);
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?? $this->generateUrl('home'));
    }
}