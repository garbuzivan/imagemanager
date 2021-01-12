<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Uploader;

use GarbuzIvan\ImageManager\ExceptionCode;
use GarbuzIvan\ImageManager\Exceptions\FileNotExistsException;
use GarbuzIvan\ImageManager\Exceptions\MimeTypeNotAvailableException;

class File extends AbstractUploader
{
    /**
     * @param string $file
     * @return string
     * @throws FileNotExistsException
     * @throws MimeTypeNotAvailableException
     */
    public function load(string $file): string
    {
        if (!file_exists($file)) {
            throw new FileNotExistsException(ExceptionCode::$FILE_NOT_EXISTS);
        }
        // content type
        $contentType = mime_content_type($file);
        if (!in_array($contentType, $this->config->getMimeTypes())) {
            throw new MimeTypeNotAvailableException(ExceptionCode::$MIME_TYPE_NOT_AVAILABLE);
        }
        return file_get_contents($file);
    }
}
