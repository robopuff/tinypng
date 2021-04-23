<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPngTest\Output\Storage;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TinyPng\Output\Storage\Filesystem;

class FilesystemTest extends TestCase
{
    use ProphecyTrait;

    public function testFilesystemStoresData()
    {
        $input = __DIR__ . '/../../TestAssets/voormedia.png';
        $output = tempnam(sys_get_temp_dir(), 'tinypng-php');

        $stream = new Stream(fopen($input, 'r'));

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(Response::class);
        $response->getBody()->willReturn($stream)->shouldBeCalled();

        $fs = new Filesystem($output);
        $fs->store($response->reveal());

        $this->assertFileEquals($input, $output);
    }
}
