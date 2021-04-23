<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Output;

use Psr\Http\Message\ResponseInterface;
use TinyPng\Output\Command\CommandInterface;
use TinyPng\Output\Storage\StorageInterface;

interface OutputInterface
{
    public function getResponse(): ResponseInterface;
    public function getSize(): int;
    public function getType(): string;
    public function setCommands(CommandInterface ...$commands): void;
    public function store(StorageInterface $storage): void;
}
