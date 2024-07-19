<?php

namespace App\Services;

use OTPHP\TOTP;
use App\Models\AccessToken;
use App\Services\EventService;
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
        new EventService("event", "kick:set-title", "test", ["title" => $title]);
        return $output;
    }

    public function setCategory($game)
    {
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
        new EventService("event", "kick:set-category", "test", ["category" => $game]);
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
        new EventService("event", "kick:clear-chat", "test", []);
        return $output;
    }

    public function permBan($user)
    {
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
        new EventService("event", "kick:permban", "test", ["user" => $user]);
        return $output;
    }

    public function tempBan($user, $duration)
    {
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
        new EventService("event", "kick:tempban", "test", ["user" => $user, "duration" => $duration]);
        return $output;
    }

    public function unban($user)
    {
        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-X', 'DELETE',
            'https://kick.com/api/v2/channels/toneq/bans/' . $user
        ];
        $result = Process::run($command);
        $output = $result->output();
        new EventService("event", "kick:unban", "test", ["user" => $user]);
        return $output;
    }

    public function deleteMessage($messageId)
    {      
        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-X', 'DELETE',
            'https://kick.com/api/v2/chatrooms/196508/messages/' . $messageId
        ];
        $result = Process::run($command);
        $output = $result->output();
        new EventService("event", "kick:delete-message", "test", ["messageId" => $messageId]);
        return $output;
    }

    public function sendMessage($message)
    {
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