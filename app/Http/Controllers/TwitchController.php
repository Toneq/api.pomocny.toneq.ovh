<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class TwitchController extends Controller
{
    public function getTwitchAuthUrl()
    {
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
        $user = Socialite::driver('twitch')->stateless()->user();

        if($user){
            $data = [
                "success" => true,
                "message" => "Konto zostało połączone",
                "data" => []
            ];

            echo "<script>
                window.opener.postMessage(" . json_encode($data) . ", '" . url('https://pomocny.toneq.ovh/providers') . "');
                window.close();
            </script>";
        } else {
            $data = [
                "success" => false,
                "message" => "Niestaty nie udało się połączyć konta!",
                "data" => []
            ];
            echo "<script>
                window.opener.postMessage(" . json_encode($data) . ", '" . url('https://pomocny.toneq.ovh/providers') . "');
                window.close();
            </script>";
        }

    }
}
