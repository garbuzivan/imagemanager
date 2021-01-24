<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageUse extends Model
{
    use HasFactory;

    protected $table = 'image_manager_use';

    /**
     * @var array<string> $fillable
     */
    protected $fillable = [
        'image_id',
        'item_id',
        'component',
    ];

    /**
     * Image by ID
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getImage()
    {
        return $this->hasOne('\GarbuzIvan\ImageManager\Models\Images', 'id', 'image_id');
    }
}
