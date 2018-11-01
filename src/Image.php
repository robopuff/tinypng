<?php

namespace TinyPng;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TinyPng\Image\Metadata;

class Image
{
    /**
     * @var Metadata
     */
    private $meta;

    /**
     * @var StreamInterface
     */
    private $dataStream;

    /**
     * Get image based on response
     * @param ResponseInterface $response
     * @return Image
     */
    public static function fromResponse(ResponseInterface $response): Image
    {
        return new self(
            new Image\Metadata($response->getHeaders()),
            $response->getBody()
        );
    }

    /**
     * Image constructor
     * @param Metadata $meta
     * @param StreamInterface $dataStream
     */
    public function __construct(Metadata $meta, StreamInterface $dataStream)
    {
        $this->meta = $meta;
        $this->dataStream = $dataStream;
    }

    /**
     * Return image content
     * @alias self::getData()
     * @return string
     */
    public function __toString()
    {
        return $this->getData();
    }

    /**
     * Get image metadata
     * @return Metadata
     */
    public function getMetadata(): Metadata
    {
        return $this->meta;
    }

    /**
     * Get content as string
     * @return string
     */
    public function getData(): string
    {
        return $this->getDataStream()->getContents();
    }

    /**
     * Get content as stream interface
     * @return StreamInterface
     */
    public function getDataStream(): StreamInterface
    {
        return $this->dataStream;
    }

    /**
     * Save content to specified file
     * @param string|resource $output
     * @throws Exception\InvalidResourceException
     */
    public function toFile($output): void
    {
        if (\is_resource($output)) {
            $resource = $output;
        } else {
            $resource = @fopen($output, 'wb');
        }

        if (!$resource) {
            throw new Exception\InvalidResourceException('Resource does not exists');
        }

        $this->getDataStream()->rewind();
        while (!$this->getDataStream()->eof()) {
            fwrite($resource, $this->getDataStream()->read(1024));
        }

        if (!\is_resource($output)) {
            fclose($resource);
        }
    }
}
