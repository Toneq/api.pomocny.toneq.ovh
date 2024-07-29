<?php

namespace App\Services;

//services section
use App\Services\ResponseService;

//redis section
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
            new ResponseService(true, "Udało się wysłać powiadomienie", [], 200);
        } catch (ConnectionException $e) {
            new ResponseService(false, 'Redis connection error: ' . $e->getMessage(), [], 500);
        } catch (\Exception $e) {
            new ResponseService(false, 'An error occurred: ' . $e->getMessage(), [], 500);
        }
    }
}