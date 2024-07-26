<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KickVerifyCode extends Model
{
    use HasFactory;

    protected $table = 'kick_verify_code';

    protected $fillable = ['user_id', 'service', 'code'];
}
