<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Services\EventService;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $eventService;

    public function __construct(EventService $eventService,){
        $this->eventService = $eventService;
    }

    public function sendNotification(Request $request){
        return $this->eventService->message("x");
    }

    public function sendWinkNotification(Request $request){
        return $this->eventService->follow("x");
    }

    public function sendMessageNotification(Request $request){
        return $this->eventService->raid("x");
    }
}
