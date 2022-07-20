<?php

namespace App\Controller\Admin;

use App\Entity\FilmByProvider;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FilmByProviderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FilmByProvider::class;
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
