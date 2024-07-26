<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KickService;
use App\Models\StreamProvider;
use App\Models\KickVerifyCode;
use App\Models\AccessToken;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Ramsey\Uuid\Uuid;
use App\Services\KickVerifyService;

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
            return response()->json(['success' => false, 'message' => "Brak autoryzacji z aplikacji!", 'data' => []], 403);
        }

        $uuid = Uuid::uuid4();

        KickVerifyCode::create([
            'user_id' => $appUser->id,
            'service' => 'kick',
            'code' =>  $uuid->toString(),
        ]);

        return response()->json(['success' => true, 'message' => "Kod weryfikacyjny został wygenerowany!", 'data' => ['code' => $uuid->toString()]], 200);
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
            return response()->json(['success' => false, 'message' => "Brak autoryzacji z aplikacji!", 'data' => []], 403);
        }

        $providerId = $request->input("id");

        $isProvider = StreamProvider::where('user_provider_id', $providerId)
                                    ->where('service', 'kick')
                                    ->first();

        if($isProvider) {
            return response()->json(['success' => false, 'message' => "To konto jest już przypisane do innego konta", 'data' => []], 401);
        }

        $isConnected = StreamProvider::where('user_id', $appUser->id)
                                    ->where('service', 'kick')
                                    ->first();

        if($isConnected) {
            return response()->json(['success' => false, 'message' => "Użytkownik ma już przypisane konto KICK", 'data' => []], 401);
        }

        StreamProvider::create([
            'user_id' => $appUser->id,
            'service' => 'kick',
            'user_provider_id' => $providerId,
            'active' => 1
        ]);

        return response()->json(['success' => true, 'message' => "Konto zostało połączone", 'data' => []], 200);
    }
}
