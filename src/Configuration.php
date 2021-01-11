<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

class Configuration
{
    /**
     * URL path to file
     *
     * @var string
     */
    protected $pathUrl = '/storage/images/';

    /**
     * Disk path to file
     *
     * @var string
     */
    protected $pathDisk = '/storage/images/';

    /**
     * Checking file existence by hash
     *
     * @var bool
     */
    protected $hash = false;

    /**
     * Default available types for download
     *
     * @var array
     */
    protected $mimeTypes = [
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif'
    ];

    /**
     * Default user agent to download via URL
     *
     * @var array
     */
    protected $userAgent = [
        'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11'
    ];

    /**
     * Default image size for cache
     *
     * @var array
     */
    protected $imageSize = [];

    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * The method to call on each pipe.
     *
     * @var string
     */
    protected $pipesMethod = 'handle';

    /**
     * @param $url
     */
    public function setPathUrl(string $url): void
    {
        $this->pathUrl = $url;
    }

    /**
     * @return string
     */
    public function getPathUrl(): string
    {
        return $this->pathUrl ?? '/';
    }

    /**
     * @param string $path
     */
    public function setPathDisk(string $path): void
    {
        $this->pathDisk = $path;
    }

    /**
     * @return string
     */
    public function getPathDisk(): string
    {
        return $this->pathDisk ?? '/';
    }

    /**
     * @param array $mimeTypes
     */
    public function setMimeTypes(array $mimeTypes): void
    {
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * @return array
     */
    public function getMimeTypes(): array
    {
        return $this->mimeTypes;
    }

    /**
     * @param array $userAgent
     */
    public function setUserAgent(array $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * Get User Agent - ALL list
     *
     * @return array
     */
    public function getUserAgent(): array
    {
        return $this->userAgent;
    }

    /**
     * Get random User Agent
     *
     * @return string
     */
    public function getUserAgentRandom(): string
    {
        return $this->userAgent[array_rand($this->userAgent)];
    }

    /**
     * @param array $imageSize
     */
    public function setImageSize(array $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    /**
     * @param array $imageSize
     */
    public function addImageSize(array $imageSize): void
    {
        if (count($imageSize) == 2) {
            $this->imageSize[] = $imageSize;
        }
    }

    /**
     * @return array
     */
    public function getImageSize(): array
    {
        return $this->imageSize;
    }

    /**
     * @param array $pipes
     */
    public function setPipes(array $pipes): void
    {
        $this->pipes = $pipes;
    }

    /**
     * @return array
     */
    public function getPipes(): array
    {
        return $this->pipes;
    }

    /**
     * Activation checking file existence by hash
     */
    public function hashActivation(): void
    {
        $this->hash = false;
    }

    /**
     * Deactivate checking file existence by hash
     */
    public function hashDeactivate(): void
    {
        $this->hash = false;
    }

    /**
     * Return status checking file existence by hash
     *
     * @return bool
     */
    public function getHash(): bool
    {
        return $this->hash;
    }
}
