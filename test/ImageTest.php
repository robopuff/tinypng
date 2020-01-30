<?php

namespace TinyPngTest;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TinyPng\Exception\InvalidResourceException;
use TinyPng\Image;

class ImageTest extends TestCase
{
    /**
     * @var StreamInterface|ObjectProphecy
     */
    public $stream;

    /**
     * @var Image\Metadata|ObjectProphecy
     */
    public $metadata;

    /**
     * @var string
     */
    public $streamContent;

    public function setUp(): void
    {
        $this->streamContent = random_bytes(128);

        $this->metadata = $this->prophesize(Image\Metadata::class);
        $this->stream = $this->prophesize(StreamInterface::class);
        $this->stream->getContents()->willReturn($this->streamContent);
    }

    public function testCanCreateInstanceFromStaticMethod()
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getHeaders()->willReturn([])->shouldBeCalled();
        $response->getBody()->will([$this->stream, 'reveal'])->shouldBeCalled();

        Image::fromResponse($response->reveal());
    }

    public function testCanGetMetadata()
    {
        $image = new Image($this->metadata->reveal(), $this->stream->reveal());
        $this->assertSame($this->metadata->reveal(), $image->getMetadata());
    }

    public function testCanGetDataStream()
    {
        $image = new Image($this->metadata->reveal(), $this->stream->reveal());
        $this->assertSame($this->stream->reveal(), $image->getDataStream());
    }

    public function testCanGetDataAsString()
    {
        $image = new Image($this->metadata->reveal(), $this->stream->reveal());
        $this->assertSame($this->streamContent, $image->getData());
    }

    public function testCanMapToString()
    {
        $image = new Image($this->metadata->reveal(), $this->stream->reveal());
        $this->assertSame($this->streamContent, (string) $image);
    }

    public function testCanSaveToFile()
    {
        $eof = 0;
        $this->stream->rewind()->shouldBeCalled();
        $this->stream->eof()->will(function () use (&$eof) {
            return $eof ++ > 0;
        })->shouldBeCalledTimes(2);
        $this->stream->read(1024)->willReturn($this->streamContent);

        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');
        $image = new Image($this->metadata->reveal(), $this->stream->reveal());
        $image->toFile($path);

        $this->assertEquals($this->streamContent, file_get_contents($path));
    }

    public function testSaveToAssignedResource()
    {
        $eof = 0;
        $this->stream->rewind()->shouldBeCalled();
        $this->stream->eof()->will(function () use (&$eof) {
            return $eof ++ > 0;
        })->shouldBeCalledTimes(2);
        $this->stream->read(1024)->willReturn($this->streamContent);

        $resource = fopen('php://memory', 'wb');
        $image = new Image($this->metadata->reveal(), $this->stream->reveal());
        $image->toFile($resource);

        rewind($resource);
        $this->assertEquals($this->streamContent, fread($resource, strlen($this->streamContent)));
    }

    public function testSaveToFileThrowsExceptionWhenResourceNotAvailable()
    {
        $image = new Image($this->metadata->reveal(), $this->stream->reveal());

        $this->expectException(InvalidResourceException::class);
        $image->toFile('/path/that/will/not/exist/in/file/system/' . bin2hex(random_bytes(32)));
    }
}
