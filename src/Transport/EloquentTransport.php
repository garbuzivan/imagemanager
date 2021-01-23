<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Transport;

use ErrorException;
use GarbuzIvan\ImageManager\Models\Images;
use Illuminate\Database\Eloquent\Collection;

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

    public function getBySize(int $minBytes = null, int $maxBytes = null, int $limit = 10, int $page = 1): array
    {
        try {
            $images = Images::rangeFileSize($minBytes, $maxBytes)->limit($limit)->offset($limit * $page - $limit)->get();
        } catch (ErrorException $e) {
            return [];
        }
        return $this->resultListToArray($images);
    }

    public function getRange(int $minWidth = null, int $maxWidth = null, int $minHeight = null, int $maxHeight = null, int $limit = 10, int $page = 1): array
    {
        try {
            $images = Images::rangeSize($minWidth, $maxWidth, $minHeight, $maxHeight)->limit($limit)->offset($limit * $page - $limit)->get();
        } catch (ErrorException $e) {
            return [];
        }
        return $this->resultListToArray($images);
    }

    public function getTitle(int $title = null, int $limit = 10, int $page = 1): array
    {
        try {
            $images = Images::title($title)->limit($limit)->offset($limit * $page - $limit)->get();
        } catch (ErrorException $e) {
            return [];
        }
        return $this->resultListToArray($images);
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
        return Images::insertGetId($insert);
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
            'width' => $image->width,
            'height' => $image->height,
            'type' => $image->type,
            'size' => $image->size,
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

    /**
     * Update image info in DB
     *
     * @param array $image
     */
    public function update(array $image): void
    {
        if (isset($image['id']) && $image['id'] > 0) {
            $update = [
                'width' => $image['width'] ?? null,
                'height' => $image['height'] ?? null,
                'type' => $image['type'] ?? null,
                'size' => $image['size'] ?? null,
            ];
            $cache = $this->getImageCacheFromDb($image);
            if (!is_null($cache)) {
                $cache = json_encode($cache);
            }
            $update['cache'] = $cache;
            if (isset($image['title'])) {
                $update['title'] = $image['title'];
            }
            Images::where('id', $image['id'])->update($update);
        }
    }

    /**
     * Preparing an array of cached image sizes to store paths in the database
     *
     * @param array $image
     * @return array|null
     */
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

    public function resultListToArray(Collection $list): array
    {
        $images = [];
        foreach ($list as $image) {
            $images[$image->id] = $this->imageToArray($image);
        }
        return $images;
    }
}
