<?php

namespace App\Http\Controllers;

//models section
use App\Models\StreamProvider;
use App\Models\KickVerifyCode;

//services section
use App\Services\KickService;
use App\Services\KickVerifyService;
use App\Services\ResponseService;

//Illuminate section
use Illuminate\Http\Request;

//jwt section
use Tymon\JWTAuth\Facades\JWTAuth;

//others section
use Ramsey\Uuid\Uuid;

class KickController extends Controller
{
    protected $kickService;

    public function __construct(KickService $kickService)
    {
        $this->middleware('auth:api', ['except' => ['verifyCode']]);
        $this->kickService = $kickService;
    }

    public function generateKickVerifyCode(Request $request){
        $appUser = JWTAuth::parseToken()->authenticate();

        if(!$appUser){
            new ResponseService(false, "Brak autoryzacji z aplikacji!", [], 403);
        }

        $uuid = Uuid::uuid4();

        KickVerifyCode::create([
            'user_id' => $appUser->id,
            'service' => 'kick',
            'code' =>  $uuid->toString(),
        ]);

        new ResponseService(true, "Kod weryfikacyjny został wygenerowany!", ['code' => $uuid->toString()], 200);
    }

    public function verifyCode(Request $request){
        $code = $request->input("code");
        $name = $request->input("name");
        $id = $request->input("id"); 

        new KickVerifyService("integration", $code, ["channelId" => $id, "channelName" => $name]);
    }

    public function verify(Request $request){
        $appUser = JWTAuth::parseToken()->authenticate();
        if(!$appUser){
            new ResponseService(false, "Brak autoryzacji z aplikacji!", [], 403);
        }

        $providerId = $request->input("id");

        $isProvider = StreamProvider::where('user_provider_id', $providerId)
                                    ->where('service', 'kick')
                                    ->first();

        if($isProvider) {
            new ResponseService(false, "To konto jest już przypisane do innego konta", [], 401);
        }

        $isConnected = StreamProvider::where('user_id', $appUser->id)
                                    ->where('service', 'kick')
                                    ->first();

        if($isConnected) {
            new ResponseService(false, "Użytkownik ma już przypisane konto KICK", [], 401);
        }

        StreamProvider::create([
            'user_id' => $appUser->id,
            'service' => 'kick',
            'user_provider_id' => $providerId,
            'active' => 1
        ]);

        new ResponseService(true, "Konto zostało połączone", [], 200);
    }
}
