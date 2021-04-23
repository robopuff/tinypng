<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPngTest\Output\Command;

use PHPUnit\Framework\TestCase;
use TinyPng\Output\Command\Metadata;

class MetadataTest extends TestCase
{
    public function testCommandData()
    {
        $metadata = new Metadata(Metadata::METADATA_CREATION, Metadata::METADATA_COPYRIGHT);
        $this->assertEquals([
            'preserve' => ['creation', 'copyright']
        ], $metadata->getCommand());
    }
}
