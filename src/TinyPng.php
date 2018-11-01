<?php

namespace TinyPng;

use TinyPng\Client\ClientInterface;

class TinyPng
{
    public const VERSION  = '0.1.0';
    public const ENDPOINT =  'https://api.tinify.com';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * TinyPng constructor.
     * @param string $apiKey
     * @param null|ClientInterface $client
     */
    public function __construct(string $apiKey, ?ClientInterface $client = null)
    {
        $this->apiKey = $apiKey;

        if (null === $client) {
            $client = new Client\GuzzleClient();
        }

        $this->setClient($client);
    }

    /**
     * Set client interface
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client): void
    {
        $client->setApiKey($this->apiKey);
        $this->client = $client;
    }

    /**
     * Get HTTP client
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Process file from string
     * @param string $file Local path to a source file
     * @return Source
     * @throws Exception\FileNotFoundException
     * @throws Exception\InvalidResponseException
     * @throws Exception\ResponseErrorException
     */
    public function fromFile(string $file): Source
    {
        if (!file_exists($file)) {
            throw new Exception\FileNotFoundException(
                sprintf('File `%s` not found', $file)
            );
        }

        $content = @file_get_contents($file);
        if (false === $content) {
            throw new Exception\FileNotFoundException(
                sprintf('Cannot read `%s` file (%s)', $file, error_get_last()['message'] ?? '')
            );
        }

        return $this->fromBuffer($content);
    }

    /**
     * Process file from url
     * @param string $url Path to remote file
     * @return Source
     * @throws Exception\InvalidResponseException
     * @throws Exception\ResponseErrorException
     */
    public function fromUrl(string $url): Source
    {
        $response = $this->getClient()->request('post', '/shrink', [
            'source' => ['url' => $url]
        ]);
        return Source::fromResponse($this->getClient(), $response);
    }

    /**
     * Process file from a string
     * @param string $buffer
     * @return Source
     * @throws Exception\InvalidResponseException
     * @throws Exception\ResponseErrorException
     */
    public function fromBuffer(string $buffer): Source
    {
        $response = $this->getClient()->request('post', '/shrink', $buffer);
        return Source::fromResponse($this->getClient(), $response);
    }

    /**
     * Validate TinyPNG connection and api key
     * @return bool
     * @throws Exception\ValidateResponseErrorException
     */
    public function validate(): bool
    {
        $response = $this->getClient()->request('post', '/shrink');
        if (!\in_array($response->getStatusCode(), [200, 400, 429], true)) {
            throw new Exception\ValidateResponseErrorException(
                sprintf('Validation failed (%s)', $response->getBody()->getContents()),
                $response->getBody()->getContents()
            );
        }
        return true;
    }
}
