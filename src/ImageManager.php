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
     * @param string|null $name
     * @param string|null $path
     * @param string|null $title
     * @return $this
     */
    public function save(string $name = null, string $path = null, string $title = null): ImageManager
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
            // save image cache size
            $this->saveResize();
            // trasport save
            $this->file['id'] = $this->config->transport()->save($this->file);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function saveResize(): ImageManager
    {
        if (!$this->isError() && !is_null($this->file)) {
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
            $this->config->transport()->updateResize($this->getImage());

        }
        return $this;
    }

    /**
     * @return array|null
     */
    public function getImage(): ?array
    {
        if (!$this->isError() && !is_null($this->file)) {
            $size = filesize($this->file['disk']);
            $imageInfo = getimagesize($this->file['disk']);
            $this->file['width'] = $imageInfo[0];
            $this->file['height'] = $imageInfo[1];
            $this->file['type'] = $imageInfo['mime'];
            $this->file['size'] = $size;
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

    public function getByHash(string $hash): ImageManager
    {
        $this->file = $this->config->transport()->getByHash($hash);
        return $this;
    }

    public function getByID(int $id): ImageManager
    {
        $this->file = $this->config->transport()->getByID($id);
        return $this;
    }

    public function getBySize(int $minBytes = 1, int $maxBytes = 100000000, int $limit = 10, int $page = 1): array
    {
        return $this->config->transport()->getBySize($minBytes, $maxBytes, $limit, $page);
    }

    public function getRange(int $minWidth, int $maxWidth, int $minHeight, int $maxHeight, int $limit = 10, int $page = 1): array
    {
        return $this->config->transport()->getRange($minWidth, $maxWidth, $minHeight, $maxHeight, $limit, $page);
    }
}
