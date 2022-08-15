<?php

namespace App\Controller\Admin;

use App\Entity\People;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PeopleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return People::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('link'),
            TextField::new('name'),
            DateField::new('uploadedAt'),
            AssociationField::new('filmDirector')->setFormTypeOption('choice_label', 'movieId')->setFormTypeOption('by_reference', false),
            AssociationField::new('filmActor')->setFormTypeOption('choice_label', 'movieId')->setFormTypeOption('by_reference', false),

        ];
    }

}
