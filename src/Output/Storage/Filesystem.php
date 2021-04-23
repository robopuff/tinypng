<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Output\Storage;

use Psr\Http\Message\ResponseInterface;

class Filesystem implements StorageInterface
{
    private string $filepath;

    /**
     * Save file into local filesystem
     * @param string $filepath
     */
    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * @throws Exception\InvalidResourceException
     */
    public function store(ResponseInterface $response): void
    {
        $resource = @fopen($this->filepath, 'wb');
        if (false === $resource) {
            throw new Exception\InvalidResourceException('Error creating resource');
        }

        $stream = $response->getBody();
        $stream->rewind();
        while (!$stream->eof()) {
            fwrite($resource, $stream->read(1024));
        }
        fclose($resource);
    }
}
