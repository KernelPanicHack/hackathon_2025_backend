<?php

namespace App\Models;

use Brick\Math\BigInteger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Item
 *
 * Представляет товар в системе.
 *
 * Каждый товар может быть связан с множеством операций (через таблицу operation_items).
 *
 * @package App\Models
 */
class Item extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово заполнять.
     *
     * @var BigInteger $id;
     * @var string $name;
     * @var string $slug;
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Операции, в которых участвует данный товар.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function operations()
    {
        return $this->belongsToMany(
            Operation::class,
            'operation_items',
            'item_id',
            'operation_id'
        )->withTimestamps();
    }
}
