<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Services\StreamProviderService;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $streamProviderService;

    public function __construct(StreamProviderService $streamProviderService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->streamProviderService = $streamProviderService;
    }

    public function getAccontProviders(Request $request){
        return $this->streamProviderService->getAccontProviders($request);
    }
}
