<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

class Hash
{
    /**
     * @param $string
     * @return string
     */
    static public function getHashString($string): string
    {
        return sha1($string);
    }

    /**
     * @param $filePath
     * @return string
     */
    static public function getHashFile($filePath): string
    {
        return sha1_file($filePath);
    }
}
