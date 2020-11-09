<?php


namespace App\Http\Operations;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Rareloop\Lumberjack\Http\ServerRequest;

class UserInfo
{
    public static function get(ServerRequest $request){
        $userInfo=array();
        if (isset($_COOKIE["fidsession"]) && isset($_COOKIE["fiduser"])){
            $userInfo["user"]=self::getUser($_COOKIE["fidsession"], $_COOKIE["fiduser"]);
            $userInfo["login"]=true;
        } else {
            $userInfo["login"]=false;
        }
        return $userInfo;
    }


    private static function getUser($sessionId, $userId){
        $client = new Client();
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
            [
                'sessionID' => $sessionId
            ],
            'localhost'
        );
        try {
            $res = $client->request("GET", "http://localhost:20445/portal/fidphil/users/id/".$userId.".json?l=de", ['cookies' => $jar]);
            $user=json_decode($res->getBody()->getContents());
            $user->login=true;
            $user->status_code=$res->getStatusCode();
        } catch (ClientException $exception){
            $user->login=false;
            $user->status_code=$exception->getResponse()->getStatusCode();
        }
        return $user;
    }
}