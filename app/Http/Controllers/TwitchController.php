<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TwitchController extends Controller
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;

    public function __construct()
    {
        $this->clientId = env('TWITCH_CLIENT_ID');
        $this->clientSecret = env('TWITCH_CLIENT_SECRET');
        $this->redirectUri = env('TWITCH_REDIRECT_URI');
    }

    public function getAuthUrl()
    {
        $url = 'https://id.twitch.tv/oauth2/authorize' . 
               '?client_id=' . $this->clientId . 
               '&redirect_uri=' . $this->redirectUri . 
               '&response_type=code' . 
               '&scope=user:read:email';

        return response()->json(['url' => $url]);
    }

    public function handleCallback(Request $request)
    {
        $code = $request->input('code');

        $response = Http::post('https://id.twitch.tv/oauth2/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
        ]);

        $data = $response->json();

        $userInfo = Http::withHeaders([
            'Authorization' => 'Bearer ' . $data['access_token'],
            'Client-Id' => $this->clientId,
        ])->get('https://api.twitch.tv/helix/users');

        return response()->json($userInfo->json());
    }
}
