<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TinyPng\TinyPng;

class GuzzleClient implements ClientInterface
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var string
     */
    private string $apiKey = "";

    /**
     * GuzzleClient constructor.
     * @param string $apiKey
     * @param array $options
     * @param Client|null $client
     */
    public function __construct(string $apiKey, array $options = [], ?Client $client = null)
    {
        $this->apiKey = $apiKey;
        $this->client = $client ?? new Client([
            'http_errors' => false,
            'base_uri'    => TinyPng::ENDPOINT,
        ] + $options);
    }

    /**
     * Make a HTTP request using specified method to url with body
     * @param string $method A HTTP method
     * @param string $url A URL to send
     * @param null|string|array|resource|StreamInterface $body If it's an array it will be sent as a JSON
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function request(string $method, string $url, $body = null): ResponseInterface
    {
        $options = [
            'headers' => [
                'User-Agent' => sprintf(
                    'TinyPng/%s Tinify/1.5.2 PHP/%s Guzzle/1 curl/1',
                    TinyPng::VERSION,
                    PHP_VERSION
                ),
                'Authorization' => sprintf('Basic %s', base64_encode($this->apiKey)),
                'Content-Type' => 'application/json',
            ]
        ];

        if (empty($body)) {
            $body = null;
        }

        switch (true) {
            case \is_array($body):
                $options['json'] = $body;
                break;
            default:
                $options['body'] = $body;
                break;
        }

        return $this->client->request($method, $url, $options);
    }
}
