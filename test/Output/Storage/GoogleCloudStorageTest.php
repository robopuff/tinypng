<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPngTest\Output\Storage;

use PHPUnit\Framework\TestCase;
use TinyPng\Output\Storage\GoogleCloudStorage;

class GoogleCloudStorageTest extends TestCase
{
    public function testRequestBodyReturnsValidCommand()
    {
        $gs = new GoogleCloudStorage([
            'a' => 'b',
            'c' => 123
        ]);

        $this->assertEquals([
            'store' => [
                'service' => 'gcs',
                'a' => 'b',
                'c' => 123
            ]
        ], $gs->requestBody());
    }
}
