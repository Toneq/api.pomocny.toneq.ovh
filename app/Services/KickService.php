<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use OTPHP\TOTP;
use App\Models\AccessToken;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Artisan; 
// use Illuminate\Support\Facades\Redis;
use CurlImpersonate\CurlImpersonate;
use Illuminate\Support\Facades\Process;

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
        // $postData = [
        //     'command' => 'title',
        //     'parameter' => $title
        // ];

        // $curl = new CurlImpersonate();
        // $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/channels/toneq/chat-commands');
        // $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        // $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        // $curl->setopt(CURLCMDOPT_POSTFIELDS, http_build_query($postData));
        // $curl->setopt(CURLCMDOPT_ENGINE, '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116');
        
        // $response = $curl->execStandard();
        // $curl->closeStream();

        // $this->notificationService->sendNotification('kick:set-title', ["title" => $title]);

        // return $response;

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-X', 'POST',
            '-d', 'command=title',
            '-d', 'parameter=' . $title,
            'https://kick.com/api/v2/channels/toneq/chat-commands'
        ];
        
        $result = Process::run($command);
        
        $output = $result->output();

        return $output;
    }

    public function setCategory($game)
    {
        // $postData = [
        //     'command' => 'category',
        //     'parameter' => $game
        // ];
        
        // $curl = new CurlImpersonate();
        // $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/channels/toneq/chat-commands');
        // $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        // $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        // $curl->setopt(CURLCMDOPT_POSTFIELDS, http_build_query($postData));
        // $curl->setopt(CURLCMDOPT_ENGINE, '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116');
        
        // $response = $curl->execStandard();
        // $curl->closeStream();

        // $this->notificationService->sendNotification('kick:set-category', ["category" => $game]);

        // return $response;

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-X', 'POST',
            '-d', 'command=category',
            '-d', 'parameter=' . $game,
            'https://kick.com/api/v2/channels/toneq/chat-commands'
        ];
        
        $result = Process::run($command);
        
        $output = $result->output();

        return $output;
    }

    public function clearChat()
    {
        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-X', 'POST',
            '-d', 'command=clear',
            'https://kick.com/api/v2/channels/toneq/chat-commands'
        ];
        
        $result = Process::run($command);
        
        $output = $result->output();

        return $output;
    }

    public function permBan($user)
    {
        // $headers = [
        //     "Content-Type: application/json",
        //     "Authorization: " . $this->accessToken,
        // ];

        // $postData = [
        //     'banned_username' => $user,
        //     'permanent' => true
        // ];
        
        // $curl = new CurlImpersonate();
        // $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/channels/toneq/bans');
        // $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        // $curl->setopt(CURLCMDOPT_HTTP_HEADERS, $headers);
        // $curl->setopt(CURLCMDOPT_POSTFIELDS, $postData);
        // $curl->setopt(CURLCMDOPT_ENGINE, '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116');
        
        // $response = $curl->execStandard();
        // $curl->closeStream();

        // $this->notificationService->sendNotification('kick:permban', ["user" => $user]);

        // return $response;

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            // '-H', 'Content-Type: application/json',
            '-X', 'POST',
            '-d', 'banned_username=' . $user,
            '-d', 'permanent=true',
            'https://kick.com/api/v2/channels/toneq/bans'
        ];
        
        $result = Process::run($command);
        
        $output = $result->output();

        return $output;
    }

    public function tempBan($user, $duration)
    {
        // $postData = [
        //     'banned_username' => $user,
        //     'permanent' => false,
        //     'duration' => $duration
        // ];
        
        // $curl = new CurlImpersonate();
        // $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/channels/toneq/bans');
        // $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        // $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        // $curl->setopt(CURLCMDOPT_POSTFIELDS, http_build_query($postData));
        // $curl->setopt(CURLCMDOPT_ENGINE, '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116');
        
        // $response = $curl->execStandard();
        // $curl->closeStream();

        // $this->notificationService->sendNotification('kick:tempban', ["user" => $user, "duration" => $duration]);

        // return $response;

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-X', 'POST',
            '-d', 'banned_username=' . $user,
            '-d', 'permanent=false',
            '-d', 'duration=' . $duration,
            'https://kick.com/api/v2/channels/toneq/bans'
        ];
        
        $result = Process::run($command);
        
        $output = $result->output();

        return $output;
    }

    public function unban($user)
    {
        // $curl = new CurlImpersonate();
        // $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/channels/toneq/bans/' . $user);
        // $curl->setopt(CURLCMDOPT_METHOD, 'DELETE');
        // $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        // $curl->setopt(CURLCMDOPT_ENGINE, '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116');
        
        // $response = $curl->execStandard();
        // $curl->closeStream();

        // $this->notificationService->sendNotification('kick:unban', ["user" => $user]);

        // return $response;

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-X', 'DELETE',
            'https://kick.com/api/v2/channels/toneq/bans/' . $user
        ];
        
        $result = Process::run($command);
        
        $output = $result->output();

        return $output;
    }

    public function deleteMessage($messageId)
    {      
        // $curl = new CurlImpersonate();
        // $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/chatrooms/196508/messages/' . $messageId);
        // $curl->setopt(CURLCMDOPT_METHOD, 'DELETE');
        // $curl->setopt(CURLCMDOPT_HTTP_HEADERS, ['Authorization' => 'Bearer ' . $this->accessToken]);
        // $curl->setopt(CURLCMDOPT_ENGINE, '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116');
        
        // $response = $curl->execStandard();
        // $curl->closeStream();

        // $this->notificationService->sendNotification('kick:delete-message', ["messageId" => $messageId]);

        // return $response;

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-X', 'DELETE',
            'https://kick.com/api/v2/chatrooms/196508/messages/' . $messageId
        ];
        
        $result = Process::run($command);
        
        $output = $result->output();

        return $output;
    }

    public function sendMessage($message)
    {
        // $headers = [
        //     "Content-Type: application/json",
        //     "Authorization: " . $this->accessToken,
        // ];

        // $postData = [
        //     'type' => 'message',
        //     'content' => $message
        // ];
        
        // $curl = new CurlImpersonate();
        // $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/api/v2/messages/send/196508');
        // $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        // $curl->setopt(CURLCMDOPT_HTTP_HEADERS, $headers);
        // $curl->setopt(CURLCMDOPT_POSTFIELDS, $postData);
        // $curl->setopt(CURLCMDOPT_ENGINE, '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116');

        // $response = $curl->execStandard();

        // $curl->closeStream();
        // $this->notificationService->sendNotification('kick:send-message', ["message" => $message]);

        // return $response;

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            // '-H', 'Content-Type: application/json',
            '-X', 'POST',
            '-d', 'type=message',
            '-d', 'content=' . $message,
            'https://kick.com/api/v2/messages/send/196508'
        ];
        
        $result = Process::run($command);
        
        $output = $result->output();

        return $output;
    }

    public function createAccessToken()
    {
        $curl = new CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/kick-token-provider');
        $curl->setopt(CURLCMDOPT_METHOD, 'GET');
        $curl->setopt(CURLCMDOPT_ENGINE, '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116');
        
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
        
        $curl = new CurlImpersonate();
        $curl->setopt(CURLCMDOPT_URL, 'https://kick.com/mobile/login');
        $curl->setopt(CURLCMDOPT_METHOD, 'POST');
        $curl->setopt(CURLCMDOPT_POSTFIELDS, http_build_query($postData));
        $curl->setopt(CURLCMDOPT_ENGINE, '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116');
        
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