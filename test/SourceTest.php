<?php

namespace TinyPngTest;

use PHPStan\Testing\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TinyPng\Exception\InvalidResponseException;
use TinyPng\Exception\ResponseErrorException;
use TinyPng\Source;
use TinyPngTest\TestAssets\MockClient;

class SourceTest extends TestCase
{
    /**
     * @var string
     */
    public $dummyFile;

    /**
     * @var MockClient
     */
    public $client;

    public function setUp(): void
    {
        $this->client = new TestAssets\MockClient();
        $this->dummyFile = __DIR__ . '/TestAssets/dummy.png';
    }

    public function testClassGeneratorFromResponseWithEmptyBody()
    {
        /** @var StreamInterface|ObjectProphecy $body */
        $body = $this->prophesize(StreamInterface::class);
        $body->getSize()->willReturn(0);

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->will([$body, 'reveal']);

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Response body is empty');
        Source::fromResponse($this->client, $response->reveal());
    }

    public function testClassGeneratorFromResponseWithErrorInResponse()
    {
        /** @var StreamInterface|ObjectProphecy $body */
        $body = $this->prophesize(StreamInterface::class);
        $body->getSize()->willReturn(1);
        $body->getContents()->willReturn('{"error":"Unauthorized","message":"Credentials are invalid"}');

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(401);
        $response->getBody()->will([$body, 'reveal']);

        $this->expectException(ResponseErrorException::class);
        Source::fromResponse($this->client, $response->reveal());
    }

    public function testClassGeneratorFromResponseWithInvalidJsonInResponse()
    {
        /** @var StreamInterface|ObjectProphecy $body */
        $body = $this->prophesize(StreamInterface::class);
        $body->getSize()->willReturn(1);
        $body->getContents()->willReturn('Invalid:Json:[Response]');

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->will([$body, 'reveal']);

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessageRegExp('/Response body json decoding failed with error `(.*?)`/');
        Source::fromResponse($this->client, $response->reveal());
    }

    public function testClassGeneratorFromResponseWithProperResponse()
    {
        /** @var StreamInterface|ObjectProphecy $body */
        $body = $this->prophesize(StreamInterface::class);
        $body->getSize()->willReturn(2)->shouldBeCalled();
        $body->getContents()->willReturn('[]')->shouldBeCalled();

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->will([$body, 'reveal'])->shouldBeCalled();
        $response->getHeaderLine('Location')->willReturn('http://location/');

        $parent = $this;
        $response = Source::fromResponse($this->client, $response->reveal());
        (function () use ($parent) {
            $parent->assertSame($parent->client, $this->client);
            $parent->assertSame('http://location/', $this->url);
        })->call($response);
    }

    public function testClearCommandsClearsArray()
    {
        $source = new Source($this->client, 'http://location/');
        (function () {
            $this->commands = range(1, 10);
            $this->clearCommands();
            SourceTest::assertEmpty($this->commands);
        })->call($source);
    }

    public function testPreserveAddsCommand()
    {
        $source = new Source($this->client, 'http//location/');
        $source->preserve(['param' => 'value']);
        (function () {
            SourceTest::assertArrayHasKey('preserve', $this->commands);
            SourceTest::assertEquals(['param' => 'value'], $this->commands['preserve']);
        })->call($source);
    }

    public function testResizeAddsCommand()
    {
        $source = new Source($this->client, 'http//location/');
        $source->resize(['param' => 'value']);
        (function () {
            SourceTest::assertArrayHasKey('resize', $this->commands);
            SourceTest::assertEquals(['param' => 'value'], $this->commands['resize']);
        })->call($source);
    }

    public function testSaveToAmazonS3WithInvalidResponse()
    {
        $this->client->buildMock('post', 'http://location/', 400, '', [], function ($body) {
            SourceTest::assertSame([
                'store' => [
                    'service' => 's3',
                    'path' => 'bucket/path/filename'
                ]
            ], $body);
        });
        $source = new Source($this->client, 'http://location/');

        $this->expectException(ResponseErrorException::class);
        $source->saveToAmazonS3(['path' => 'bucket/path/filename']);
    }

    public function testSaveToAmazonS3()
    {
        $data = random_bytes(32);
        $this->client->buildMock('post', 'http://location/', 200, $data);

        $source = new Source($this->client, 'http://location/');

        $image = $source->saveToAmazonS3([]);
        $this->assertEquals($data, $image->getData());
    }

    public function testGetImageWithInvalidResponse()
    {
        $this->client->buildMock('get', 'http://location/', 400, '', [], function ($body) {
            SourceTest::assertEmpty($body);
        });
        $source = new Source($this->client, 'http://location/');

        $this->expectException(ResponseErrorException::class);
        $source->getImage();
    }

    public function testGetImage()
    {
        $data = random_bytes(32);
        $this->client->buildMock('get', 'http://location/', 200, $data);
        $source = new Source($this->client, 'http://location/');

        $image = $source->getImage();
        $this->assertEquals($data, $image->getData());
    }

    public function testToFile()
    {
        $data = random_bytes(32);
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');
        $this->client->buildMock('get', 'http://location/', 200, $data);
        $source = new Source($this->client, 'http://location/');

        $source->toFile($path);
        $this->assertEquals($data, file_get_contents($path));
    }
}
