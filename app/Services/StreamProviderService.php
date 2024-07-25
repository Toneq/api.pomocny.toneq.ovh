<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\StreamProvider;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class StreamProviderService
{
    public function getAccontProviders($request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $providers = StreamProvider::where('user_id', $user->id)->get();
            $services = $providers->pluck('service');
            
            return response()->json($services);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
