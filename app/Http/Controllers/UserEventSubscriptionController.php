<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserEventSubscriptionController extends Controller
{
    public function subscribe(Request $request, $prefix)
    {
        $response = new StreamedResponse(function () use ($prefix) {
            Redis::psubscribe(["user:{$prefix}:event"], function ($message) {
                echo "data: $message\n\n";
                ob_flush();
                flush();
            });
        });
    
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');
    
        return $response;
    }
}
