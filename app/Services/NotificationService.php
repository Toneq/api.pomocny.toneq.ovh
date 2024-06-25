<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class NotificationService
{
    public function sendNotification($user){   
        $data = [
            'event' => 'notification',
            'data' => [
                
            ],
        ];
        
        Redis::publish(`user:{$user->id}:event`, json_encode($data));
    }

    public function sendWinkNotification($user){   
        $data = [
            'event' => 'wink',
            'data' => [
                
            ],
        ];
        
        Redis::publish(`user:{$user->id}:event`, json_encode($data));
    }

    public function sendMessageNotification($user){   
        $data = [
            'event' => 'message',
            'data' => [
                
            ],
        ];
        
        Redis::publish(`user:{$user->id}:event`, json_encode($data));
    }
}
