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


}