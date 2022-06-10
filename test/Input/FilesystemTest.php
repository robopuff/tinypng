<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPngTest\Input;

use PHPUnit\Framework\TestCase;
use TinyPng\Input\Exception\FilesystemException;
use TinyPng\Input\Filesystem;

class FilesystemTest extends TestCase
{
    public function testFileResourceIsReturned()
    {
        $fs = new Filesystem(__DIR__ . '/../TestAssets/voormedia.png');
        $this->assertIsResource($fs->getBuffer());
    }

    public function testExceptionIsThrownWhenFileDoesNotExists()
    {
        $fs = new Filesystem('/a/path/to/the/file/that/does/not/exists.png');
        $this->expectException(FilesystemException::class);
        $this->expectExceptionCode(404);
        $fs->getBuffer();
    }
}
