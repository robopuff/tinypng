<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Output\Storage;

use Psr\Http\Message\ResponseInterface;

interface StorageInterface
{
    public function store(ResponseInterface $response): void;
}
