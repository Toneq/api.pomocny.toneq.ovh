<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Predis\Connection\ConnectionException;
use Illuminate\Http\Request;
use App\Services\EventService;
class EventController extends Controller
{


    protected $request;

    public function __construct(Request $request)
    {
        $type = $request->input("type");
        $event = $request->input("event");
        $channelName = $request->input("channel");
        $data = $request->input("data");

        new EventService($type, $event, $channelName, $data);
    }

    public function __invoke(Request $request)
    {
        return response()->json(['status' => 'success']);
    }

    // protected $eventService;

    // public function __construct(EventService $eventService,){
    //     $this->eventService = $eventService;
    // }

    // public function subscribe($user)
    // {
    //     try {
    //         $response = new StreamedResponse(function () use ($user) {
    //             Redis::psubscribe(["user:{$user}:event"], function ($message) {
    //                 echo "data: $message\n\n";
    //                 ob_flush();
    //                 flush();
    //             });
    //         });
        
    //         $response->headers->set('Content-Type', 'text/event-stream');
    //         $response->headers->set('Cache-Control', 'no-cache');
    //         $response->headers->set('Connection', 'keep-alive');
    //         $response->headers->set('X-Accel-Buffering', 'no');
        
    //         return $response;
    //     } catch (ConnectionException $e) {
    //         \Log::error('Redis connection error: ' . $e->getMessage());
    //         return response()->json(['error' => 'Could not connect to Redis server'], 500);
    //     } catch (\Exception $e) {
    //         \Log::error('An error occurred: ' . $e->getMessage());
    //         return response()->json(['error' => 'An internal server error occurred'], 500);
    //     }
    // }
}
