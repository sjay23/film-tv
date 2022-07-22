<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use App\Field\VichImageField;

class ImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Image::class;
    }


    public function configureFields(string $pageName): iterable
    {

        return [
            yield VichImageField::new('imageFile')->onlyOnForms(),
            TextField::new('link'),
            TextField::new('filePath'),
            DateField::new('uploadedAt'),
            AssociationField::new('filmBanner'),
            AssociationField::new('FilmPoster'),
            IntegerField::new('uploaded'),
            CollectionField::new('link', 'Image')
                ->setTemplatePath('admin/test_template.html.twig')->hideOnForm(),
            ];
    }

}
