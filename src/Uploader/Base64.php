<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Uploader;

use GarbuzIvan\ImageManager\ExceptionCode;
use GarbuzIvan\ImageManager\Exceptions\FilterValidateBase64ImageException;
use GarbuzIvan\ImageManager\Exceptions\MimeTypeNotAvailableException;

class Base64 extends AbstractUploader
{
    /**
     * @param string $image
     * @return string
     * @throws FilterValidateBase64ImageException
     * @throws MimeTypeNotAvailableException
     */
    public function load(string $image): string
    {
        // base64 string
        $identifier = 'data:image/';
        if (mb_substr($image, 0, mb_strlen($identifier)) != $identifier) {
            throw new FilterValidateBase64ImageException(ExceptionCode::$FILTER_VALIDATE_BASE64_IMAGE);
        }
        // content type
        $contentType = str_replace('data:', null, explode(';', $image)[0]);
        if (!in_array($contentType, $this->config->getMimeTypes())) {
            throw new MimeTypeNotAvailableException(ExceptionCode::$MIME_TYPE_NOT_AVAILABLE);
        }
        // get image
        $replace = substr($image, 0, strpos($image, ',') + 1);
        $image = str_replace([$replace, ' '], ['', '+'], $image);
        $image = base64_decode($image);

        return $image;
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
        } catch (MimeTypeNotAvailableException|FilterValidateBase64ImageException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
