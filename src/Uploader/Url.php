<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Uploader;

use GarbuzIvan\ImageManager\Exceptions\FilterValidateUrlException;
use GarbuzIvan\ImageManager\Exceptions\MimeTypeNotAvailableException;
use GarbuzIvan\ImageManager\Exceptions\UrlNotLoadException;
use GarbuzIvan\ImageManager\ExceptionCode;

class Url extends AbstractUploader
{
    /**
     * @param string $url
     * @return string
     * @throws FilterValidateUrlException
     * @throws MimeTypeNotAvailableException
     * @throws UrlNotLoadException
     */
    public function load(string $url): string
    {
        // Exception FILTER VALIDATE URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new FilterValidateUrlException(ExceptionCode::$FILTER_VALIDATE_URL);
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
        $notFound = true;
        foreach ($this->config->getMimeTypes() as $mimeType) {
            if (strripos($contentType, $mimeType) !== false ){
                $notFound = false;
                break;
            }
        }
        if ($notFound) {
            throw new MimeTypeNotAvailableException(ExceptionCode::$MIME_TYPE_NOT_AVAILABLE);
        }

        return $output;
    }

    /**
     * The load methods are called to load through the method ImageManager->load()
     *
     * @param string $object
     * @return string
     */
    public function pipe(string $object): ?string
    {
        try {
            return $this->load($object);
        } catch (FilterValidateUrlException | MimeTypeNotAvailableException | UrlNotLoadException | \Exception $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
