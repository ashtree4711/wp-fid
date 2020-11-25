<?php


namespace App\Http\Operations;

use App\Http\Authentication\Authenticator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Rareloop\Lumberjack\Http\ServerRequest;

class UserManager
{
    public static function get(ServerRequest $request){
        $user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
        return $user;
    }

    public static function update(ServerRequest $request){
        self::updateUserToKug($request->getServerParams()["KUG_FID"], $request->getParsedBody());
        return;
    }

    private static function updateUserToKug($kugUrl, $params){
        error_log($kugUrl);
        error_log($_SESSION["userID"]);
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
            [
                'sessionID' => $_SESSION["sessionID"]
            ],
            'localhost'
        );


        $client=new Client();


        try {
            error_log("TRY");
            $res=$client->request('PUT', $kugUrl.'users/id/'.$_SESSION["userID"], [
                'cookies' => $jar,
                'form_params' => $params
                ]
            );

            error_log(print_r($res, true));
            $user["status_code"]=$res->getStatusCode();
        } catch (ClientException $res){
            error_log("EXCEPTION");
            error_log(print_r($res, true));
        }




        return;

    }


}