<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Output;

use Psr\Http\Message\ResponseInterface;
use TinyPng\Client\ClientInterface;
use TinyPng\Output\Command\CommandInterface;
use TinyPng\Output\Storage\StorageInterface;
use TinyPng\Output\Storage\StorageRequestInterface;

class Output implements OutputInterface
{
    private ResponseInterface $response;
    private ClientInterface $client;

    /**
     * @var CommandInterface[]
     */
    private array $commands = [];

    /**
     * @var array|mixed
     */
    private $body;

    /**
     * Output constructor.
     * @param ClientInterface $client
     * @param ResponseInterface $response
     */
    public function __construct(ClientInterface $client, ResponseInterface $response)
    {
        $this->client = $client;
        $this->response = $response;

        $body = $this->response->getBody()->getContents();
        $this->body = json_decode($body, true);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getSize(): int
    {
        return $this->body['size'] ?? 0;
    }

    public function getType(): string
    {
        return $this->body['type'] ?? '';
    }

    public function setCommands(CommandInterface ...$commands): void
    {
        $this->commands = $commands;
    }

    public function store(StorageInterface $storage): void
    {
        $url = $this->response->getHeaderLine('Location');

        $body = [];
        foreach ($this->commands as $cmd) {
            $body = $body + $cmd->getCommand();
        }

        if ($storage instanceof StorageRequestInterface) {
            $body = $body + $storage->requestBody();
        }

        $response = $this->client->request(ClientInterface::METHOD_POST, $url, $body);
        $storage->store($response);
    }
}
