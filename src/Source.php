<?php

namespace TinyPng;

use Psr\Http\Message\ResponseInterface;
use TinyPng\Client\ClientInterface;

class Source
{

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $commands = [];

    /**
     * @var string
     */
    private $url;

    /**
     * Get source from response
     * @param ClientInterface $client
     * @param ResponseInterface $response
     * @return Source
     * @throws Exception\InvalidResponseException
     * @throws Exception\ResponseErrorException
     */
    public static function fromResponse(ClientInterface $client, ResponseInterface $response): Source
    {
        $size = $response->getBody()->getSize();
        if (null !== $size && $size < 1) {
            throw new Exception\InvalidResponseException('Response body is empty');
        }

        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception\InvalidResponseException(
                sprintf('Response body json decoding failed with error `%s`', json_last_error_msg())
            );
        }

        if (!empty($json['error'] ?? null)) {
            throw new Exception\ResponseErrorException('Response error occurred', $body);
        }

        return new self($client, $response->getHeaderLine('Location'));
    }

    /**
     * Source constructor.
     * @param ClientInterface $client
     * @param string $url
     */
    public function __construct(ClientInterface $client, string $url)
    {
        $this->client = $client;
        $this->url = $url;
    }

    /**
     * Clear all commands (`resize` and `preserve`)
     * @return Source
     */
    public function clearCommands(): Source
    {
        $this->commands = [];
        return $this;
    }

    /**
     * Preserve metadata in optimized image
     * You can request that specific metadata is copied
     * from the uploaded image to the compressed version.
     * Preserving `copyright` information, the GPS `location` and
     * the `creation` date are currently supported. Preserving
     * @param string[] $arguments Can contain one of supported metadata (copyright, location, creation)
     * @return Source
     */
    public function preserve(array $arguments = []): Source
    {
        $this->commands['preserve'] = $arguments;
        return $this;
    }

    /**
     * Resize image
     * Use the API to create resized versions of your uploaded images.
     * By letting the API handle resizing you avoid having to write
     * such code yourself and you will only have to upload your image once.
     * The resized images will be optimally compressed with a nice and crisp appearance.
     * @param array $arguments [
     *     'method' => 'fit',  // (string) [scale, fit, cover, thumb]
     *     'width'  => 0,  // (int)
     *     'height' => 0 // (int)
     * ]
     * @return Source
     */
    public function resize(array $arguments = []): Source
    {
        $this->commands['resize'] = $arguments;
        return $this;
    }

    /**
     * Saving to Amazon S3
     * You can tell the TinyPNG API to save compressed images directly to Amazon S3.
     * If you use S3 to host your images this saves you
     * the hassle of downloading images to your server and uploading them to S3 yourself.
     * @param array $arguments [
     *     'aws_access_key_id' => ''. // (string),
     *     'aws_secret_access_key' => '', // (string)
     *     'region' => '', // (string) an AWS region
     *     'path' => '', // (string) The path at which you want to store the image including the bucket name
     *                   // The path must be supplied in the following format: `<bucket>/<path>/<filename>`.
     * ]
     * @return Image
     * @throws Exception\ResponseErrorException
     */
    public function saveToAmazonS3(array $arguments = []): Image
    {
        $response = $this->client->request('post', $this->url, $this->commands + [
            'store' => [
                    'service' => 's3'
                ] + $arguments
        ]);
        if ($response->getStatusCode() !== 200) {
            $contents = $response->getBody()->getContents();
            throw new Exception\ResponseErrorException(
                sprintf('Invalid image response (%s)', $contents),
                $contents
            );
        }

        return Image::fromResponse($response);
    }

    /**
     * Get image content and process all commands
     * @return Image
     * @throws Exception\ResponseErrorException
     */
    public function getImage(): Image
    {
        $response = $this->client->request('get', $this->url, $this->commands);
        if ($response->getStatusCode() !== 200) {
            $contents = $response->getBody()->getContents();
            throw new Exception\ResponseErrorException(
                sprintf('Invalid image response (%s)', $contents),
                $contents
            );
        }

        return Image::fromResponse($response);
    }

    /**
     * Save source image to local filesystem
     * @param string $output
     * @throws Exception\InvalidResourceException
     * @throws Exception\ResponseErrorException
     */
    public function toFile($output): void
    {
        $this->getImage()->toFile($output);
    }
}
