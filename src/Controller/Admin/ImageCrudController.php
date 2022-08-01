<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
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
            TextField::new('link'),
            TextField::new('filePath'),
            DateField::new('uploadedAt'),
            IntegerField::new('uploaded'),
            Field::new('imageFile')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            TextareaField::new('link', 'Photo')->setTemplatePath('admin/test_template.html.twig')->onlyOnIndex()
        ];
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Image $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance->getImageFile() !== null) {
            $entityInstance->setFilePath($entityInstance->getImageFile()->getFilename());
            $entityManager->persist($entityInstance);
        }

        $entityManager->flush();
    }
}
