<?php

namespace App\Services;

//models section
use App\Models\StreamProvider;

//services section
use App\Services\ResponseService;

//jwt section
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class StreamProviderService
{
    protected $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function getAccontProviders($request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $providers = StreamProvider::where('user_id', $user->id)->get();
            $services = $providers->pluck('service');
            return $this->responseService->response(true, "Udało sie pobrać zintegrowane platformy", $services, 200);
        } catch (JWTException $e) {
            return $this->responseService->response(false, "Nieautoryzowany", [], 401);
        }
    }
}
