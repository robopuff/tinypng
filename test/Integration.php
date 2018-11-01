<?php

namespace TinyPngTest;

use PHPUnit\Framework\TestCase;
use TinyPng\Client\GuzzleClient;
use TinyPng\Source;
use TinyPng\TinyPng;

class Integration extends TestCase
{
    /**
     * @var TinyPng
     */
    static public $tiny;

    /**
     * @var Source
     */
    static public $optimized;

    public static function setUpBeforeClass()
    {
        self::$tiny = new TinyPng(getenv('TINYPNG_KEY'), new GuzzleClient());
        self::$tiny->validate();

        self::$optimized = self::$tiny->fromFile(__DIR__ . '/TestAssets/voormedia.png');
    }

    public function testShouldCompressFromFile()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');
        self::$optimized->toFile($path);

        $size = filesize($path);
        $contents = fread(fopen($path, 'rb'), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(1500, $size);

        /* width == 137 */
        $this->assertContains("\0\0\0\x89", $contents, 'Has width == 137');
        $this->assertNotContains('Copyright Voormedia', $contents, 'Does not contain copyright');
    }

    public function testShouldCompressFromUrl()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');
        $source = self::$tiny->fromUrl(
            'https://raw.githubusercontent.com/tinify/tinify-php/master/test/examples/voormedia.png'
        );
        $source->toFile($path);

        $size = filesize($path);
        $contents = fread(fopen($path, 'rb'), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(1500, $size);

        /* width == 137 */
        $this->assertContains("\0\0\0\x89", $contents, 'Has width == 137');
        $this->assertNotContains('Copyright Voormedia', $contents, 'Does not contain copyright');
    }

    public function testShouldResize()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');
        self::$optimized->clearCommands();
        self::$optimized->resize(['method' => 'fit', 'width' => 50, 'height' => 20])->toFile($path);

        $size = filesize($path);
        $contents = fread(fopen($path, 'rb'), $size);

        $this->assertGreaterThan(500, $size);
        $this->assertLessThan(1000, $size);

        /* width == 50 */
        $this->assertContains("\0\0\0\x32", $contents, 'Has width == 50');
        $this->assertNotContains('Copyright Voormedia', $contents, 'Does not contain copyright');
    }

    public function testShouldPreserveMetadata()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');
        self::$optimized->clearCommands();
        self::$optimized->preserve(['copyright', 'creation'])->toFile($path);

        $size = filesize($path);
        $contents = fread(fopen($path, 'rb'), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(2000, $size);

        /* width == 137 */
        $this->assertContains("\0\0\0\x89", $contents, 'Has width == 137');
        $this->assertContains('Copyright Voormedia', $contents, 'Contains copyright');
    }

    public function testShouldPreserveMetadataAndResize()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');
        self::$optimized->clearCommands();
        self::$optimized->preserve(['copyright', 'creation'])->toFile($path);
        self::$optimized->resize(['method' => 'fit', 'width' => 50, 'height' => 20])->toFile($path);

        $size = filesize($path);
        $contents = fread(fopen($path, 'rb'), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(2000, $size);

        /* width == 50 */
        $this->assertContains("\0\0\0\x32", $contents, 'Has width == 50');
        $this->assertContains('Copyright Voormedia', $contents, 'Contains copyright');
    }
}
