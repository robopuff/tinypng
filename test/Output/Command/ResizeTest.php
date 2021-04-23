<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPngTest\Output\Command;

use PHPUnit\Framework\TestCase;
use TinyPng\Output\Command\Resize;

class ResizeTest extends TestCase
{
    public function testCommandData()
    {
        $resize = new Resize(Resize::METHOD_THUMB, 120, 420);
        $this->assertEquals([
            'resize' => [
                'method' => 'thumb',
                'width' => 120,
                'height' => 420
            ]
        ], $resize->getCommand());
    }

    public function testCommandDataWithOnlyWidth()
    {
        $resize = new Resize(Resize::METHOD_THUMB, 120);
        $this->assertEquals([
            'resize' => [
                'method' => 'thumb',
                'width' => 120
            ]
        ], $resize->getCommand());
    }

    public function testCommandDataWithOnlyHeight()
    {
        $resize = new Resize(Resize::METHOD_THUMB, null, 120);
        $this->assertEquals([
            'resize' => [
                'method' => 'thumb',
                'height' => 120
            ]
        ], $resize->getCommand());
    }
}
