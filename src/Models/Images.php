<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;

    protected $table = 'image_manager';

    /**
     * @var array<string> $fillable
     */
    protected $fillable = [
        'hash',
        'name',
        'path',
    ];

    /**
     * Get uses image
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getUses()
    {
        return $this->hasMany('GarbuzIvan\ImageManager\Models\ImagesUse', 'image_id', 'id');
    }

    /**
     * Get image by range filesize
     *
     * @param $query
     * @param int|null $minBytes
     * @param int|null $maxBytes
     * @return Builder
     */
    public function scopeRangeFileSize($query, int $minBytes = null, int $maxBytes = null)
    {
        if (!is_null($minBytes)) {
            $query->where('size', '>=', $minBytes);
        }
        if (!is_null($maxBytes)) {
            $query->where('size', '<=', $maxBytes);
        }
        return $query;
    }

    /**
     * Get image by range size
     *
     * @param $query
     * @param int|null $minWidth
     * @param int|null $maxWidth
     * @param int|null $minHeight
     * @param int|null $maxHeight
     * @return Builder
     */
    public function scopeRangeSize($query, int $minWidth = null, int $maxWidth = null, int $minHeight = null, int $maxHeight = null)
    {
        if (!is_null($minWidth)) {
            $query->where('width', '>=', $minWidth);
        }
        if (!is_null($maxWidth)) {
            $query->where('width', '<=', $maxWidth);
        }
        if (!is_null($minHeight)) {
            $query->where('height', '>=', $minHeight);
        }
        if (!is_null($maxHeight)) {
            $query->where('height', '<=', $maxHeight);
        }
        return $query;
    }

    /**
     * Get image by title
     *
     * @param $query
     * @param string|null $title
     * @return mixed
     */
    public function scopeTitle($query, string $title = null)
    {
        if (!is_null($title)) {
            $query->where('title', 'LIKE', $title);
        }
        return $query;
    }
}
