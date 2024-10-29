<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $photo)
 */
class ProductPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','photo','is_primary'];

    public const PHOTO_UPLOAD_PATH = 'images/uploads/product/';
    public const THUMB_PHOTO_UPLOAD_PATH = 'images/uploads/product_thumb/';
    public const PHOTO_WIDTH = 800;
    public const PHOTO_HEIGHT = 800;
    public const PHOTO_THUMB_WIDTH = 200;
    public const PHOTO_THUMB_HEIGHT = 200;

    /**
     * @param array $photo
     * @return mixed
     */

    final public function storeProductPhoto(array $photo): mixed
    {
        return self::create($photo);
    }
}
