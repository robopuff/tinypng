<?php

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

declare(strict_types=1);

namespace TinyPng\Client;

use GuzzleHttp\Client;
use PackageVersions\Versions;
use Psr\Http\Message\ResponseInterface;
use TinyPng\TinyPng;

class GuzzleClient implements ClientInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $apiKey = "";

    /**
     * GuzzleClient constructor.
     * @param array<mixed> $options
     * @param Client|null $client
     */
    public function __construct(array $options = [], Client $client = null)
    {
        if (!$client) {
            $client = new Client([
                    'http_errors' => false,
                    'base_uri'    => TinyPng::ENDPOINT,
                ] + $options);
        }

        $this->client = $client;
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
     * @param null|string|array<mixed> $body If it's an array it will be sent as a JSON
     * @return ResponseInterface
     */
    public function request(string $method, string $url, $body = null): ResponseInterface
    {
        $options = [
            'headers' => [
                'User-Agent' => sprintf(
                    'TinyPng/%s Tinify/1.5.2 PHP/%s Guzzle/%s curl/1',
                    TinyPng::VERSION,
                    PHP_VERSION,
                    Versions::getVersion('guzzlehttp/guzzle')
                ),
                'Authorization' => sprintf('Basic %s', base64_encode($this->apiKey)),
                'Content-Type' => 'application/json',
            ]
        ];

        if (empty($body)) {
            $body = null;
        }

        if (\is_string($body)) {
            $options['body'] = $body;
        }

        if (\is_array($body)) {
            $options['json'] = $body;
        }

        return $this->client->request($method, $url, $options);
    }
}
