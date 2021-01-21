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
     * Get image by range size
     *
     * @param $query
     * @param int $minBytes
     * @param int $maxBytes
     * @return Builder
     */
    public function scopeRangeSize($query, int $minBytes, int $maxBytes)
    {
        return $query->where('size', '>=', $minBytes)->where('size', '<=', $maxBytes);
    }
}
