<?php

namespace App\Tests\testByEndpoint;

use App\Entity\Image;
use App\Tests\TestMain;


class ImageTest extends TestMain
{
    public function testImageCollection(): void
    {
        $this->getCollection('/api/images', Image::class);
    }

    public function testImageRecord(): void
    {
        $this->getRecord(Image::class);
    }

}