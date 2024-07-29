<?php

namespace App\Services;

//models section
use App\Models\AccessToken;

//services section
use App\Services\EventService;
use App\Services\ResponseService;

//others section
use Illuminate\Support\Facades\Http;
use OTPHP\TOTP;

class TwitchService
{
    protected $clientId;
    protected $clientSecret;
    protected $accessTokenBot;
    protected $accessTokenBroadcaster;
    protected $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->clientId = env('TWITCH_CLIENT_ID');
        $this->clientSecret = env('TWITCH_CLIENT_SECRET');
        $this->accessTokenBot = $this->getAccessToken("bot");
        $this->accessTokenBroadcaster = $this->getAccessToken("broadcaster");
        $this->responseService = $responseService;
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
        new EventService("event", "twitch:set-title", "test", ["title" => $title]);
        return $this->responseService->response(true, "Tytuł został pomyślnie zmieniony", $response, 200);
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
        new EventService("event", "twitch:set-category", "test", ["category" => $game]);
        return $this->responseService->response(true, "Kategoria została pomyślnie zmieniona", $response, 200);
    }

    public function clearChat()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessTokenBot,
            'Client-Id' => $this->clientId,
            'Content-Type' => 'application/json',
        ])->delete('https://api.twitch.tv/helix/moderation/chat', [
            'broadcaster_id' => '190291001',
            'moderator_id' => '896529196'
        ]);
        new EventService("event", "twitch:clear-chat", "test", []);
        return $this->responseService->response(true, "Czat został wyczyszczony", $response, 200);
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
        new EventService("event", "twitch:permban", "test", ["user" => $user]);
        return $this->responseService->response(true, "Permban został pomyślnie nadany", $response, 200);
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
        new EventService("event", "twitch:tempban", "test", ["user" => $user, "duration" => $duration]);
        return $this->responseService->response(true, "Tymczasowe wykluczenie zostało pomyślnie nadane", $response, 200);
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
        new EventService("event", "twitch:unban", "test", ["user" => $user]);
        return $this->responseService->response(true, "Unban został pomyślnie nadany", $response, 200);
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
        new EventService("event", "twitch:delete-message", "test", ["messageId" => $messageId]);
        return $this->responseService->response(true, "Wiadomość została pomyślnie usunięta", $response, 200);
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

        return $this->responseService->response(true, "Wiadomość została pomyślnie wysłana", $response, 200);
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

        return $this->responseService->response(true, "Powiadomienie zostało pomyślnie wysłane", $response, 200);
    }

    // public function createAccessToken()
    // {
    //     $response = Http::post('https://id.twitch.tv/oauth2/token', [
    //         'grant_type' => 'refresh_token',
    //         'refresh_token' => $this->accessTokenBot,
    //         'client_id' => $this->clientId,
    //         'client_secret' => $this->clientSecret,
    //     ]);

    //     if ($response->successful()) {
    //         $token = $response->json()['access_token'];
    //         AccessToken::updateOrCreate(
    //             ['service' => 'twitch'],
    //             ['user' => 'bot'],
    //             ['type' => 'bot'],
    //             ['token' => $token]
    //         );
    //         return response()->json(['token' => $token]);
    //     } else {
    //         return response()->json(['error' => 'failed create access token'], 500);
    //     }
    // }

    private function getAccessToken($type){
        return AccessToken::where('service', 'twitch')
                            ->where('user', $type)
                            ->pluck('token')
                            ->first();
    }
}