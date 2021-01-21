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

    public function getBySize(int $minBytes, int $maxBytes, int $limit, int $page): array
    {
        try {
            $images = Images::rangeSize()->get();
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
        // is not hash
        if (!isset($image['hash']) || is_null($image['hash'])) {
            return 0;
            // is not path
        } elseif (!isset($image['path']) || is_null($image['path'])) {
            return 0;
            // is not name
        } elseif (!isset($image['name']) || is_null($image['name'])) {
            return 0;
        }

        $insert = [
            'hash' => $image['hash'],
            'title' => $image['title'] ?? null,
            'name' => $image['name'],
            'path' => $image['path'],
            'width' => $image['width'] ?? null,
            'height' => $image['height'] ?? null,
            'type' => $image['type'] ?? null,
            'size' => $image['size'] ?? null,
        ];
        $cache = [];
        if (isset($image['cache']) && is_array($image['cache'])) {
            foreach ($image['cache'] as $keyImg => $img) {
                $cache[$keyImg] = $img['path'];
            }
        }
        $insert['cache'] = json_encode($cache);
        return Images::create($cache)->id;
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
        $cache = json_decode($image->cache, true);
        if (is_array($cache)) {
            foreach ($cache as $keyImg => $img) {
                $imgCache['path'] = $img['path'];
                $imgCache['disk'] = $this->config->getPathDisk() . $img['path'];
                $imgCache['disk'] = (str_replace('//', '/', $imgCache['disk']));;
                $imgCache['url'] = $this->config->getPathUrl() . $img['path'];
                $object['cache'][$keyImg] = $imgCache;
            }
        }
        return $object;
    }
}
