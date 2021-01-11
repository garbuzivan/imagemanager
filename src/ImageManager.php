<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

use GarbuzIvan\ImageManager\Exceptions\FilterValidateUrlException;
use GarbuzIvan\ImageManager\Exceptions\MimeTypeNotAvailableException;
use GarbuzIvan\ImageManager\Exceptions\UrlNotLoadException;
use GarbuzIvan\ImageManager\Uploader\Url;
use GarbuzIvan\ImageManager\Hash;

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

    public function loadUrl(string $url): ImageManager
    {
        try {
            $this->object = (new Url($this->config))->load($url);
        } catch (FilterValidateUrlException | MimeTypeNotAvailableException | UrlNotLoadException | \Exception $e) {
            $this->error = ['error' => $e->getMessage()];
        }
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
        if (!$this->isError()) {
            // args
            $hash = (new Hash)->getHashString($this->object);
            $name = is_null($path) ?
                $hash . '.' . (new File)->getExtensionFromString($this->object, $this->config->getMimeTypes())
                :
                preg_replace('~[^0-9a-zA-Z-_\.]~isuU', '', $name);
            $path = !is_null($path) ? $path : date('/Y/m/d/H/m/');
            $title = !is_null($title) ? $title : $hash;
            $disk = $this->config->getPathDisk() . $path;
            $disk = (str_replace('//', '/', $disk));
            $url = $this->config->getPathUrl() . $path;
            // create path
            try {
                $makeDirectory = (new File)->makeDirectory($disk);
            } catch (Exceptions\MakeDirectoryException $e) {
                $this->error = ['error' => $e->getMessage()];
                return $this;
            }
            // save image to file
            if ($makeDirectory) {
                (new File)->save($disk . $name, $this->object);
                $this->file = ['name' => $name, 'disk' => $disk . $name, 'url' => $url . $name];
            }
        }
        return $this;
    }

    /**
     * @return array|false|null
     */
    public function getImage()
    {
        if (!$this->isError() && !is_null($this->file)) {
            return $this->file;
        }
        return false;
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

}
