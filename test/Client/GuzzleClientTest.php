<?php

namespace TinyPngTest\Client;

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use TinyPng\Client\GuzzleClient;

class GuzzleClientTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Client|ObjectProphecy
     */
    public $guzzle;

    public function setUp(): void
    {
        $this->guzzle = $this->prophesize(Client::class);
    }

    public function testSetDefaultClient()
    {
        $guzzle = $this->guzzle;
        $client = new GuzzleClient('1234', [], $guzzle->reveal());
        (function () use ($guzzle) {
            GuzzleClientTest::assertSame($guzzle->reveal(), $this->client);
        })->call($client);
    }

    public function testSetApiKeyIsStored()
    {
        $key = random_bytes(32);
        $this->guzzle->request('get', 'http://example.com', Argument::that(
            function ($argument) use($key) {
                $authKey = sprintf('Basic %s', base64_encode($key));
                GuzzleClientTest::assertEquals($authKey, $argument['headers']['Authorization']);
                return true;
            }
        ))->willReturn(new Response())->shouldBeCalled();


        $client = new GuzzleClient($key, [], $this->guzzle->reveal());
        $client->request('get', 'http://example.com');
    }

    public function testRequestAddsHeaders()
    {
        $apiKey = random_bytes(32);
        $this->guzzle->request('get', 'http://example.com', Argument::that(
            function ($argument) use ($apiKey) {
                GuzzleClientTest::assertArrayNotHasKey('body', $argument);
                GuzzleClientTest::assertArrayHasKey('headers', $argument);
                GuzzleClientTest::assertArrayHasKey('User-Agent', $argument['headers']);
                GuzzleClientTest::assertArrayHasKey('Authorization', $argument['headers']);
                GuzzleClientTest::assertEquals('Basic ' . base64_encode($apiKey), $argument['headers']['Authorization']);
                return true;
            }
        ))->willReturn(new Response())->shouldBeCalled();

        $client = new GuzzleClient($apiKey, [], $this->guzzle->reveal());
        $client->request('get', 'http://example.com', ['array' => 'of']);
    }

    public function testRequestWithBodyAsString()
    {
        $body = random_bytes(128);
        $this->guzzle->request('get', 'http://example.com', Argument::that(
            function ($argument) use ($body) {
                GuzzleClientTest::assertArrayNotHasKey('json', $argument);
                GuzzleClientTest::assertArrayHasKey('body', $argument);
                GuzzleClientTest::assertSame($body, $argument['body']);
                return true;
            }
        ))->willReturn(new Response())->shouldBeCalled();

        $client = new GuzzleClient('123', [], $this->guzzle->reveal());
        $client->request('get', 'http://example.com', $body);
    }

    public function testRequestWithBodyAsArray()
    {
        $body = ['key' => random_bytes(128)];
        $this->guzzle->request('get', 'http://example.com', Argument::that(
            function ($argument) use ($body) {
                GuzzleClientTest::assertArrayNotHasKey('body', $argument);
                GuzzleClientTest::assertArrayHasKey('json', $argument);
                GuzzleClientTest::assertSame($body, $argument['json']);
                return true;
            }
        ))->willReturn(new Response())->shouldBeCalled();

        $client = new GuzzleClient('123', [], $this->guzzle->reveal());
        $client->request('get', 'http://example.com', $body);
    }
}
