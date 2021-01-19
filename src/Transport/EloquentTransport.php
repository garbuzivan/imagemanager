<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Transport;


class EloquentTransport extends AbstractTransport
{
    public function existsHash(string $hash): bool
    {
        // TODO: Implement existsHash() method.
    }

    public function getByHash(string $hash): array
    {
        // TODO: Implement getByHash() method.
    }

    public function getByID(int $hash): array
    {
        // TODO: Implement getByID() method.
    }

    public function getBySize(int $bytes): array
    {
        // TODO: Implement getBySize() method.
    }

    public function getRange(int $minWidth, int $maxWidth, int $minHeight, int $maxHeight): array
    {
        // TODO: Implement getRange() method.
    }

    public function save(array $image): int
    {
        // TODO: Implement save() method.
    }
}
