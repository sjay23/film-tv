<?php

namespace App\Service;


use Imagine\Imagick\Imagine;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class ImageFileService
{

    private Imagine $imagine;

    public function __construct(
        ParameterBagInterface $params
    ) {
        $this->imagine = new Imagine();
        $this->importFolder = $params->get('import_product_file');

    }

    public function getUploadFileByUrl(?object $poster): ?UploadedFile
    {
        $url=$poster->getLink();
        if ($url == null) return null;
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
}
