<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class EventService
{
    public function message($user){   
        $data = [
            'event' => 'message',
            'data' => [
                
            ],
        ];
        
        Redis::publish(`user:{$user->id}:event`, json_encode($data));
    }

    public function follow($user){   
        $data = [
            'event' => 'follow',
            'data' => [
                
            ],
        ];
        
        Redis::publish(`user:{$user->id}:event`, json_encode($data));
    }

    public function raid($user){   
        $data = [
            'event' => 'raid',
            'data' => [
                
            ],
        ];
        
        Redis::publish(`user:{$user->id}:event`, json_encode($data));
    }

    public function subscription($user){   
        $data = [
            'event' => 'subscription',
            'data' => [
                
            ],
        ];
        
        Redis::publish(`user:{$user->id}:event`, json_encode($data));
    }
}
