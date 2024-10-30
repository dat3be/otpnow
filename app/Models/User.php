<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telegram_userid',
        'telegram_username',
        'balance',
        'aff_code',
        'aff_balance',
        'ref_by',
        'phone_num',
    ];

    // Sự kiện creating để tự động tạo aff_code
    protected static function booted()
    {
        static::creating(function ($user) {
            $user->aff_code = self::generateUniqueAffCode();
        });
    }

    // Hàm tạo mã aff_code ngẫu nhiên và đảm bảo unique
    public static function generateUniqueAffCode()
    {
        do {
            $code = Str::upper(Str::random(6)); // Tạo mã 6 ký tự, gồm chữ hoa và số
        } while (self::where('aff_code', $code)->exists());

        return $code;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
}
