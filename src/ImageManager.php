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
        if(is_null($config)){
            $config = new Configuration();
        }
        $this->config = $config;
    }

}
