<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Output\Storage;

use Psr\Http\Message\ResponseInterface;

/**
 * Class AmazonS3
 * @package TinyPng\Output\Storage
 * @link https://tinypng.com/developers/reference#saving-to-amazon-s3
 */
class AmazonS3 implements StorageInterface, StorageRequestInterface
{
    private array $arguments;

    /**
     * Saving to Amazon S3
     * You can tell the TinyPNG API to save compressed images directly to Amazon S3.
     * If you use S3 to host your images this saves you
     * the hassle of downloading images to your server and uploading them to S3 yourself.
     * @param array<string, mixed> $arguments [
     *     'aws_access_key_id' => ''. // (string),
     *     'aws_secret_access_key' => '', // (string)
     *     'region' => '', // (string) an AWS region
     *     'path' => '', // (string) The path at which you want to store the image including the bucket name
     *                   // The path must be supplied in the following format: `<bucket>/<path>/<filename>`.
     *     'headers' => [], // (array) headers array
     * ]
     */
    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function store(ResponseInterface $response): void
    {
        return; // Do nothing
    }

    public function requestBody(): array
    {
        return [
            'store' => [
                'service' => 's3'
            ] + $this->arguments
        ];
    }
}
