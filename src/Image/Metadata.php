<?php

namespace TinyPng\Image;

class Metadata
{
    /**
     * @var array
     */
    private $metadata;

    /**
     * ImageMeta constructor.
     * @param array $metadata
     */
    public function __construct(array $metadata = [])
    {
        $this->metadata = $metadata;
    }

    /**
     * Get information from metadata
     * @param string $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        return $this->metadata[$name] ?? $default;
    }

    /**
     * Get image width
     * @return int
     */
    public function getWidth(): int
    {
        return (int) $this->get('image-width', 0);
    }

    /**
     * Get image height
     * @return int
     */
    public function getHeight(): int
    {
        return (int) $this->get('image-height', 0);
    }

    /**
     * Get location
     * @return null|string
     */
    public function getLocation(): ?string
    {
        return $this->get('location');
    }

    /**
     * Get image size
     * @return int
     */
    public function getSize(): int
    {
        return (int) $this->get('content-length', 0);
    }

    /**
     * Get content type
     * @return null|string
     */
    public function getContentType(): ?string
    {
        return $this->get('content-type');
    }
}
