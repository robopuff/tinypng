<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPngTest;

use PHPUnit\Framework\TestCase;
use TinyPng\Client\GuzzleClient;
use TinyPng\Input\Filesystem as FilesystemInput;
use TinyPng\Input\Guzzle;
use TinyPng\Output\Command\Metadata;
use TinyPng\Output\Command\Resize;
use TinyPng\Output\Storage\Filesystem as FilesystemStorage;
use TinyPng\TinyPng;

class Integration extends TestCase
{

    public function testShouldCompressFromFile()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');

        $tiny = new TinyPng(new GuzzleClient(getenv('TINYPNG_KEY')));
        $output = $tiny->optimize(new FilesystemInput(__DIR__ . '/TestAssets/voormedia.png'));
        $output->store(new FilesystemStorage($path));

        $size = filesize($path);
        $contents = fread(fopen($path, 'rb'), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(1500, $size);

        /* width == 137 */
        $this->assertStringContainsString("\0\0\0\x89", $contents, 'Has width == 137');
        $this->assertStringNotContainsString('Copyright Voormedia', $contents, 'Does not contain copyright');
    }

    public function testShouldCompressFromUrl()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');

        $tiny = new TinyPng(new GuzzleClient(getenv('TINYPNG_KEY')));
        $output = $tiny->optimize(new Guzzle(
            'https://raw.githubusercontent.com/tinify/tinify-php/master/test/examples/voormedia.png'
        ));
        $output->store(new FilesystemStorage($path));

        $size = filesize($path);
        $contents = fread(fopen($path, 'rb'), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(1500, $size);

        /* width == 137 */
        $this->assertStringContainsString("\0\0\0\x89", $contents, 'Has width == 137');
        $this->assertStringNotContainsString('Copyright Voormedia', $contents, 'Does not contain copyright');
    }

    public function testShouldResize()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');

        $tiny = new TinyPng(new GuzzleClient(getenv('TINYPNG_KEY')));
        $output = $tiny->optimize(new FilesystemInput(__DIR__ . '/TestAssets/voormedia.png'));
        $output->setCommands(new Resize(Resize::METHOD_FIT, 50, 20));
        $output->store(new FilesystemStorage($path));

        $size = filesize($path);
        $contents = fread(fopen($path, 'rb'), $size);

        $this->assertGreaterThan(500, $size);
        $this->assertLessThan(1000, $size);

        /* width == 50 */
        $this->assertStringContainsString("\0\0\0\x32", $contents, 'Has width == 50');
        $this->assertStringNotContainsString('Copyright Voormedia', $contents, 'Does not contain copyright');
    }

    public function testShouldPreserveMetadata()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');

        $tiny = new TinyPng(new GuzzleClient(getenv('TINYPNG_KEY')));
        $output = $tiny->optimize(new FilesystemInput(__DIR__ . '/TestAssets/voormedia.png'));
        $output->setCommands(new Metadata(Metadata::METADATA_COPYRIGHT, Metadata::METADATA_CREATION));
        $output->store(new FilesystemStorage($path));

        $size = filesize($path);
        $contents = fread(fopen($path, 'rb'), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(2000, $size);

        /* width == 137 */
        $this->assertStringContainsString("\0\0\0\x89", $contents, 'Has width == 137');
        $this->assertStringContainsString('Copyright Voormedia', $contents, 'Contains copyright');
    }

    public function testShouldPreserveMetadataAndResize()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');

        $tiny = new TinyPng(new GuzzleClient(getenv('TINYPNG_KEY')));
        $output = $tiny->optimize(new FilesystemInput(__DIR__ . '/TestAssets/voormedia.png'));
        $output->setCommands(
            new Resize(Resize::METHOD_FIT, 50, 20),
            new Metadata(Metadata::METADATA_COPYRIGHT, Metadata::METADATA_CREATION),
        );
        $output->store(new FilesystemStorage($path));

        $size = filesize($path);
        $contents = fread(fopen($path, 'rb'), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(2000, $size);

        /* width == 50 */
        $this->assertStringContainsString("\0\0\0\x32", $contents, 'Has width == 50');
        $this->assertStringContainsString('Copyright Voormedia', $contents, 'Contains copyright');
    }
}
