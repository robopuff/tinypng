<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPngTest\Output;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TinyPng\Client\GuzzleClient;
use TinyPng\Output\Command\Metadata;
use TinyPng\Output\Output;
use TinyPng\Output\Storage\Filesystem;
use TinyPng\Output\Storage\StorageInterface;

class OutputTest extends TestCase
{
    use ProphecyTrait;

    public function testStoreIsCreatingCorrectRequest()
    {
        /** @var ResponseInterface|ObjectProphecy $storeResponse */
        $storeResponse = $this->prophesize(Response::class);

        /** @var GuzzleClient|ObjectProphecy $client */
        $client = $this->prophesize(GuzzleClient::class);
        $client->request('post', 'http://example.com/image.png', [
            'preserve' => ['creation']
        ])->will([$storeResponse, 'reveal'])->shouldBeCalled();

        /** @var StreamInterface|ObjectProphecy $stream */
        $stream = $this->prophesize(Stream::class);
        $stream->getContents()->willReturn('{}')->shouldBeCalled();

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(Response::class);
        $response->getBody()->will([$stream, 'reveal'])->shouldBeCalled();
        $response->getHeaderLine('Location')->willReturn('http://example.com/image.png');

        /** @var StorageInterface|ObjectProphecy $storage */
        $storage = $this->prophesize(Filesystem::class);
        $storage->store($storeResponse->reveal());

        $output = new Output($client->reveal(), $response->reveal());
        $output->setCommands(new Metadata(Metadata::METADATA_CREATION));
        $output->store($storage->reveal());
    }

    public function testSizeAndTypeAreRetrievedCorrectlyFromResponse()
    {
        /** @var GuzzleClient|ObjectProphecy $guzzle */
        $guzzle = $this->prophesize(GuzzleClient::class);

        /** @var StreamInterface|ObjectProphecy $stream */
        $stream = $this->prophesize(Stream::class);
        $stream->getContents()->willReturn('{"size":120,"type":"application/safetype"}')->shouldBeCalled();

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(Response::class);
        $response->getBody()->will([$stream, 'reveal'])->shouldBeCalled();

        $output = new Output($guzzle->reveal(), $response->reveal());

        $this->assertSame($response->reveal(), $output->getResponse());
        $this->assertEquals(120, $output->getSize());
        $this->assertEquals('application/safetype', $output->getType());
    }
}
