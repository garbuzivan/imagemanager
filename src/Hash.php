<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

class Hash
{
    static public function getHashCurl($object): string
    {
        return sha1($object);
    }
}
