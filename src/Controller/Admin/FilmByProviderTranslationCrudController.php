<?php

namespace App\Controller\Admin;

use App\Entity\FilmByProviderTranslation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FilmByProviderTranslationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FilmByProviderTranslation::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id')->hideOnForm(),
            TextField::new('title'),
            CollectionField::new('banner')->renderExpanded(),
            TextareaField::new('description'),
            IntegerField::new('bannerUploaded'),
            TextField::new('locale'),


        ];
    }

}
