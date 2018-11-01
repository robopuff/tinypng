<?php

namespace TinyPngTest\Exception;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TinyPng\Exception\ResponseErrorException;

class ResponseErrorExceptionTest extends TestCase
{
    public function testExceptionProvidesResponseBodyProvidedByString()
    {
        $exception = new ResponseErrorException('Message', 'Response body');
        $this->assertSame('Response body', $exception->getResponseBody());
    }

    public function testExceptionProvidesResponseBodyProvidedByResponseInterface()
    {
        /** @var StreamInterface|ObjectProphecy $body */
        $body = $this->prophesize(StreamInterface::class);
        $body->getContents()->willReturn('Response body');

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->will([$body, 'reveal']);

        $exception = new ResponseErrorException('Message', $response->reveal());
        $this->assertSame('Response body', $exception->getResponseBody());
    }
}
