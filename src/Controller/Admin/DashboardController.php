<?php

namespace App\Controller\Admin;

use App\Entity\Advert;
use App\Entity\Categories;
use App\Entity\Message;
use App\Entity\User;
use App\Enum\AdvertStatus;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController implements DashboardControllerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[IsGranted('ROLE_MODERATOR')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $userCount = $this->entityManager->getRepository(User::class)->count([]);
        $categoriesCount = $this->entityManager->getRepository(Categories::class)->count([]);
        $messageCount = $this->entityManager->getRepository(Message::class)->count([]);

        $advertRepo = $this->entityManager->getRepository(Advert::class);
        $totalAdverts = $advertRepo->count([]);
        $publishedCount = $advertRepo->count(['status' => AdvertStatus::PUBLISHED]);
        $pendingCount = $advertRepo->count(['status' => AdvertStatus::PENDING_REVIEW]);
        $soldCount = $advertRepo->count(['status' => AdvertStatus::SOLD]);

        $soldAdverts = $advertRepo->findBy(['status' => AdvertStatus::SOLD]);
        $totalVolume = 0.0;
        foreach ($soldAdverts as $ad) {
            $totalVolume += (float) $ad->getPrice();
        }

        $recentAdverts = $advertRepo->findBy([], ['id' => 'DESC'], 5);

        return $this->render('admin/admin_dashboard.html.twig', [
            'userCount' => $userCount,
            'categoriesCount' => $categoriesCount,
            'messageCount' => $messageCount,
            'totalAdverts' => $totalAdverts,
            'publishedCount' => $publishedCount,
            'pendingCount' => $pendingCount,
            'soldCount' => $soldCount,
            'totalVolume' => $totalVolume,
            'recentAdverts' => $recentAdverts,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Marketplace Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        $pendingCount = $this->entityManager->getRepository(Advert::class)->count(['status' => AdvertStatus::PENDING_REVIEW]);

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::linkToUrl('Homepage', 'fas fa-home', $this->generateUrl('home'));
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Categories::class)
            ->setPermission('ROLE_ADMIN');

        $advertMenu = MenuItem::linkToCrud('Adverts', 'fas fa-shop', Advert::class)
            ->setPermission('ROLE_MODERATOR');
        if ($pendingCount > 0) {
            $advertMenu->setBadge($pendingCount, 'warning');
        }
        yield $advertMenu;

        yield MenuItem::linkToCrud('Messages', 'fas fa-comments', Message::class)
            ->setPermission('ROLE_MODERATOR');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        /** @var User $user */
        return parent::configureUserMenu($user)
            ->setAvatarUrl($user->getImageFileName() ? 'images/'.$user->getImageFileName() : '')
            ->setMenuItems([
                MenuItem::linkToUrl('My Profile', 'fa fa-user', $this->generateUrl('show_profile')),
            ]);
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
