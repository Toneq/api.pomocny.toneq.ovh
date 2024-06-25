<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Services\NotificationService;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $notificationService;

    public function __construct(NotificationService $notificationService,){
        $this->notificationService = $notificationService;
    }

    public function sendNotification(Request $request){
        return $this->notificationService->sendNotification();
    }

    public function sendWinkNotification(Request $request){
        return $this->notificationService->sendWinkNotification();
    }

    public function sendMessageNotification(Request $request){
        return $this->notificationService->sendMessageNotification();
    }
}
