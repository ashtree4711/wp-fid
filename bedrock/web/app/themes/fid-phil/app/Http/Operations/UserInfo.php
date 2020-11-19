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
            $userInfo["user"]=self::getUser($_COOKIE["fidsession"], $_COOKIE["fiduser"], $request->getServerParams()["KUG_HOME"]);
            $userInfo["login"]=true;
        } else {
            $userInfo["login"]=false;
        }
        return $userInfo;
    }


    private static function getUser($sessionId, $userId, $kugUrl){
        $client = new Client();
        $user=null;
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
            [
                'sessionID' => $sessionId
            ],
            'localhost'
        );
        try {
            $res = $client->request("GET", $kugUrl."/portal/fidphil/users/id/".$userId.".json?l=de", ['cookies' => $jar]);
            $user=json_decode($res->getBody()->getContents());

        } catch (ClientException $exception){

        }
        return $user;
    }
}