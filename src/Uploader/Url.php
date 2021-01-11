<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Uploader;

use GarbuzIvan\ImageManager\Exception\UrlException;
use GarbuzIvan\ImageManager\Exception\MimeTypeNotAvailableException;
use GarbuzIvan\ImageManager\Exception\UrlNotLoadException;
use GarbuzIvan\ImageManager\ExceptionCode;

class Url extends AbstractUploader
{
    /**
     * @param string $url
     * @return string
     * @throws UrlException
     * @throws MimeTypeNotAvailableException
     * @throws UrlNotLoadException
     */
    public function load(string $url): string
    {
        // Exception FILTER VALIDATE URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new UrlException(ExceptionCode::$FILTER_VALIDATE_URL);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->config->getUserAgentRandom());
        $output = curl_exec($ch);

        // Exception URL NOT LOAD
        if ($output == false) {
            throw new UrlNotLoadException(ExceptionCode::$URL_NOT_LOAD);
        }

        // Exception MIME TYPE NOT AVAILABLE
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        if (!in_array($contentType, $this->config->getMimeTypes())) {
            throw new MimeTypeNotAvailableException(ExceptionCode::$MIME_TYPE_NOT_AVAILABLE);
        }

        return $output;
    }
}
