<?php

declare(strict_types=1);

/**
 * @see https://github.com/robopuff/tinypng for the canonical source repository
 * @license https://github.com/robopuff/tinypng/blob/master/LICENSE New BSD-3 License
 */

namespace TinyPng\Output\Command;

class Resize implements CommandInterface
{
    public const METHOD_SCALE = 'scale';
    public const METHOD_FIT   = 'fit';
    public const METHOD_COVER = 'cover';
    public const METHOD_THUMB = 'thumb';

    private array $data;

    /**
     * Resize image
     * Use the API to create resized versions of your uploaded images.
     * By letting the API handle resizing you avoid having to write
     * such code yourself and you will only have to upload your image once.
     * The resized images will be optimally compressed with a nice and crisp appearance.
     * @link https://tinypng.com/developers/reference#request-options
     * @param string $method
     * @param int|null $width
     * @param int|null $height
     */
    public function __construct(string $method, ?int $width = null, ?int $height = null)
    {
        $this->data = [
            'method' => $method
        ];

        if (null != $width) {
            $this->data['width'] = $width;
        }

        if (null != $height) {
            $this->data['height'] = $height;
        }
    }

    public function getCommand(): array
    {
        return [
            'resize' => $this->data
        ];
    }
}
