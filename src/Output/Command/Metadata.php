<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Output\Command;

class Metadata implements CommandInterface
{
    public const METADATA_COPYRIGHT = 'copyright';
    public const METADATA_LOCATION  = 'location';
    public const METADATA_CREATION  = 'creation';

    /**
     * @var string[]
     */
    private array $data;

    public function __construct(string ...$metadata)
    {
        $this->data = $metadata;
    }

    public function getCommand(): array
    {
        return [
            'preserve' => $this->data
        ];
    }
}
