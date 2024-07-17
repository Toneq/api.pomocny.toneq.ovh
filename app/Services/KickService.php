<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use OTPHP\TOTP;
use App\Models\AccessToken;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Artisan; 
// use Illuminate\Support\Facades\Redis;

class KickService
{
    protected $accessToken;
    protected $qr_code;
    protected $email;
    protected $password;

    public function __construct()
    {
        $this->qr_code = env('KICK_OTP_QR_CODE');
        $this->accessToken = $this->getAccessToken("bot");
        $this->email = env('KICK_ACCOUNT_EMAIL');
        $this->password = env('KICK_ACCOUNT_PASSWORD');
    }

    public function setTitle($title)
    {
        $postData = [
            'command' => 'title',
            'parameter' => $title
        ];
        
        $curl = new CurlImpersonate\CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/channels/toneq/chat-commands');
        $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        $curl->setopt(CURLCMDOPT_POSTFIELDS, http_build_query($postData));
        $curl->setopt(CURLCMDOPT_ENGINE, './curl/curl_chrome116');
        
        $response = $curl->execStandard();
        $curl->closeStream();

        // $this->notificationService->sendNotification('kick:set-title', ["title" => $title]);

        return $response->json();
    }

    public function setCategory($game)
    {
        $postData = [
            'command' => 'category',
            'parameter' => $game
        ];
        
        $curl = new CurlImpersonate\CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/channels/toneq/chat-commands');
        $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        $curl->setopt(CURLCMDOPT_POSTFIELDS, http_build_query($postData));
        $curl->setopt(CURLCMDOPT_ENGINE, './curl/curl_chrome116');
        
        $response = $curl->execStandard();
        $curl->closeStream();

        // $this->notificationService->sendNotification('kick:set-category', ["category" => $game]);

        return $response->json();
    }

    public function permBan($user)
    {
        $postData = [
            'banned_username' => $user,
            'permanent' => true
        ];
        
        $curl = new CurlImpersonate\CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/channels/toneq/bans');
        $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        $curl->setopt(CURLCMDOPT_POSTFIELDS, http_build_query($postData));
        $curl->setopt(CURLCMDOPT_ENGINE, './curl/curl_chrome116');
        
        $response = $curl->execStandard();
        $curl->closeStream();

        // $this->notificationService->sendNotification('kick:permban', ["user" => $user]);

        return $response->json();
    }

    public function tempBan($user, $duration)
    {
        $postData = [
            'banned_username' => $user,
            'permanent' => false,
            'duration' => $duration
        ];
        
        $curl = new CurlImpersonate\CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/channels/toneq/bans');
        $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        $curl->setopt(CURLCMDOPT_POSTFIELDS, http_build_query($postData));
        $curl->setopt(CURLCMDOPT_ENGINE, './curl/curl_chrome116');
        
        $response = $curl->execStandard();
        $curl->closeStream();

        // $this->notificationService->sendNotification('kick:tempban', ["user" => $user, "duration" => $duration]);

        return $response->json();
    }

    public function unban($user)
    {
        $curl = new CurlImpersonate\CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/channels/toneq/bans/' . $user);
        $curl->setopt(CURLCMDOPT_METHOD, 'DELETE');
        $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        $curl->setopt(CURLCMDOPT_ENGINE, './curl/curl_chrome116');
        
        $response = $curl->execStandard();
        $curl->closeStream();

        // $this->notificationService->sendNotification('kick:unban', ["user" => $user]);

        return $response->json();
    }

    public function deleteMessage($messageId)
    {      
        $curl = new CurlImpersonate\CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/chatrooms/196508/messages/' . $messageId);
        $curl->setopt(CURLCMDOPT_METHOD, 'DELETE');
        $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        $curl->setopt(CURLCMDOPT_ENGINE, './curl/curl_chrome116');
        
        $response = $curl->execStandard();
        $curl->closeStream();

        // $this->notificationService->sendNotification('kick:delete-message', ["messageId" => $messageId]);

        return $response->json();
    }

    public function sendMessage($message)
    {
        $postData = [
            'type' => 'message',
            'content' => $message
        ];
        
        $curl = new CurlImpersonate\CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/messages/send/196508');
        $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        $curl->setopt(CURLCMDOPT_POSTFIELDS, http_build_query($postData));
        $curl->setopt(CURLCMDOPT_ENGINE, './curl/curl_chrome116');
        
        $response = $curl->execStandard();
        $curl->closeStream();

        // $this->notificationService->sendNotification('kick:send-message', ["message" => $message]);

        return $response->json();
    }

    public function createAccessToken()
    {
        $curl = new CurlImpersonate\CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/kick-token-provider');
        $curl->setopt(CURLCMDOPT_METHOD, 'GET');
        $curl->setopt(CURLCMDOPT_ENGINE, './curl/curl_chrome116');
        
        $response = $curl->execStandard();
        $curl->closeStream();
        $responseData = json_decode($response, true);
        $encryptedValidFrom = $responseData['encryptedValidFrom'];
        
        $timestamp = time();
        $otp = TOTP::create($this->qr_code);
        $code = $otp->at($timestamp);
        
        $postData = [
            'email' => $this->email,
            'password' => $this->password,
            'isMobileRequest' => true,
            'nameFieldName' => '',
            'validFromFieldName' => $encryptedValidFrom,
            'one_time_password' => $code
        ];
        
        $curl = new CurlImpersonate\CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/mobile/login');
        $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        $curl->setopt(CURLCMDOPT_POSTFIELDS, http_build_query($postData));
        $curl->setopt(CURLCMDOPT_ENGINE, './curl/curl_chrome116');
        
        $response = $curl->execStandard();
        $curl->closeStream();
        
        $responseData = json_decode($response, true);
        $token = $responseData['token'];

        AccessToken::updateOrCreate(
            ['service' => 'kick'],
            ['token' => $token]
        );

        return response()->json(['token' => $token]);
    }

    public function getOTP(){
        $timestamp = time();
        $otp = TOTP::create($this->qr_code);
        $code = $otp->at($timestamp);

        return response()->json(['code' => $code]);
    }

    private function getAccessToken(){
        return AccessToken::where('service', 'kick')->pluck('token')->first();
    }
}