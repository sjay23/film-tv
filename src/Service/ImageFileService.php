<?php

namespace App\Service;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Imagine\Gd\Imagine;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageFileService
{
    private Imagine $imagine;
    private ImageRepository $imageRepository;

    public function __construct(
        ParameterBagInterface $params,
        ImageRepository $imageRepository
    ) {
        $this->imagine = new Imagine();
        $this->imageRepository = $imageRepository;
        $this->importFolder = $params->get('import_images_file');
    }

    public function getUploadFileByUrl(?string $url): ?UploadedFile
    {
        if ($url == null) {
            return null;
        }
        $url = str_replace(" ", "%20", $url);
        try {
            $image = $this->imagine->open($url);
        } catch (\Exception $e) {
            echo "retry Image download \n";
            $image = $this->imagine->open($url);
        }
        $pathInfo = pathinfo($url);
        $filename = md5(microtime(true) . $pathInfo['filename']) . '.' . $pathInfo['extension'] ;
        $pathThumb = $this->importFolder . '/' . $filename ;
        $image->save($pathThumb);

        return new UploadedFile($pathThumb, $filename, null, null, true);
    }

    public function updateFile(?Image $image, ?UploadedFile $uploadedFile): void
    {
        $image->setUploaded(true);
        $image->setImageFile($uploadedFile);
        $this->imageRepository->save($image);
    }
}
