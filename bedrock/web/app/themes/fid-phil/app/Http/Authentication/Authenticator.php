<?php
namespace App\Http\Authentication;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Rareloop\Lumberjack\Http\ServerRequest;
use function Zend\Diactoros\parseCookieHeader;

class Authenticator
{
    public static function login($params, $kugUrl){
        $response = self::requestToKug($params, $kugUrl);
        $kugCookie = parseCookieHeader($response->getHeaders()["Set-Cookie"][0]);
        error_log(print_r($kugCookie, true));
        $_SESSION["sessionID"]=$kugCookie["sessionID"];
        setcookie("sessionID", $kugCookie["sessionID"], 0, "/portal");
        $user=json_decode($response->getBody()->getContents(), true);
        if ($user["success"]==1){
            $_SESSION["userID"]=$user["userid"];
        } else {
            $response->status_code=401;
        }
        return $response;
    }

    public static function logout($kugUrl){
        $client = new Client();
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
            [
                'sessionID' => $_SESSION["sessionID"]
            ],
            'localhost'
        );
        $user=array();
        try {
            $res = $client->request('GET', $kugUrl."logout",  [
                "cookies" => $jar]);
            $res->status_code=$res->getStatusCode();
            error_log(print_r($res, true));
            unset($_COOKIE["sessionID"]);
            session_unset();
            session_destroy();
            $user["login"]=0;
            $user["logout"]="success";
            return $user;
        } catch (ClientException $exception){
            $code=$exception->getResponse()->getStatusCode();
            error_log(print_r($code, true));
            session_unset();
            session_destroy();
            $user["logout"]="failure";
            return $user;
        }
    }

    public static function signup($kugUrl, $params){
        $client = new Client();
        try {
            $res = $client->request('POST', $kugUrl."/users/registrations",  [
                RequestOptions::JSON => [
                    "username" => $params["username"],
                    "password1"=>$params["password1"],
                    "password2"=>$params["password2"],
                    "wp_home"=>$params["wp_home"]
                ]]);
            $res->status_code=$res->getStatusCode();

            return $res;
        } catch (ClientException $exception){
            $exception->status_code=$exception->getResponse()->getStatusCode();
            return $exception;
        }
    }

    public static function confirm($kugUrl, $params){
        $client = new Client();
        try {
            $res = $client->request('POST', $kugUrl."/users/registrations",  [
                RequestOptions::JSON => [
                    "username" => $params["username"],
                    "password1"=>$params["password1"],
                    "password2"=>$params["password2"],
                    "portal_host"=>$params["portal_host"]
                ]]);
            $res->status_code=$res->getStatusCode();

            return $res;
        } catch (ClientException $exception){
            $exception->status_code=$exception->getResponse()->getStatusCode();
            return $exception;
        }
    }


    public static function auth($kugUrl){
        $user=array();
        if (isset($_SESSION["sessionID"]) && isset($_SESSION["userID"])){
            $user["user"]=self::getUser($_SESSION["sessionID"], $_SESSION["userID"], $kugUrl);
            $user["login"]=1;
        } else {
            $user["login"]=0;
        }
        error_log("AUTH_ARRAY");
        error_log(print_r($user, true));
        return $user;
    }


    private static function getUser($sessionId, $userId, $kugUrl){
        $client = new Client();
        error_log($sessionId);
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
            [
                'sessionID' => $sessionId
            ],
            'localhost'
        );
        //error_log($kugUrl);
        //$res = $client->request("GET", $kugUrl."users/id/".$userId.".json?l=de", ['sessionID' =>"fbd3033f418fb635c1d1900259363bc0"]);
        //error_log(print_r($res->getBody()->getContents(), true));
        $user=array();
        try {
            error_log("TRY");
            $res = $client->request("GET", $kugUrl."users/id/".$userId.".json?l=de", ['cookies' => $jar]);

            $user=json_decode($res->getBody()->getContents(), true);
            $user["status_code"]=$res->getStatusCode();
        } catch (ClientException $res){
            $user["status_code"]=$res->getResponse()->getStatusCode();
        }

        return $user;
    }

    private static function requestToKug($params, $kugUrl){
        $client = new Client();



        try {
            $res = $client->request('POST', $kugUrl."login?representation=json",  [
                RequestOptions::JSON => ["username" => $params["username"], "password"=>$params["password"], "authenticatorid"=>"1"]]);
            $res->status_code=$res->getStatusCode();

            return $res;
        } catch (ClientException $exception){
            $exception->status_code=$exception->getResponse()->getStatusCode();
            return $exception;
        }

    }





}