<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Type
 *
 * Представляет тип, который может быть использован для классификации операций или других сущностей.
 *
 * @package App\Models
 *
 * @var string $name;
 * @var string $slug;
 */
class Type extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'slug',
    ];
}
