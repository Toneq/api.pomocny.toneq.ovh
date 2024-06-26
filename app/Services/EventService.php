<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class EventService
{
    public function message($user){   
        try {
            $data = [
                'event' => 'message',
                'data' => [],
            ];
    
            Redis::publish(`user:{$user->id}:event`, json_encode($data));
            return response()->json(['success' => true]);
    
        } catch (ConnectionException $e) {
            return response()->json(['error' => 'Redis connection error: ' . $e->getMessage()], 500);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function follow($user){   
        try {
            $data = [
                'event' => 'follow',
                'data' => [],
            ];
    
            Redis::publish(`user:{$user->id}:event`, json_encode($data));
            return response()->json(['success' => true]);
    
        } catch (ConnectionException $e) {
            return response()->json(['error' => 'Redis connection error: ' . $e->getMessage()], 500);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function raid($user){   
        try {
            $data = [
                'event' => 'raid',
                'data' => [],
            ];
    
            Redis::publish(`user:{$user->id}:event`, json_encode($data));
            return response()->json(['success' => true]);
    
        } catch (ConnectionException $e) {
            return response()->json(['error' => 'Redis connection error: ' . $e->getMessage()], 500);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function subscription($user){   
        try {
            $data = [
                'event' => 'subscription',
                'data' => [],
            ];
    
            Redis::publish(`user:{$user->id}:event`, json_encode($data));
            return response()->json(['success' => true]);
    
        } catch (ConnectionException $e) {
            return response()->json(['error' => 'Redis connection error: ' . $e->getMessage()], 500);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function test(){   
        try {
            $data = [
                'event' => 'message',
                'data' => [
                    'user' => 'test',
                    'badges' => ['1', '2', '3'],
                    'service' => 'twitch',
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam fermentum lacinia pharetra. Donec varius dui enim, eu imperdiet odio tincidunt ut. Duis varius, lorem in pretium consequat, nisl sapien pharetra urna, a ornare tortor tortor a felis. In consequat arcu purus, quis molestie leo dictum eget. Cras sed turpis vel dolor lobortis vulputate. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus molestie, nulla non dictum convallis, tortor leo egestas nibh, eu dictum ligula mi ut ex. Mauris eu venenatis nisl. Quisque suscipit rhoncus congue. Cras vitae erat viverra, ultricies dui eget, vulputate tellus. Phasellus.',
                    'message_id' => 'XXXXX-xxxx-xxxx-xxxxx'
                ],
            ];
    
            Redis::publish('user:test:event', json_encode($data));
            return response()->json(['success' => true]);
    
        } catch (ConnectionException $e) {
            return response()->json(['error' => 'Redis connection error: ' . $e->getMessage()], 500);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
