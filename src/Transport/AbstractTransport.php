<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Transport;

abstract class AbstractTransport
{
    /**
     * Search image by hash
     *
     * @param string $hash
     * @return array
     */
    abstract public function getByHash(string $hash): array;

    /**
     * Search image by id
     *
     * @param int $hash
     * @return array
     */
    abstract public function getByID(int $hash): array;

    /**
     * Search image by filesize (bytes)
     *
     * @param int $bytes - bytes
     * @return array
     */
    abstract public function getBySize(int $bytes): array;

    /**
     * Search for an image by a range of width and height
     *
     * @param int $minWidth
     * @param int $maxWidth
     * @param int $minHeight
     * @param int $maxHeight
     * @return array
     */
    abstract public function getRange(int $minWidth, int $maxWidth, int $minHeight, int $maxHeight): array;

    /**
     * Save image to DB
     *
     * @param array $image
     * @return int - ID image
     */
    abstract public function save(array $image): int;
}
