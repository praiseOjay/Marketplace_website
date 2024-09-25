<?php

namespace App\Controller\Admin;

use App\Entity\Advert;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdvertCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        //Return your entity's FQCN
        return Advert::class;
    }

    //Configure your CRUD
    public function configureCrud(Crud $crud): Crud
    {
        //Set the advert date time format
        return $crud
            ->setDateTimeFormat('dd-MM-yyyy HH:mm:ss');
    }


    public function configureFields(string $pageName): iterable
    {
        //Configure each field in your CRUD
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('title');
        yield TextEditorField::new('description');
        yield IntegerField::new('price');
        yield ImageField::new('imageFileName')
            ->setBasePath('images/')
            ->setUploadDir('public/images/');
        yield TextField::new('location');
        yield DateField::new('timestamp')->setLabel('Edited At');
        yield BooleanField::new('isPublished');
    }
}
