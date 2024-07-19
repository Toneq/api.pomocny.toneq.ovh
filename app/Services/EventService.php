<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class EventService
{
    public function __construct($type, $event, $channelName, $data)
    {
        $this->send($type, $event, $channelName, $data);
    }

    private function send($type, $event, $channelName, $data){
        try {
            $json = [
                'event' => $event,
                'data' => $data,
            ];
    
            Redis::publish('user:' . $channelName . ':' . $type, json_encode($json));
            return response()->json(['success' => true]);
    
        } catch (ConnectionException $e) {
            return response()->json(['error' => 'Redis connection error: ' . $e->getMessage()], 500);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}