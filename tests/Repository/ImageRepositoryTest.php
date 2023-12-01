<?php

namespace App\Tests\Repository;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ImageRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->containerKernel = static::getContainer();
        $this->iamgeRepository = $this->containerKernel->get(ImageRepository::class);
        $this->imageFile = new UploadedFile(
            './tests/image/test_image.png',
            'my_image.png',
            'image/png',
        );
    }

    public function testSave()
    {
        $image =  new Image();
        $image->setImageFile( $this->imageFile);
        $this->iamgeRepository->save($image);
        $this->image = $this->iamgeRepository->findOneBy(['name'=>'my_image.png']);

        $this->assertEquals('my_image.png', $this->image->getImageName());
    }

    public function testAdd()
    {
        $image =  new Image();
        $image->setImageFile( $this->imageFile);
        $this->iamgeRepository->add($image , true);
        $this->image = $this->iamgeRepository->findOneBy(['name'=>'my_image.png']);

        $this->assertEquals('my_image.png', $this->image->getImageName());
    }

    public function testDelete()
    {
        $this->image = $this->iamgeRepository->findOneBy([]);
        $id = $this->image->getId();
        $this->iamgeRepository->delete($this->image);

        $this->assertEquals(null , $this->iamgeRepository->findOneBy(['id'=>$id]));
    }

    public function testRemove()
    {
        $this->image = $this->iamgeRepository->findOneBy([]);
        $id = $this->image->getId();
        $this->iamgeRepository->remove($this->image,true);

        $this->assertEquals(null , $this->iamgeRepository->findOneBy(['id'=>$id]));
    }

    public function testSetImageLink()
    {
        $this->image = $this->iamgeRepository->findOneBy([]);
        $id = $this->image->getId();
        $this->iamgeRepository->setImageLink($this->image , 'test_link');

        $this->assertEquals('test_link' , $this->iamgeRepository->findOneBy(['id'=>$id])->getLink());
    }
}
