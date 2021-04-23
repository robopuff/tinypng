<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Input;

class Filesystem implements InputInterface
{
    private string $filepath;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * @return resource|false
     * @throws Exception\FilesystemException
     */
    public function getBuffer()
    {
        if (!file_exists($this->filepath)) {
            throw new Exception\FilesystemException(sprintf(
                'File `%s` not found',
                $this->filepath
            ), 404);
        }

        try {
            return fopen($this->filepath, 'r');
        } catch (\Throwable $t) {
            throw new Exception\FilesystemException('Cannot create resource', 500, $t);
        }
    }
}
