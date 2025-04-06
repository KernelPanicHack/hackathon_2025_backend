<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpseclib3\Math\BigInteger;

/**
 * Class Operation
 *
 * Представляет операцию в системе.
 *
 *
 * @package App\Models
 *
 * @property BigInteger $id;
 * @property string $type;
 * @property BigInteger $cost;
 * @property int $remaining_balance;
 * @property string $category;
 * @property string $ref_no;
 */
class Operation extends Model
{
    use HasFactory;


    protected $fillable = [
        'type',
        'cost',
        'remaining_balance',
        'category_id',
        'date',
        'ref_no',
    ];

    /**
     * Пользователи, связанные с данной операцией.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_operations',
            'operation_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Товары, связанные с данной операцией.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function items()
    {
        return $this->belongsToMany(
            Item::class,
            'operation_items',
            'operation_id',
            'item_id'
        )->withTimestamps();
    }

    /**
     * Категории, к которым относится данная операция.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'operation_categories',
            'operation_id',
            'category_id'
        )->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
