<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use App\Services\TwitchService;
use App\Models\StreamProvider;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class TwitchController extends Controller
{
    protected $twitchService;

    public function __construct(TwitchService $twitchService)
    {
        $this->twitchService = $twitchService;
    }

    public function getTwitchAuthUrl(Request $request)
    {
        $token = $request->query('token');
        Session::put('auth_token', $token);

        return Socialite::driver('twitch')
                    ->scopes([
                        'user:read:email',
                        'user:read:broadcast',
                        'channel:manage:broadcast',
                        'channel:read:redemptions',
                        'chat:read',
                        'channel:moderate',
                        'bits:read',
                        'channel_subscriptions',
                        'channel:read:subscriptions',
                        'channel:manage:predictions',
                        'channel:manage:polls',
                        'channel:edit:commercial',
                        'channel:read:charity',
                        'channel:read:cheers',
                        'moderator:read:chatters',
                        'channel:read:vips',
                        'moderation:read',
                        'moderator:read:followers',
                        'channel:read:hype_train',
                        'channel:bot',
                        'channel:manage:ads',
                        'channel:read:ads'
                    ])
                    ->redirect();
    }

    public function handleTwitchCallback()
    {
        $token = Session::pull('auth_token');
        $twitchUser = Socialite::driver('twitch')->stateless()->user();

        JWTAuth::setToken($token);
        $appUser = JWTAuth::authenticate();
        // $appUser = JWTAuth::parseToken()->authenticate();
        // $user["token"];
        // $user["refreshToken"];
        // $user["nickname"];
        
        if(!$twitchUser){
            $data = [
                "success" => false,
                "message" => "Brak autoryzacji z twitch!",
                "data" => []
            ];
            $this->postMessage($data);
        }

        if(!$appUser){
            $data = [
                "success" => false,
                "message" => "Brak autoryzacji z aplikacji!",
                "data" => []
            ];
            $this->postMessage($data);
        }

        $connected = StreamProvider::where('user_provider_id', $twitchUser["id"])
                                    ->first();

        if($connected) {
            $data = [
                "success" => false,
                "message" => "To konto jest już przypisane do innego konta",
                "data" => [
                    "pageToken" => $token,
                ]
            ];
            $this->postMessage($data);
        }

        StreamProvider::create([
            'user_id' => $appUser->id,
            'service' => 'twitch',
            'user_provider_id' => $twitchUser["id"],
            'active' => 1
        ]);

        AccessToken::updateOrCreate(
            ['service' => 'twitch'],
            ['user' => $appUser->id],
            ['type' => 'access'],
            ['token' => $twitchUser["token"]]
        );

        AccessToken::updateOrCreate(
            ['service' => 'twitch'],
            ['user' => $appUser->id],
            ['type' => 'refresh'],
            ['token' => $twitchUser["refreshToken"]]
        );

        $data = [
            "success" => true,
            "message" => "Konto zostało połączone",
            "data" => []
        ];
        $this->postMessage($data);
    }

    private function postMessage($data){
        echo "<script>
            window.opener.postMessage(" . json_encode($data) . ", '" . url('https://pomocny.toneq.ovh/providers') . "');
            window.close();
        </script>";
        return;
    }
}
