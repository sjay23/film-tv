<?php

namespace App\Tests\Controller;

use App\Entity\Image;
use App\Tests\TestMain;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ImageControllerTest extends TestMain
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->imageRepository = $this->entityManager
            ->getRepository(Image::class);
        $this->idRecord = $this->imageRepository->findOneBy([])->getId();
    }

    public function testImageCollection(): void
    {
        $this->getCollection('/api/images', Image::class);
    }

    public function testImageRecord(): void
    {
        $this->getRecord(Image::class);
    }

    public function testAddImage(): void
    {
        $genreUri = $this->router->generate('add_image');
        $files =  new UploadedFile(
            './tests/image/test_image1.png',
            'my_image.png',
            'image/png',
        );
         $this->sendPostUriForUploadFile($genreUri, [
            'images' => $files
        ]);
    }

    public function testUpdateImage(): void
    {
        $imageUri = $this->router->generate('update_image',['id'=>126]);

        /**
         * Insert Image
         */
        $response = $this->sendPostUriForUpdate($imageUri, [
            'link' => 'test link'
        ]);

        $responseRecord = json_decode($response->getContent());
        /**
         * @var Image $imageRecord
         */
        $imageRecord = $this->imageRepository->findOneBy(['id' => $responseRecord->id]);

        $imageId = $imageRecord->getId();
        $testUri = static::findIriBy(Image::class, ['id' => $imageId]);
        if ($testUri) {
            $this->sendGetUri($testUri);
        }

        $this->assertMatchesResourceItemJsonSchema(Image::class);
        $this->assertEquals('test link', $imageRecord->getLink());
    }

    public function testDeleteRecord(): void
    {
        $recordDeleteUri = $this->router->generate('delete_image', array('id' => $this->idRecord));

        $this->sendDeleteUri($recordDeleteUri);

        $testUri = static::findIriBy(Image::class, ['id' => $this->idRecord]);

        $this->assertEquals(null, $testUri);
    }
}