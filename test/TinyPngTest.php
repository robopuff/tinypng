<?php

namespace TinyPngTest;

use PHPStan\Testing\TestCase;
use TinyPng\Client\GuzzleClient;
use TinyPng\Exception\FileNotFoundException;
use TinyPng\Exception\ValidateResponseErrorException;
use TinyPng\TinyPng;
use TinyPngTest\TestAssets\MockClient;

class TinyPngTest extends TestCase
{
    /**
     * @var MockClient
     */
    public $client;

    public function setUp()
    {
        $this->client = new TestAssets\MockClient();
    }

    public function testSetDefaultClient()
    {
        $tiny = new TinyPng('');
        $this->assertInstanceOf(GuzzleClient::class, $tiny->getClient());
    }

    public function testSetClientInConstructor()
    {
        $tiny = new TinyPng('', $this->client);
        $this->assertSame($this->client, $tiny->getClient());
    }

    public function testSetClientAddsApiKeyToClient()
    {
        $apiKey = random_bytes(32);
        $tiny = new TinyPng($apiKey, $this->client);
        $this->assertSame($apiKey, $this->client->apiKey);
    }

    public function testValidateWithValidKeyShouldReturnTrue()
    {
        $body = '{"error":"Input missing","message":"No input"}';
        $this->client->buildMock('post', '/shrink', 400, $body);
        $tiny = new TinyPng('', $this->client);
        $this->assertTrue($tiny->validate());
    }

    public function testValidateWithLimitedKeyShouldReturnTrue()
    {
        $body = '{"error":"Too many requests","message":"Your monthly limit has been exceeded"}';
        $this->client->buildMock('post', '/shrink', 429, $body);
        $tiny = new TinyPng('', $this->client);
        $this->assertTrue($tiny->validate());
    }

    public function testValidateWithErrorShouldThrowException()
    {
        $body = '{"error":"Unauthorized","message":"Credentials are invalid"}';
        $this->client->buildMock('post', '/shrink', 401, $body);
        $tiny = new TinyPng('', $this->client);

        $this->expectException(ValidateResponseErrorException::class);
        $tiny->validate();
    }

    public function testFromBuffer()
    {
        $buffer = random_bytes(128);
        $this->client->buildMock('post', '/shrink', 200, '{}', [], function ($body) use ($buffer) {
            TinyPngTest::assertSame($buffer, $body);
        });

        $tiny = new TinyPng('', $this->client);
        $tiny->fromBuffer($buffer);
    }

    public function testFromFileWithInvalidFile()
    {
        $path = '/path/that/does/not/exist/in/any/system/' . bin2hex(random_bytes(32));

        $tiny = new TinyPng('', $this->client);

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessageRegExp('/File `(.*?)` not found/');
        $tiny->fromFile($path);
    }

    public function testFromFileWithInvalidFileThatCannotBeRead()
    {
        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');
        chmod($path, 0);

        $tiny = new TinyPng('', $this->client);

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessageRegExp('/Cannot read `(.*?)` file \((.*?)\)/');
        $tiny->fromFile($path);
    }

    public function testFromFile()
    {
        $buffer = random_bytes(128);

        $path = tempnam(sys_get_temp_dir(), 'tinypng-php');
        file_put_contents($path, $buffer);

        $this->client->buildMock('post', '/shrink', 200, '{}', [], function ($body) use ($buffer) {
            TinyPngTest::assertSame($buffer, $body);
        });

        $tiny = new TinyPng('', $this->client);
        $tiny->fromFile($path);
    }

    public function testFromUrl()
    {
        $url = 'http://example.com/image.jpg';
        $this->client->buildMock('post', '/shrink', 200, '{}', [], function ($body) use ($url) {
            TinyPngTest::assertSame([
                'source' => [
                    'url' => $url
                ]
            ], $body);
        });

        $tiny = new TinyPng('', $this->client);
        $tiny->fromUrl($url);
    }
}
