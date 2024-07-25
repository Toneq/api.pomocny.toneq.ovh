<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use App\Services\TwitchService;
use App\Models\StreamProvider;
use App\Models\AccessToken;
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

        $twitchId = $twitchUser["id"];
        $twitchAccessToken = $twitchUser->accessTokenResponseBody['access_token'];
        $twitchRefreshToken = $twitchUser->accessTokenResponseBody['refresh_token'];

        $connected = StreamProvider::where('user_provider_id', $twitchId)
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
            'user_provider_id' => $twitchId,
            'active' => 1
        ]);

        AccessToken::updateOrCreateToken($twitchAccessToken, 'twitch', 'access', $appUser->id);
        AccessToken::updateOrCreateToken($twitchRefreshToken, 'twitch', 'refresh', $appUser->id);

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
