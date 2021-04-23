<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng;

use TinyPng\Client\ClientInterface;
use TinyPng\Input\InputInterface;
use TinyPng\Output\OutputInterface;

class TinyPng
{
    public const VERSION  = '1.0.0';
    public const ENDPOINT =  'https://api.tinify.com';

    private ClientInterface $client;

    /**
     * TinyPng constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function optimize(InputInterface $input): OutputInterface
    {
        $response = $this->client->request(ClientInterface::METHOD_POST, '/shrink', $input->getBuffer());
        if ($response->getStatusCode() !== 201) {
            throw new Exception(sprintf(
                'Invalid response, code: `%d`, message: `%s`',
                $response->getStatusCode(),
                $response->getBody()->getContents()
            ));
        }

        return new Output\Output($this->client, $response);
    }
}
