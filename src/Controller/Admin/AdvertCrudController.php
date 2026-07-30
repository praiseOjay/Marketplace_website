<?php

namespace App\Controller\Admin;

use App\Entity\Advert;
use App\Enum\AdvertStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdvertCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Advert::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDateTimeFormat('dd-MM-yyyy HH:mm:ss');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('title');
        yield SlugField::new('slug')->setTargetFieldName('title')->hideOnIndex();
        yield TextEditorField::new('description');
        yield IntegerField::new('price');
        yield ImageField::new('imageFileName')
            ->setBasePath('images/')
            ->setUploadDir('public/images/');
        yield TextField::new('location');
        yield DateField::new('timestamp')->setLabel('Edited At');
        yield ChoiceField::new('status')
            ->setChoices([
                'Published' => AdvertStatus::PUBLISHED,
                'Draft' => AdvertStatus::DRAFT,
                'Pending Review' => AdvertStatus::PENDING_REVIEW,
                'Rejected' => AdvertStatus::REJECTED,
                'Sold' => AdvertStatus::SOLD,
            ]);
        yield BooleanField::new('isPublished');
    }
}
