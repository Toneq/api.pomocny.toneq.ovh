<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Services\EventService;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $eventService;

    public function __construct(EventService $eventService,){
        $this->eventService = $eventService;
    }

    public function test(){
        return $this->eventService->test();
    }    
}
