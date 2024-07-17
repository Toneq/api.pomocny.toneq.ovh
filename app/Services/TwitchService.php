<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use OTPHP\TOTP;
use App\Models\AccessToken;
use Illuminate\Support\Facades\Log;

class TwitchService
{
    protected $clientId;
    protected $clientSecret;
    protected $accessTokenBot;
    protected $accessTokenBroadcaster;

    public function __construct()
    {
        $this->clientId = env('TWITCH_CLIENT_ID');
        $this->clientSecret = env('TWITCH_CLIENT_SECRET');
        $this->accessTokenBot = $this->getAccessToken("bot");
        $this->accessTokenBroadcaster = $this->getAccessToken("broadcaster");
    }

    public function setTitle($title)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessTokenBroadcaster,
            'Client-Id' => $this->clientId,
            'Content-Type' => 'application/json',
        ])->patch('https://api.twitch.tv/helix/channels', [
            'broadcaster_id' => '190291001',
            'title' => $title,
        ]);

        return $response->json();
    }

    public function setCategory($game)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessTokenBroadcaster,
            'Client-Id' => $this->clientId,
            'Content-Type' => 'application/json',
        ])->patch('https://api.twitch.tv/helix/channels', [
            'broadcaster_id' => '190291001',
            'game_id' => $game,
        ]);

        return $response->json();
    }

    public function permBan($user)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessTokenBot,
            'Client-Id' => $this->clientId,
            'Content-Type' => 'application/json',
        ])->post('https://api.twitch.tv/helix/moderation/bans', [
            'broadcaster_id' => '190291001',
            'moderator_id' => '896529196',
            'data' => [
                'user_id' => $user,
                'reason' => 'no reason'
            ]
        ]);

        return $response->json();
    }

    public function tempBan($user, $duration)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessTokenBot,
            'Client-Id' => $this->clientId,
            'Content-Type' => 'application/json',
        ])->post('https://api.twitch.tv/helix/moderation/bans', [
            'broadcaster_id' => '190291001',
            'moderator_id' => '896529196',
            'data' => [
                'user_id' => '9876',
                'reason' => 'no reason',
                'duration' => $duration
            ]
        ]);

        return $response->json();
    }

    public function unban($user)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessTokenBot,
            'Client-Id' => $this->clientId,
            'Content-Type' => 'application/json',
        ])->delete('https://api.twitch.tv/helix/moderation/bans', [
            'broadcaster_id' => '190291001',
            'moderator_id' => '896529196',
            'user_id' => $user
        ]);

        return $response->json();
    }

    public function deleteMessage($messageId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessTokenBot,
            'Client-Id' => $this->clientId,
            'Content-Type' => 'application/json',
        ])->delete('https://api.twitch.tv/helix/moderation/chat', [
            'broadcaster_id' => '190291001',
            'moderator_id' => '896529196',
            'message_id' => $messageId
        ]);

        return $response->json();
    }

    public function sendMessage($message)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessTokenBot,
            'Client-Id' => $this->clientId,
            'Content-Type' => 'application/json',
        ])->post('https://api.twitch.tv/helix/chat/messages', [
            'broadcaster_id' => '190291001',
            'sender_id' => '896529196',
            'message' => $message
        ]);

        return $response->json();
    }

    public function sendAnnouncement($message, $color)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessTokenBot,
            'Client-Id' => $this->clientId,
            'Content-Type' => 'application/json',
        ])->post('https://api.twitch.tv/helix/chat/announcements', [
            'broadcaster_id' => '190291001',
            'sender_id' => '896529196',
            'message' => $message,
            'color' => $color
        ]);

        return $response->json();
    }

    public function createAccessToken()
    {
        $response = Http::post('https://id.twitch.tv/oauth2/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->accessTokenBot,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        if ($response->successful()) {
            $token = $response->json()['access_token'];
            AccessToken::updateOrCreate(
                ['service' => 'twitch'],
                ['token' => $token]
            );
            return response()->json(['token' => $token]);
        } else {
            return response()->json(['error' => 'failed create access token'], 500);
        }
    }

    private function getAccessToken($type){
        return AccessToken::where([
            ['service', '=', 'twitch'],
            ['type', '=', $type]
        ])->pluck('token')->first();
    }
}