<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 *
 * Представляет категорию операции или товара.
 *
 * @package App\Models
 *
 * @var string $name;
 * @var string $slug;
 */
class Category extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Операции, связанные с данной категорией.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function operations()
    {
        return $this->belongsToMany(
            Operation::class,
            'operation_categories',
            'category_id',
            'operation_id'
        )->withTimestamps();
    }

    public function items()
    {
        return $this->hasManyThrough(Item::class, Operation::class, 'category_id', 'id', 'id', 'item_id');
    }
}
