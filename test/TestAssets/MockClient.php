<?php

namespace TinyPngTest\TestAssets;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use TinyPng\Client\ClientInterface;

class MockClient implements ClientInterface
{
    /**
     * @var array
     */
    public $mocks = [];

    /**
     * @var string
     */
    public $apiKey;

    /**
     * Get mock id
     * @param string $method
     * @param string $url
     * @return string
     */
    public function getMockId(string $method, string $url): string
    {
        return base64_encode(strtolower($method . $url));
    }

    /**
     * Add mock
     * @param string $method
     * @param string $url
     * @param null $response
     */
    public function mock(string $method, string $url, $response = null): void
    {
        $this->mocks[$this->getMockId($method, $url)] = $response;
    }

    /**
     * Build mock response
     * @param string $method
     * @param string $url
     * @param int $code
     * @param string $body
     * @param array $headers
     */
    public function buildMock(
        string $method,
        string $url,
        int $code = 200,
        string $body = '',
        array $headers = [],
        $callable = null
    ): void {
        $bodyStream = fopen('php://memory', 'wb');
        fwrite($bodyStream, $body);
        rewind($bodyStream);

        $response = new Response($code, $headers, $bodyStream);

        if (\is_callable($callable)) {
            $response = function ($body, $url, $method) use ($response, $callable) {
                $callable($body, $url, $method);
                return $response;
            };
        }

        $this->mocks[$this->getMockId($method, $url)] = $response;
    }

    /**
     * Set api authentication key
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Make a HTTP request using specified method to url with body
     * @param string $method A HTTP method
     * @param string $url A URL to send
     * @param null|string|array $body If it's an array it will be sent as a JSON
     * @return ResponseInterface
     */
    public function request(string $method, string $url, $body = null): ResponseInterface
    {
        $mockId = $this->getMockId($method, $url);
        $mock = $this->mocks[$mockId] ?? null;

        if (!$mock) {
            throw new \RuntimeException('Mock not introduced');
        }

        if (\is_callable($mock)) {
            return $mock($body, $url, $method);
        }

        return $mock;
    }
}
