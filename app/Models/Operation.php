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
 * Каждая операция может быть связана с множеством пользователей (через таблицу user_operations)
 * и с множеством товаров (через таблицу operation_items).
 *
 * @package App\Models
 */
class Operation extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово заполнять.
     *
     * @var BigInteger $id;
     * @var string $type;
     * @var int $cost;
     * @var int $remaining_balance;
     * @var string $category;
     * @var string $category;
     * @var BigInteger $ref_no;
     *
     */
    protected $fillable = [
        'type',
        'cost',
        'remaining_balance',
        'category',
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
}
