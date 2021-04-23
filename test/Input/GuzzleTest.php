<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPngTest\Input;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use TinyPng\Input\Guzzle;

class GuzzleTest extends TestCase
{
    public function testReturnsCorrectStream()
    {
        $correctUrl = 'https://run.mocky.io/v3/f428ebd6-20a6-4c54-9812-f13b99e251d8';
        $guzzle = new Guzzle($correctUrl);
        $buffer = $guzzle->getBuffer();
        $this->assertInstanceOf(StreamInterface::class, $buffer);
        $this->assertGreaterThan(0, $buffer->getSize());
    }
}
