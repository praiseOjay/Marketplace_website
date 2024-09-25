<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\SearchMode;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        //Return your entity's FQCN
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        //Configure each field in your CRUD
        $roles = ['ROLE_USER', 'ROLE_MODERATOR', 'ROLE_ADMIN','ROLE_SUPER_ADMIN'];
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('username');
        yield TextField::new('password');
        yield TextField::new('email');;
        yield ImageField::new('imageFileName')
            ->setBasePath('images/')
            ->setUploadDir('public/images');
        yield ChoiceField::new('roles')
            ->setChoices(array_combine($roles, $roles))
            ->allowMultipleChoices()
            ->renderExpanded()
            ->renderAsBadges()
            ->setHelp('Available roles: ROLE_USER, ROLE_ADMIN, ROLE_SUPER_ADMIN, ROLE_MODERATOR');
    }

    public function configureActions(Actions $actions): Actions
    {
        //Configure your actions here
        return $actions
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE);
    }

    public function configureCrud(Crud $crud): Crud
    {
        //Configure your CRUD
        return $crud
            ->setEntityLabelInPlural('Users')
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'User list')
            ->setSearchFields(['username'])
            ->setAutofocusSearch()
            ->setSearchMode(SearchMode::ANY_TERMS)
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(10)
            ->setPaginatorRangeSize(5)
            ->hideNullValues();
    }
}
