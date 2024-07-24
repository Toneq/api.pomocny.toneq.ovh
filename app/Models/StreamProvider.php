<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamProvider extends Model
{
    use HasFactory;

    protected $table = 'stream_providers';

    protected $fillable = [
        'user_id',
        'service',
        'user_provider_id',
        'active'
    ];
}
