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
        $image = Images::where('hash', $hash)->first();
        if (is_null($image)) {
            return null;
        } else {
            return $this->imageToArray($image);
        }
    }

    public function getByID(int $id): ?array
    {
        $image = Images::where('id', $id)->first();
        if (is_null($image)) {
            return null;
        } else {
            return $this->imageToArray($image);
        }
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
        $cache = $this->getImageCacheFromDb($image);
        if (!is_null($cache)) {
            $cache = json_encode($cache);
        }
        $insert['cache'] = $cache;
        return Images::create($insert)->id;
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
            'id' => $image->id,
            'hash' => $image->hash,
            'title' => $image->title,
            'name' => $image->name,
            'path' => $image->path,
            'cache' => [],
        ];
        $object['disk'] = $this->config->getPathDisk() . $image->path;
        $object['disk'] = (str_replace('//', '/', $object['disk']));
        $object['url'] = $this->config->getPathUrl() . $image->path;
        if (!is_null($image->cache)) {
            $cache = json_decode($image->cache, true);
            if (is_array($cache)) {
                foreach ($cache as $keyImg => $img) {
                    if (isset($img['path']) && !is_null($img['path'])) {
                        $imgCache = [];
                        $imgCache['path'] = $img['path'];
                        $imgCache['disk'] = $this->config->getPathDisk() . $img['path'];
                        $imgCache['disk'] = (str_replace('//', '/', $imgCache['disk']));
                        $imgCache['url'] = $this->config->getPathUrl() . $img['path'];
                        if (file_exists($imgCache['disk'])) {
                            $object['cache'][$keyImg] = $imgCache;
                        }
                    }
                }
            }
        }
        return $object;
    }

    public function updateResize(array $image): void
    {
        if ($image['id'] > 0) {
            $cache = $this->getImageCacheFromDb($image);
            if (!is_null($cache)) {
                $cache = json_encode($cache);
            }
            $update['cache'] = $cache;
            Images::where('id', $image['id'])->update($update);
        }
    }

    public function getImageCacheFromDb(array $image): ?array
    {
        $images = null;
        if (isset($image['cache']) && is_array($image['cache'])) {
            foreach ($image['cache'] as $keyImg => $img) {
                $images[$keyImg]['path'] = $img['path'];
            }
        }
        return $images;
    }
}
