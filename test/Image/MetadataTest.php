<?php

namespace TinyPngTest\Image;

use PHPUnit\Framework\TestCase;
use TinyPng\Image\Metadata;

class MetadataTest extends TestCase
{
    public function testGetData()
    {
        $data = random_bytes(32);
        $meta = new Metadata(['random' => $data]);
        $this->assertSame($data, $meta->get('random'));
    }

    public function testGetWidth()
    {
        $data = random_int(1, 32);
        $meta = new Metadata(['image-width' => $data]);
        $this->assertSame($data, $meta->getWidth());
    }

    public function testGetHeight()
    {
        $data = random_int(1, 32);
        $meta = new Metadata(['image-height' => $data]);
        $this->assertSame($data, $meta->getHeight());
    }

    public function testGetLocation()
    {
        $data = 'http://example.com/';
        $meta = new Metadata(['location' => $data]);
        $this->assertSame($data, $meta->getLocation());
    }

    public function testGetSize()
    {
        $data = random_int(2 ^ 6, 32 ^2);
        $meta = new Metadata(['content-length' => $data]);
        $this->assertSame($data, $meta->getSize());
    }

    public function testGetContentType()
    {
        $data = 'text';
        $meta = new Metadata(['content-type' => $data]);
        $this->assertSame($data, $meta->getContentType());
    }
}
