<?php

namespace App\Services;

//models section
use App\Models\AccessToken;

//services section
use App\Services\EventService;
use App\Services\ResponseService;

//Illuminate section
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

//others section
use OTPHP\TOTP;
use CurlImpersonate\CurlImpersonate;

class KickService
{
    protected $accessToken;
    protected $qr_code;
    protected $email;
    protected $password;
    protected $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->qr_code = env('KICK_OTP_QR_CODE');
        $this->accessToken = $this->getAccessToken("bot");
        $this->email = env('KICK_ACCOUNT_EMAIL');
        $this->password = env('KICK_ACCOUNT_PASSWORD');
        $this->responseService = $responseService;
    }

    public function setTitle($title)
    {
        $json = [
            "command" => "title",
            "parameter" => $title
        ];

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-d', json_encode($json),
            'https://kick.com/api/v2/channels/toneq/chat-commands'
        ];
        $result = Process::run($command);        

        if ($result->successful()) {
            new EventService("event", "kick:set-title", "test", ["title" => $title]);
            $output = json_decode($result->output(), true);
            return $this->responseService->response(true, "Tytuł został pomyślnie zmieniony", $output, 200);
        } else {
            $errorOutput = $result->errorOutput();
            return $this->responseService->response(false, "Tytuł nie został pomyślnie zmieniony", $errorOutput, 500);
        }
    }

    public function setCategory($game)
    {
        $json = [
            "command" => "category",
            "parameter" => $game
        ];

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-d', json_encode($json),
            'https://kick.com/api/v2/channels/toneq/chat-commands'
        ];
        $result = Process::run($command);        

        if ($result->successful()) {
            new EventService("event", "kick:set-category", "test", ["category" => $game]);
            $output = json_decode($result->output(), true);
            return $this->responseService->response(true, "Kategoria została pomyślnie zmieniona", $output, 200);
        } else {
            $errorOutput = $result->errorOutput();
            return $this->responseService->response(false, "Kategoria nie została pomyślnie zmieniona", $errorOutput, 500);
        }
    }

    public function clearChat()
    {
        $json = [
            "command" => "clear"
        ];

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-d', json_encode($json),
            'https://kick.com/api/v2/channels/toneq/chat-commands'
        ];
        $result = Process::run($command);        

        if ($result->successful()) {
            new EventService("event", "kick:clear-chat", "test", []);
            return $this->responseService->response(true, "Czat został wyczyszczony", $result, 200);
        } else {
            $errorOutput = $result->errorOutput();
            return $this->responseService->response(false, "Czat nie został wyczyszczony", $errorOutput, 500);
        }
    }

    public function permBan($user)
    {
        $json = [
            "banned_username" => $user,
            "permanent" => true
        ];

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-d', json_encode($json),
            'https://kick.com/api/v2/channels/toneq/bans'
        ];      
        $result = Process::run($command);  
        
        if ($result->successful()) {
            new EventService("event", "kick:permban", "test", ["user" => $user]);
            $output = json_decode($result->output(), true);
            return $this->responseService->response(true, "Permban został pomyślnie nadany", $output, 200);
        } else {
            $errorOutput = $result->errorOutput();
            return $this->responseService->response(false, "Permban nie został pomyślnie nadany", $errorOutput, 500);
        }
    }

    public function tempBan($user, $duration)
    {
        $json = [
            "banned_username" => $user,
            "permanent" => false,
            "duration" => $duration
        ];

        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-d', json_encode($json),
            'https://kick.com/api/v2/channels/toneq/bans'
        ];
        $result = Process::run($command);
    
        if ($result->successful()) {
            new EventService("event", "kick:tempban", "test", ["user" => $user, "duration" => $duration]);
            $output = json_decode($result->output(), true);
            return $this->responseService->response(true, "Tymczasowe wykluczenie zostało pomyślnie nadane", $output, 200);
        } else {
            $errorOutput = $result->errorOutput();
            return $this->responseService->response(false, "Tymczasowe wykluczenie nie zostało pomyślnie nadane", $errorOutput, 500);
        }
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
    
        if ($result->successful()) {
            new EventService("event", "kick:unban", "test", ["user" => $user]);
            $output = json_decode($result->output(), true);
            return $this->responseService->response(true, "Unban został pomyślnie nadany", $output, 200);
        } else {
            $errorOutput = $result->errorOutput();
            return $this->responseService->response(false, "Unban nie został pomyślnie nadany", $errorOutput, 500);
        }
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
    
        if ($result->successful()) {
            new EventService("event", "kick:delete-message", "test", ["messageId" => $messageId]);
            $output = json_decode($result->output(), true);
            return $this->responseService->response(true, "Wiadomość została pomyślnie usunięta", $output, 200);
        } else {
            $errorOutput = $result->errorOutput();
            return $this->responseService->response(false, "Wiadomość nie została pomyślnie usunięta", $errorOutput, 500);
        }
    }

    public function sendMessage($message)
    {
        $json = [
            "type" => "message",
            "content" => $message
        ];


        $command = [
            '/var/www/api.pomocny.toneq.ovh/curl/curl_chrome116',
            '-H', 'Authorization: Bearer ' . $this->accessToken,
            '-H', 'Content-Type: application/json',
            '-d', json_encode($json),
            'https://kick.com/api/v2/messages/send/196508'
        ];
        $result = Process::run($command);
    
        if ($result->successful()) {
            $output = json_decode($result->output(), true);
            return $this->responseService->response(true, "Wiadomość została pomyślnie wysłana", $output, 200);
        } else {
            $errorOutput = $result->errorOutput();
            return $this->responseService->response(false, "Wiadomość nie została pomyślnie wysłana", $errorOutput, 500);
        }
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
            ['user' => 'bot'],
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

    private function getAccessToken($type){
        // print_r(AccessToken::where('service', 'kick')->pluck('token')->first());
        return AccessToken::where('service', 'kick')
                            ->where('user', $type)
                            ->pluck('token')
                            ->first();
    }
}