<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Test;

use GarbuzIvan\ImageManager\ImageManager;
use PHPUnit\Framework\TestCase;

class ImageManagerTest extends TestCase
{
    public function testGetError()
    {
        $image = new ImageManager();
        $this->assertIsBool(true);
    }
}
