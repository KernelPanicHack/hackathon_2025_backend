<?php

namespace App\Models;

use Brick\Math\BigInteger;
use App\Http\Requests\RegisterRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var BigInteger $id ;
     * @var string $name ;
     * @var string $email ;
     * @var string $email_verified_at ;
     * @var string $avatar ;
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'provider_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function operations()
    {
        return $this->belongsToMany(
            Operation::class,
            'user_operations',
            'user_id',
            'operation_id'
        )->withTimestamps();
    }

    public static function makeUser(RegisterRequest $request): User
    {
        $user = new User();
        $user->email = $request->post('email');
        $user->password = Hash::make($request->validated('password'));
        $user->name = mb_split('@', $request->post('email'))[0];
        $user->save();

        return $user;
    }
}
