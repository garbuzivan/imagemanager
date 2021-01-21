<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Transport;

use ErrorException;
use GarbuzIvan\ImageManager\Models\Images;

class EloquentTransport extends AbstractTransport
{
    public function existsHash(string $hash): bool
    {
        if (Images::where('hash', $hash)->exists()) {
            return true;
        } else {
            return false;
        }
    }

    public function getByHash(string $hash): ?array
    {
        try {
            $image = Images::where('hash', $hash)->firstOrFail();
        } catch (ErrorException $e) {
            return null;
        }
        return $this->imageToArray($image);
    }

    public function getByID(int $id): ?array
    {
        try {
            $image = Images::where('id', $id)->firstOrFail();
        } catch (ErrorException $e) {
            return [];
        }
        return $this->imageToArray($image);
    }

    public function getBySize(int $bytes, int $limit, int $page): array
    {
        try {
            $images = Images::where('id', $id)->get();
        } catch (ErrorException $e) {
            return [];
        }
    }

    public function getRange(int $minWidth, int $maxWidth, int $minHeight, int $maxHeight, int $limit, int $page): array
    {
        // TODO: Implement getRange() method.
    }

    public function save(array $image): int
    {
        // TODO: Implement save() method.
    }

    /**
     * Object to array image
     *
     * @param $image
     * @return array
     */
    public function imageToArray($image): array
    {
        $object = [
            'hash' => $image->hash,
            'title' => $image->title,
            'name' => $image->name,
            'path' => $image->path,
            'cache' => $image->cache,
        ];
        $object['disk'] = $this->config->getPathDisk() . $image->path;
        $object['disk'] = (str_replace('//', '/', $object['disk']));
        $object['url'] = $this->config->getPathUrl() . $image->path;
        return $object;
    }
}
