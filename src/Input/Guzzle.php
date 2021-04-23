<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Input;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;

class Guzzle implements InputInterface
{
    private ClientInterface $client;
    private string $url;
    private string $httpMethod;
    private array $options;

    public function __construct(
        string $url,
        string $httpMethod = 'get',
        array $options = [],
        ?ClientInterface $client = null
    ) {
        $this->client = $client ?? new Client([
            'http_errors' => false,
        ]);
        $this->url = $url;
        $this->httpMethod = $httpMethod;
        $this->options = $options;
    }

    /**
     * @throws GuzzleException
     */
    public function getBuffer(): StreamInterface
    {
        $response = $this->client->request($this->httpMethod, $this->url, $this->options);
        return $response->getBody();
    }
}
