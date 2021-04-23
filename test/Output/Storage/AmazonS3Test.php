<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPngTest\Output\Storage;

use PHPUnit\Framework\TestCase;
use TinyPng\Output\Storage\AmazonS3;

class AmazonS3Test extends TestCase
{
    public function testRequestBodyReturnsValidCommand()
    {
        $as3 = new AmazonS3([
            'a' => 'b',
            'c' => 123
        ]);

        $this->assertEquals([
            'store' => [
                'service' => 's3',
                'a' => 'b',
                'c' => 123
            ]
        ], $as3->requestBody());
    }
}
