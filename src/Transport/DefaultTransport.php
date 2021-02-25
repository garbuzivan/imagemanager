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

    public function getTitle(string $title, int $limit, int $page): array
    {
        return [];
    }

    public function setUse(array $images = [], int $item = 0, string $component = 'default'): void
    {
        // TODO: Implement setUse() method.
    }

    public function dropUse(array $images = [], int $item = 0, string $component = 'default'): void
    {
        // TODO: Implement dropUse() method.
    }

    public function dropImage(int $id): void
    {
        // TODO: Implement dropImage() method.
    }

    public function getUse(int $item, string $component): array
    {
        return [];
    }

    public function clear(int $item = 0, string $component = 'default'): void
    {
        // TODO: Implement clear() method.
    }
}
