<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\TwitchService;
use App\Services\KickService;

class StreamController extends Controller
{
    protected $twitchService;
    protected $kickService;

    public function __construct(TwitchService $twitchService, KickService $kickService)
    {
        $this->twitchService = $twitchService;
        $this->kickService = $kickService;
    }

    public function setTitle(Request $request){
        $headers = $request->headers->all();
    
        $service = $request->input("service");
        $title = $request->input("title");
    
        if ($service == "kick") {
            $responseKick = $this->kickService->setTitle($title);
            $this->kickService->sendMessage("Tytuł został zmieniony na: " . $title);
            return ["kick" => $responseKick];
        }
    
        if ($service == "twitch") {
            $responseTwitch = $this->twitchService->setTitle($title);
            $this->twitchService->sendAnnouncement("Tytuł został zmieniony na: " . $title, 'green');
            return ["twitch" => $responseTwitch];
        }
    
        if ($service == "both") {
            $responseKick = $this->kickService->setTitle($title);
            $this->kickService->sendMessage("Tytuł został zmieniony na: " . $title);
    
            $responseTwitch = $this->twitchService->setTitle($title);
            $this->twitchService->sendAnnouncement("Tytuł został zmieniony na: " . $title, 'green');
            return ["kick" => $responseKick, "twitch" => $responseTwitch];
        }
    }

    public function setCategory(Request $request){
        $headers = $request->headers->all();

        $game = $request->input("game");
        

        if($request->input("service")=="kick"){
            $responseKick = $this->kickService->setCategory($request->input("game"));
            $this->kickService->sendMessage("Kategoria została zmieniona na: " . $request->input("game"));

            return ["kick" => $responseKick];
        }

        if($request->input("service")=="twitch"){
            $responseTwitch = $this->twitchService->setCategory($request->input("game"));
            $this->twitchService->sendAnnouncement("Kategoria została zmieniona na: " . $request->input("game"), 'purple');

            return ["twitch" => $responseTwitch];
        }

        if($request->input("service")=="both"){
            $responseKick = $this->kickService->setCategory($request->input("game"));
            $this->kickService->sendMessage("Kategoria została zmieniona na: " . $request->input("game"));

            $responseTwitch = $this->twitchService->setCategory($request->input("game"));
            $this->twitchService->sendAnnouncement("Kategoria została zmieniona na: " . $request->input("game"), 'purple');
            

            return ["kick" => $responseKick, "twitch" => $responseTwitch];
        }
    }

    public function clearChat(Request $request){
        $headers = $request->headers->all();        

        if($request->input("service")=="kick"){
            $responseKick = $this->kickService->clearChat();
            return ["kick" => $responseKick];
        }

        if($request->input("service")=="twitch"){
            $responseTwitch = $this->twitchService->clearChat();
            return ["twitch" => $responseTwitch];
        }

        if($request->input("service")=="both"){
            $responseKick = $this->kickService->clearChat();
            $responseTwitch = $this->twitchService->clearChat();           
            return ["kick" => $responseKick, "twitch" => $responseTwitch];
        }
    }

    public function tempBan(Request $request){
        $user = $request->input("user");
        $duration = $request->input("duration");

        if($request->input("service")=="kick"){
            return $this->kickService->tempBan($user, $duration);
        }

        if($request->input("service")=="twitch"){
            return $this->twitchService->tempBan($user, $duration*60);
        }
    }

    public function permBan(Request $request){
        $user = $request->input("user");

        if($request->input("service")=="kick"){
            return $this->kickService->permBan($user);
        }

        if($request->input("service")=="twitch"){
            return $this->twitchService->permBan($user);
        }
    }

    public function unban(Request $request){
        $user = $request->input("user");

        if($request->input("service")=="kick"){
            return $this->kickService->unban($user);
        }

        if($request->input("service")=="twitch"){
            return $this->twitchService->unban($user);
        }
    }

    public function deleteMessage(Request $request){
        $message = $request->input("message");

        if($request->input("service")=="kick"){
            return $this->kickService->deleteMessage($message);
        }

        if($request->input("service")=="twitch"){
            return $this->twitchService->deleteMessage($message);
        }
    }

    public function sendMessage(Request $request){
        $message = $request->input("message");

        if($request->input("service")=="kick"){
            return $this->kickService->sendMessage($message);
        }

        if($request->input("service")=="twitch"){
            return $this->twitchService->sendMessage($message);
        }

        if($request->input("service")=="both"){
            $responseKick = $this->kickService->sendMessage($message);
            $responseTwitch = $this->twitchService->sendMessage($message);

            return ["kick" => $responseKick, "twitch" => $responseTwitch];
        }
    }

    public function getOTP(){
        return $this->kickService->getOTP();
    }

    public function createKickToken(){
        return $this->kickService->createAccessToken();
    }
}
