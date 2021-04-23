<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Input;

use Psr\Http\Message\StreamInterface;

interface InputInterface
{
    /**
     * @return null|string|array|resource|StreamInterface
     */
    public function getBuffer();
}
