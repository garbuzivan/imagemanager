<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

use GarbuzIvan\ImageManager\Exceptions\FilterValidateUrlException;
use GarbuzIvan\ImageManager\Exceptions\MimeTypeNotAvailableException;
use GarbuzIvan\ImageManager\Exceptions\UrlNotLoadException;
use GarbuzIvan\ImageManager\Uploader\Url;

class ImageManager
{
    /**
     * @var mixed $object
     */
    protected $object;

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
        if(is_null($config)){
            $config = new Configuration();
        }
        $this->config = $config;
    }

    public function loadUrl(string $url): ImageManager
    {
        try {
            $this->object = (new Url($this->config))->load($url);
        } catch (FilterValidateUrlException | MimeTypeNotAvailableException | UrlNotLoadException $exception) {
            dd($exception);
        } catch (\Exception $exception) {
            dd($exception);
        }
        return $this;
    }



}
