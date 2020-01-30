<?php

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

declare(strict_types=1);

namespace TinyPng\Exception;

use Psr\Http\Message\ResponseInterface;

class ResponseErrorException extends \TinyPng\Exception
{
    /**
     * @var null|string
     */
    private $responseBody;

    /**
     * ResponseErrorException constructor.
     * @param string $message
     * @param null|string|ResponseInterface $responseBody
     */
    public function __construct(string $message = '', $responseBody = null)
    {
        parent::__construct($message, 0, null);

        if ($responseBody instanceof ResponseInterface) {
            $responseBody = $responseBody->getBody()->getContents();
        }

        $this->responseBody = $responseBody;
    }

    /**
     * Get raw response body, if set
     * @return null|string
     */
    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }
}
