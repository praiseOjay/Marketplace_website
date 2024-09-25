<?php

namespace App\Controller\Admin;

use App\Entity\Advert;
use App\Entity\Categories;
use App\Entity\Category;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController implements DashboardControllerInterface
{
    #[isGranted('ROLE_MODERATOR')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
//        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
//         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
//        try {
//            return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
//        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
//        }

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
//         if ('jane' === $this->getUser()->getUsername()) {
//             return $this->redirect('...');
//         }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
         return $this->render('admin/admin_dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        //set your dashboard name
        return Dashboard::new()
            ->setTitle('Marketplace Dashboard');

    }

    public function configureMenuItems(): iterable
    {
        //set your menu items
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::linkToUrl('Homepage', 'fas fa-home', $this->generateUrl('home'));
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Categories::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Adverts', 'fas fa-shop', Advert::class)
            ->setPermission('ROLE_MODERATOR');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        //set your user menu items
        return parent::configureUserMenu($user)
            ->setAvatarUrl('images/'.$user->getImageFileName())
            ->setMenuItems([
                MenuItem::linkToUrl('My Profile', 'fa fa-user', $this->generateUrl('show_profile')),
            ]);
    }

    public function configureActions(): Actions
    {
        //set your actions
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
