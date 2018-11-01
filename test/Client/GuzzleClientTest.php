<?php

namespace TinyPngTest\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use TinyPng\Client\GuzzleClient;

class GuzzleClientTest extends TestCase
{
    /**
     * @var Client|ObjectProphecy
     */
    public $guzzle;

    public function setUp()
    {
        $this->guzzle = $this->prophesize(Client::class);
    }

    public function testSetDefaultClient()
    {
        $guzzle = $this->guzzle;
        $client = new GuzzleClient([], $guzzle->reveal());
        (function () use ($guzzle) {
            GuzzleClientTest::assertSame($guzzle->reveal(), $this->client);
        })->call($client);
    }

    public function testSetApiKeyIsStored()
    {
        $key = random_bytes(32);
        $client = new GuzzleClient();
        $client->setApiKey($key);
        (function () use ($key) {
            GuzzleClientTest::assertSame($key, $this->apiKey);
        })->call($client);
    }

    public function testRequestAddsHeaders()
    {
        $apiKey = random_bytes(32);
        $this->guzzle->request('get', 'http://example.com', Argument::that(function ($argument) use ($apiKey) {
            GuzzleClientTest::assertArrayNotHasKey('body', $argument);
            GuzzleClientTest::assertArrayHasKey('headers', $argument);
            GuzzleClientTest::assertArrayHasKey('User-Agent', $argument['headers']);
            GuzzleClientTest::assertArrayHasKey('Authorization', $argument['headers']);
            GuzzleClientTest::assertEquals('Basic ' . base64_encode($apiKey), $argument['headers']['Authorization']);
            return true;
        }))->willReturn(new Response())->shouldBeCalled();

        $client = new GuzzleClient([], $this->guzzle->reveal());
        $client->setApiKey($apiKey);
        $client->request('get', 'http://example.com', []);
    }

    public function testRequestWithBodyAsString()
    {
        $body = random_bytes(128);
        $this->guzzle->request('get', 'http://example.com', Argument::that(function ($argument) use ($body) {
            GuzzleClientTest::assertArrayNotHasKey('json', $argument);
            GuzzleClientTest::assertArrayHasKey('body', $argument);
            GuzzleClientTest::assertSame($body, $argument['body']);
            return true;
        }))->willReturn(new Response())->shouldBeCalled();

        $client = new GuzzleClient([], $this->guzzle->reveal());
        $client->request('get', 'http://example.com', $body);
    }

    public function testRequestWithBodyAsArray()
    {
        $body = ['key' => random_bytes(128)];
        $this->guzzle->request('get', 'http://example.com', Argument::that(function ($argument) use ($body) {
            GuzzleClientTest::assertArrayNotHasKey('body', $argument);
            GuzzleClientTest::assertArrayHasKey('json', $argument);
            GuzzleClientTest::assertSame($body, $argument['json']);
            return true;
        }))->willReturn(new Response())->shouldBeCalled();

        $client = new GuzzleClient([], $this->guzzle->reveal());
        $client->request('get', 'http://example.com', $body);
    }
}
