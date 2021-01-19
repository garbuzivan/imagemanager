<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Laravel;

use GarbuzIvan\ImageManager\Configuration;

class Config extends Configuration
{
    protected $configFile = 'imagemanager';
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
        $this->load();
    }

    /**
     * @return $this|Configuration
     */
    public function load(): Configuration
    {
        $this->setPathDisk(config($this->configFile . '.disks.disk'));
        $this->setPathUrl(config($this->configFile . '.disks.url'));
        $this->setPath(config($this->configFile . '.disks.path'));
        $mimeTypes = config($this->configFile . '.mime_types');
        if(is_array($mimeTypes)){
            $this->setMimeTypes($mimeTypes);
        }
        $userAgent = config($this->configFile . '.useragent');
        if(is_array($userAgent)){
            $this->setUserAgent($userAgent);
        }
        $imageSize = config($this->configFile . '.cache');
        if(is_array($imageSize)){
            $this->setImageSize($imageSize);
        }
        $pipes = config($this->configFile . '.pipes');
        if(is_array($pipes)){
            $this->setPipes($pipes);
        }
        return $this;
    }
}
