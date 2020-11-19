<?php
namespace App\Http\Authentication;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Rareloop\Lumberjack\Http\ServerRequest;
use function Zend\Diactoros\parseCookieHeader;

class Authenticator
{
    public static function login($params, $kugUrl){

        if (isset($_COOKIE["fidsession"])){
            error_log("FID SESSION FOUND: ".$_COOKIE["fidsession"]);
            $jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
                [
                    'sessionID' => $_COOKIE["fidsession"]
            ],
                'localhost'
            );
            $response = self::requestToKug($params, $jar, $kugUrl);
        } else {
            error_log("NO FID SESSION AVAILABLE");
            $jar=array();
            $response = self::requestToKug($params, $jar, $kugUrl);
            $kugCookie = parseCookieHeader($response->getHeaders()["Set-Cookie"][0]);
            error_log(print_r($kugCookie, true));
            setcookie("fidsession", $kugCookie["sessionID"]);
            error_log("FID SESSION CREATED");

        }
        error_log(print_r($response, true));


        if ($response->status_code==200 && isset(explode("/", $response->getHeaders()["Content-Location"][0])[7])){
            setcookie("fiduser", explode("/", $response->getHeaders()["Content-Location"][0])[7]);
        } else {
            $response->status_code=401;
        }


        return $response;
    }


    public static function auth($kugUrl){
        $user=array();
        if (isset($_COOKIE["fidsession"]) && isset($_COOKIE["fiduser"])){
            $user["user"]=self::getUser($_COOKIE["fidsession"], $_COOKIE["fiduser"], $kugUrl);
            $user["login"]=true;
        } else {
            $user["login"]=false;
        }
        return $user;

    }


    private static function getUser($sessionId, $userId, $kugUrl){
        $client = new Client();
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
            [
                'sessionID' => $sessionId
            ],
            'localhost'
        );
        try {
            $res = $client->request("GET", $kugUrl."/portal/fidphil/users/id/".$userId.".json?l=de", ['cookies' => $jar]);
            $user=json_decode($res->getBody()->getContents());
            $user->login=true;
            $user->status_code=$res->getStatusCode();
        } catch (ClientException $exception){
            $user->login=false;
            $user->status_code=$exception->getResponse()->getStatusCode();
        }
        return $user;
    }

    private static function requestToKug($params, $jar, $kugUrl){
        $client = new Client();
        try {
            $res = $client->request("POST", $kugUrl."login", [
                'query' => [
                    "authenticatorid" => 1,
                    "username" => $params["username"],
                    "password" => $params["password"]
                ],
                'cookies' => $jar]);
            $res->status_code=$res->getStatusCode();
            return $res;
        } catch (ClientException $exception){
            $exception->status_code=$exception->getResponse()->getStatusCode();
            return $exception;
        }

    }





}