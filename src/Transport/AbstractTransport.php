<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Transport;

abstract class AbstractTransport
{
    abstract public function getByHash(string $hash): array;
    abstract public function getByID(int $hash): array;
    abstract public function getBySize(int $size): array;
    abstract public function getRange(int $minWidth, int $maxWidth, int $minHeight, int $maxHeight): array;
    abstract public function save(array $image): int;
}
