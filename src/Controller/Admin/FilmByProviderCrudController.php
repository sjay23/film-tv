<?php

namespace App\Controller\Admin;

use App\Entity\FilmByProvider;
use Doctrine\DBAL\Types\FloatType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class FilmByProviderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FilmByProvider::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id')->hideOnForm(),
            IntegerField::new('movieId')->hideOnForm(),
            IntegerField::new('duration'),
            IntegerField::new('year'),
            IntegerField::new('posterUploaded'),
            TextField::new('rating'),
            TextField::new('age'),
            TextField::new('link'),
            CollectionField::new('poster')->renderExpanded(),
            #CollectionField::new('poster')->setBasePath('/public/images')->setUploadDir('/public/images'),
            AssociationField::new('audio')->setFormTypeOption('choice_label', 'name')->setFormTypeOption('by_reference', false),
            CollectionField::new('actor')->renderExpanded(),
            CollectionField::new('country')->renderExpanded(),
            AssociationField::new('genre')->setFormTypeOption('choice_label', 'name')->setFormTypeOption('by_reference', false),
            CollectionField::new('director')->renderExpanded(),


        ];
    }

}
