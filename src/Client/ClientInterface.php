<?php

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

declare(strict_types=1);

namespace TinyPng\Client;

use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * Set api authentication key
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void;

    /**
     * Make a HTTP request using specified method to url with body
     * @param string $method A HTTP method
     * @param string $url A URL to send
     * @param null|string|array<mixed> $body If it's an array it will be sent as a JSON
     * @return ResponseInterface
     */
    public function request(string $method, string $url, $body = null): ResponseInterface;
}
