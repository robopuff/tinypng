<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Output\Storage;

use Psr\Http\Message\ResponseInterface;

/**
 * Class GoogleCloudStorage
 * @package TinyPng\Output\Storage
 * @link https://tinypng.com/developers/reference#saving-to-google-cloud-storage
 */
class GoogleCloudStorage implements StorageInterface, StorageRequestInterface
{
    private array $arguments;

    /**
     * Saving to Google Cloud Storage
     * You can tell the Tinify API to save compressed images directly to
     * Google Cloud Storage. If you use GCS to host your images this saves
     * you the hassle of downloading images to your server and uploading
     * them to GCS yourself.
     * @param array<string, mixed> $arguments [
     *     'gcp_access_token' => ''. // (string),
     *     'path' => '', // (string) The path at which you want to store the image including the bucket name.
     *                   // The path must be supplied in the following format: <bucket>/<path>/<filename>.
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
                'service' => 'gcs'
            ] + $this->arguments
        ];
    }
}
