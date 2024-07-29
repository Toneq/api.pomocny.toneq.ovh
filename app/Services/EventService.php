<?php

namespace App\Services;

//services section
use App\Services\ResponseService;

//redis section
use Illuminate\Support\Facades\Redis;

class EventService
{
    protected $responseService;

    public function __construct($type, $event, $channelName, $data, ResponseService $responseService = null)
    {
        $this->responseService = $responseService;
        $this->send($type, $event, $channelName, $data);
    }

    private function send($type, $event, $channelName, $data){
        try {
            $json = [
                'event' => $event,
                'data' => $data,
            ];
    
            Redis::publish('user:' . $channelName . ':' . $type, json_encode($json));
            return $this->responseService->response(true, "Udało się wysłać powiadomienie", [], 200);
        } catch (ConnectionException $e) {
            return $this->responseService->response(false, 'Błąd połączenia Redis: ' . $e->getMessage(), [], 500);
        } catch (\Exception $e) {
            return $this->responseService->response(false, 'Wystąpił błąd: ' . $e->getMessage(), [], 500);
        }
    }
}