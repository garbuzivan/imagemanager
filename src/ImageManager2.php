<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

use GarbuzIvan\ImageManager\Exceptions\FilterValidateUrlException;
use GarbuzIvan\ImageManager\Exceptions\MimeTypeNotAvailableException;
use GarbuzIvan\ImageManager\Exceptions\UrlNotLoadException;
use Intervention\Image\ImageManager as Intervention;

class ImageManager
{
    /**
     * @var string $object
     */
    protected $object;

    /**
     * Saved image and images other size in cache
     *
     * @var array $file
     */
    protected $file = null;

    /**
     * File hash
     *
     * @var null
     */
    protected $hash = null;

    /**
     * @var array|null $error
     */
    protected $error = null;

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

    public function load(string $object): ImageStatus
    {

    }

    /**
     * Primary image processing after loading
     */
    public function afterLoad(): void
    {
        if (!$this->isError()) {
            $this->hash = (new Hash)->getHashString($this->object);
            $this->file = $this->config->transport()->getByHash($this->hash);
        } else {
            $this->hash = null;
        }
    }

    /**
     * @param string $url
     * @return $this
     */
    public function loadUrl(string $url): ImageManager
    {
        try {
            $this->object = (new \GarbuzIvan\ImageManager\Uploader\Url($this->config))->load($url);
        } catch (FilterValidateUrlException | MimeTypeNotAvailableException | UrlNotLoadException | \Exception $e) {
            $this->error = ['error' => $e->getMessage()];
        }
        $this->afterLoad();
        return $this;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function loadFile(string $file): ImageManager
    {
        try {
            $this->object = (new \GarbuzIvan\ImageManager\Uploader\File($this->config))->load($file);
        } catch (\Exception $e) {
            $this->error = ['error' => $e->getMessage()];
        }
        $this->afterLoad();
        return $this;
    }

    /**
     * @param string $base64
     * @return $this
     */
    public function loadBase64(string $base64): ImageManager
    {
        try {
            $this->object = (new \GarbuzIvan\ImageManager\Uploader\Base64($this->config))->load($base64);
        } catch (\Exception $e) {
            $this->error = ['error' => $e->getMessage()];
        }
        $this->afterLoad();
        return $this;
    }

    /**
     * @param string|null $title
     * @param string|null $name
     * @param string|null $path
     * @return $this
     */
    public function save(string $title = null, string $name = null, string $path = null): ImageManager
    {
        if ($this->file == null && !$this->isError()) {
            // args
            $extension = '.' . (new File)->getExtensionFromString($this->object, $this->config->getMimeTypes());
            $hash = (new Hash)->getHashString($this->object);
            $name = $this->getNameImage($name, $hash, $extension);
            $pathDate = !is_null($path) ? $path : date('/Y/m/d/H/m/');
            $title = !is_null($title) ? $title : null;
            $disk = $this->config->getPathDisk() . $pathDate;
            $disk = (str_replace('//', '/', $disk));
            $path = (str_replace('//', '/', $pathDate . $name));
            $url = $this->config->getPathUrl() . $pathDate . $name;
            // create path
            try {
                $makeDirectory = (new File)->makeDirectory($disk);
            } catch (Exceptions\MakeDirectoryException $e) {
                $this->error = ['error' => $e->getMessage()];
                return $this;
            }
            // save image to file
            if ($makeDirectory) {
                $disk .= $name;
                (new File)->save($disk, $this->object);
                $this->file = [
                    'hash' => $hash,
                    'title' => $title,
                    'name' => $name,
                    'disk' => $disk,
                    'path' => $path,
                    'url' => $url,
                    'cache' => [],
                ];
            }
            // Start Pipes
            $this->pipes();
            // save image cache size
            $this->saveResize();
            // trasport save
            $this->file['id'] = $this->config->transport()->save($this->getImage());
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function saveResize(): ImageManager
    {
        if ($this->isLoadImage()) {
            // drop old cache image
            if (isset($this->file['cache']) && is_array($this->file['cache'])) {
                foreach ($this->file['cache'] as $image) {
                    if (file_exists($image['disk'])) {
                        unlink($image['disk']);
                    }
                }
            }
            // create cache image
            foreach ($this->config->getImageSize() as $size) {
                $width = $size[0] > 0 ? intval($size[0]) : null;
                $height = $size[1] > 0 ? intval($size[1]) : null;
                $key = $width . 'x' . $height;
                $name = $key . '-' . $this->file['name'];
                $disk = str_replace($this->file['name'], $name, $this->file['disk']);
                $url = str_replace($this->file['name'], $name, $this->file['url']);
                $path = str_replace($this->file['name'], $name, $this->file['path']);
                (new Intervention())->make($this->file['disk'])
                    ->fit($width, $height)
                    ->save($disk);
                if (file_exists($disk)) {
                    $this->file['cache'][$key] = [
                        'disk' => $disk,
                        'url' => $url,
                        'path' => $path,
                    ];
                }
            }
            // Update cache size image info in db
            $this->config->transport()->update($this->getImage());

        }
        return $this;
    }


    /**
     * @param array|null $file
     * @return $this
     */
    public function update(array $file = null): ImageManager
    {
        if (is_array($file) && count($file) > 0) {
            $this->config->transport()->update($file);
        } elseif ($this->isLoadImage()) {
            $this->config->transport()->update($this->getImage());
        }
        return $this;
    }

    /**
     * Method update info all images from array
     *
     * @param array $images example [['id'=>1], ['id'=>11], ['id'=>31]]
     * @return $this
     */
    public function updateArrayByID(array $images): ImageManager
    {
        foreach ($images as $image) {
            if (!isset($image['id'])) {
                continue;
            }
            $this->getByID($image['id']);
            if ($this->isLoadImage()) {
                $this->config->transport()->update($this->getImage());
            }
        }
        return $this;
    }

    /**
     * @return array|null
     */
    public function getImage(): ?array
    {
        if ($this->isLoadImage()) {
            if (file_exists($this->file['disk'])) {
                $size = filesize($this->file['disk']);
                $imageInfo = getimagesize($this->file['disk']);
                $this->file['width'] = $imageInfo[0];
                $this->file['height'] = $imageInfo[1];
                $this->file['type'] = $imageInfo['mime'];
                $this->file['size'] = $size;
            } else {
                $this->file['width'] = 0;
                $this->file['height'] = 0;
                $this->file['type'] = 0;
                $this->file['size'] = 0;
            }
            return $this->file;
        }
        return null;
    }

    /**
     * @return $this
     */
    public function pipes(): ImageManager
    {
        foreach ($this->config->getPipes() as $className) {
            $class = new $className;
            call_user_func($class, $this->file);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return !is_null($this->error);
    }

    /**
     * @return array|bool
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string|null $name
     * @param string $hash
     * @param string $extension
     * @return string
     */
    public function getNameImage(?string $name, string $hash, string $extension): string
    {
        $name = preg_replace('~[^0-9a-zA-Z-_\.]~isuU', '', $name);
        $name = mb_strlen($name) == 0 ? $hash : $name;
        $name = str_ireplace($extension, '', $name) . $extension;
        return $name;
    }

    /**
     * Test load image
     *
     * @return bool
     */
    public function isLoadImage(): bool
    {
        return !$this->isError() && !is_null($this->file);
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
     * @return $this
     */
    public function getByID(int $id): ImageManager
    {
        $this->file = $this->config->transport()->getByID($id);
        return $this;
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
     * Delete image server and use information
     *
     * @param array|null $images
     * @return $this
     */
    public function drop(array $images = null): ImageManager
    {
        if (is_null($images)) {
            if ($this->isLoadImage()) {
                $images[] = $this->file;
            } else {
                $images = [];
            }
        }
        $dropList = [];
        foreach ($images as $image) {
            if (!isset($image['id'])) {
                continue;
            }
            $dropList[] = $image['id'];
            // drop file riginal
            if (isset($image['disk']) && file_exists($image['disk'])) {
                unlink($image['disk']);
            }
            // drop file cache
            if (isset($image['cache']) && is_array($image['cache'])) {
                foreach ($image['cache'] as $img) {
                    if (file_exists($img['disk'])) {
                        unlink($img['disk']);
                    }
                }
            }
        }
        // drop in DB
        $this->config->transport()->dropImage($dropList);
        return $this;
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
