<?php

namespace App\Services;

//services section
use App\Services\ResponseService;

//redis section
use Illuminate\Support\Facades\Redis;

class KickVerifyService
{
    protected $responseService;

    public function __construct($event, $code, $data, ResponseService $responseService)
    {
        $this->responseService = $responseService;
        $this->send($event, $code, $data);
    }

    private function send($event, $code, $data){
        try {
            $json = [
                'event' => $event,
                'data' => $data,
            ];
    
            Redis::publish('verify:' . $code, json_encode($json));
            return $this->responseService->response(true, "Udało się wysłać powiadomienie", [], 200);
        } catch (ConnectionException $e) {
            return $this->responseService->response(false, 'Błąd połączenia Redis: ' . $e->getMessage(), [], 500);
        } catch (\Exception $e) {
            return $this->responseService->response(false, 'Wystąpił błąd: ' . $e->getMessage(), [], 500);
        }
    }
}