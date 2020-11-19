<?php

namespace App\Http\Controllers;

use App\Http\Authentication\Authenticator;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\RedirectResponse;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;


class AuthController extends BaseController
{

    public function login(ServerRequest $request){



        $params=$request->getParsedBody();
        error_log(print_r($request->getParsedBody(), true));
        $user["firstname"]="Mark";
        $user["lastname"]="Eschweiler";
        $response=Authenticator::login($params, $request->getServerParams()["KUG_FID"]);

        /*$creds=$request->getParsedBody();
        $client = new Client();
        $res = $client->request("POST", "localhost:20445/portal/fidphil/login", ["form_params"=>[
            "username" => $creds["username"],
            "password" => $creds["password"],
            "authenticatorid" => "1",
            "redirect_to" => "http%3A%2F%2Flocalhost%3A8000%2Fportal"
        ]
        ]);
*/
        //error_log(print_r($res->getBody()->getContents(), true));



        if ($response->status_code == 200){
            $user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
            return new TimberResponse('views/templates/auth/signin.twig', ["status_code"=>200, "user"=>$user]);
        } else {
            return new TimberResponse('views/templates/auth/signin.twig', ["status_code"=>$response->status_code]);
        }
    }

    public function forwardAfterLogin(ServerRequest $request){
        setcookie("fidsession", $_COOKIE["sessionID"]);
        setcookie("fiduser", $request->getQueryParams()["userid"]);
        return new RedirectResponse($request->getServerParams()["HTTP_REFERER"]);

    }

    public function logout(ServerRequest $request){


        // http://localhost:20445/portal/fidphil/logout.html?l=de
        error_log(print_r($request, true));
        return;
    }

    public function signup(ServerRequest $request){
        error_log(print_r($request, true));
        $success=true;
        $params = $request->getParsedBody();
        if ($success){
            return new TimberResponse('views/templates/auth/success-signup.twig', ["email"=>$params["email"]]);
        } else {
            return;
        }

    }





}
