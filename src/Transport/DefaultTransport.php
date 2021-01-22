<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Transport;


class DefaultTransport extends AbstractTransport
{
    public function existsHash(string $hash): bool
    {
        return false;
    }

    public function getByHash(string $hash): ?array
    {
        return null;
    }

    public function getByID(int $id): ?array
    {
        return [];
    }

    public function getBySize(int $minBytes, int $maxBytes, int $limit, int $page): array
    {
        return [];
    }

    public function getRange(int $minWidth, int $maxWidth, int $minHeight, int $maxHeight, int $limit, int $page): array
    {
        return [];
    }

    public function save(array $image): int
    {
        return 0;
    }

    public function update(array $image): void
    {
        // TODO: Implement updateResize() method.
    }
}
