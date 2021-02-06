<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

class ImageManager
{
    /**
     * @var Configuration $config
     */
    protected $config;

    /**
     * Configuration constructor.
     * @param Configuration|null $config
     */
    public function __construct(Configuration $config = null)
    {
        if (is_null($config)) {
            $config = new Configuration();
        }
        $this->config = $config;
    }

    /**
     * @param string $object
     * @return ImageStatus
     */
    public function load(string $object): ImageStatus
    {
        $image = null;
        foreach ($this->config->getUploaders() as $uploadClass){
            $image = (new $uploadClass($this->config))->pipe($object);
            if(!is_null($image)){
                return new ImageStatus($image, $this->config);
            }
        }
        return new ImageStatus(null, $this->config);
    }

    /**
     * Search image by hash
     *
     * @param string $hash
     * @return $this
     */
    public function getByHash(string $hash): ImageManager
    {
        $this->file = $this->config->transport()->getByHash($hash);
        return $this;
    }

    /**
     * Search image by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getByID(int $id): ?array
    {
        return $this->config->transport()->getByID($id);
    }

    /**
     * Search images by min\max filesize
     *
     * @param int|null $minBytes
     * @param int|null $maxBytes
     * @param int $limit
     * @param int $page
     * @return array
     */
    public function getByFileSize(int $minBytes = null, int $maxBytes = null, int $limit = 10, int $page = 1): array
    {
        return $this->config->transport()->getBySize($minBytes, $maxBytes, $limit, $page);
    }

    /**
     * Search images by min\max width\height
     *
     * @param int|null $minWidth
     * @param int|null $maxWidth
     * @param int|null $minHeight
     * @param int|null $maxHeight
     * @param int $limit
     * @param int $page
     * @return array
     */
    public function getBySize(int $minWidth = null, int $maxWidth = null, int $minHeight = null, int $maxHeight = null, int $limit = 10, int $page = 1): array
    {
        return $this->config->transport()->getRange($minWidth, $maxWidth, $minHeight, $maxHeight, $limit, $page);
    }

    /**
     * Search for an image by a title
     *
     * @param string|null $title
     * @param int $limit
     * @param int $page
     * @return array
     */
    public function getTitle(string $title = null, int $limit = 10, int $page = 1): array
    {
        return $this->config->transport()->getTitle($title, $limit, $page);
    }

    /**
     * Include use image in component item
     *
     * @param array $images
     * @param int $item
     * @param string $component
     * @return $this
     */
    public function setUse(array $images = [], int $item = 0, string $component = 'default'): ImageManager
    {
        $this->config->transport()->setUse($images, $item, $component);
        return $this;
    }

    /**
     * Drop use images in component item
     *
     * @param array $images
     * @param int $item
     * @param string $component
     * @return $this
     */
    public function dropUse(array $images = [], int $item = 0, string $component = 'default'): ImageManager
    {
        $this->config->transport()->dropUse($images, $item, $component);
        return $this;
    }

    /**
     * Get list images use in component item
     *
     * @param int $item
     * @param string $component
     * @return array
     */
    public function getUse(int $item = 0, string $component = 'default'): array
    {
        return $this->config->transport()->getUse($item, $component);
    }
}
