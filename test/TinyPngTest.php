<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPngTest;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TinyPng\Client\ClientInterface;
use TinyPng\Client\GuzzleClient;
use TinyPng\Exception;
use TinyPng\Input\Filesystem;
use TinyPng\Input\InputInterface;
use TinyPng\Output\Output;
use TinyPng\TinyPng;

class TinyPngTest extends TestCase
{
    use ProphecyTrait;

    public function testOptimize()
    {
        /** @var StreamInterface|ObjectProphecy $body */
        $body = $this->prophesize(Stream::class);
        $body->getContents()->willReturn('{}');

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(Response::class);
        $response->getStatusCode()->willReturn(201)->shouldBeCalled();
        $response->getBody()->will([$body, 'reveal']);

        /** @var ClientInterface|ObjectProphecy $client */
        $client = $this->prophesize(GuzzleClient::class);
        $client->request('post', '/shrink', 'buffer')->will([$response, 'reveal'])->shouldBeCalled();

        /** @var InputInterface|ObjectProphecy $input */
        $input = $this->prophesize(Filesystem::class);
        $input->getBuffer()->willReturn('buffer')->shouldBeCalled();

        $tiny = new TinyPng($client->reveal());
        $output = $tiny->optimize($input->reveal());

        $this->assertInstanceOf(Output::class, $output);
        $this->assertSame($response->reveal(), $output->getResponse());
    }

    public function testOptimizeThrowsExceptionWhenWrongHttpStatusProvided()
    {
        /** @var StreamInterface|ObjectProphecy $body */
        $body = $this->prophesize(Stream::class);
        $body->getContents()->willReturn('error');

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(Response::class);
        $response->getStatusCode()->willReturn(400)->shouldBeCalled();
        $response->getBody()->will([$body, 'reveal']);

        /** @var ClientInterface|ObjectProphecy $client */
        $client = $this->prophesize(GuzzleClient::class);
        $client->request('post', '/shrink', 'buffer')->will([$response, 'reveal'])->shouldBeCalled();

        /** @var InputInterface|ObjectProphecy $input */
        $input = $this->prophesize(Filesystem::class);
        $input->getBuffer()->willReturn('buffer')->shouldBeCalled();

        $tiny = new TinyPng($client->reveal());

        $this->expectException(Exception::class);
        $tiny->optimize($input->reveal());
    }
}
