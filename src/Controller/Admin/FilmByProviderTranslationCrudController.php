<?php

namespace App\Controller\Admin;

use App\Entity\FilmByProviderTranslation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FilmByProviderTranslationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FilmByProviderTranslation::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
