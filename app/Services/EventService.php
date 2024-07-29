<?php

namespace App\Services;

//services section
use App\Services\ResponseService;

//redis section
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
            new ResponseService(true, "Udało się wysłać powiadomienie", [], 200);
        } catch (ConnectionException $e) {
            new ResponseService(false, 'Błąd połączenia Redis: ' . $e->getMessage(), [], 500);
        } catch (\Exception $e) {
            new ResponseService(false, 'Wystąpił błąd: ' . $e->getMessage(), [], 500);
        }
    }
}