<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory;

    protected $table = 'access_token';

    protected $fillable = ['token', 'service', 'type', 'user'];

    public static function updateOrCreateToken($token, $service, $type, $userId)
    {
        return self::updateOrCreate(
            [
                'service' => $service,
                'type' => $type,
                'user' => $userId
            ],
            [
                'token' => $token
            ]
        );
    }
}
