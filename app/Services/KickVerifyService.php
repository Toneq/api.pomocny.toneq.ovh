<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class KickVerifyService
{
    public function __construct($event, $code, $data)
    {
        $this->send($event, $code, $data);
    }

    private function send($event, $code, $data){
        try {
            $json = [
                'event' => $event,
                'data' => $data,
            ];
    
            Redis::publish('verify:' . $code, json_encode($json));
            return response()->json(['success' => true]);
    
        } catch (ConnectionException $e) {
            return response()->json(['error' => 'Redis connection error: ' . $e->getMessage()], 500);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}