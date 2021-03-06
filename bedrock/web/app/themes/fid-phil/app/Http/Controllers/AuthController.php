<?php

namespace App\Http\Controllers;

use App\Http\Authentication\Authenticator;
use App\Http\Operations\WebManager;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\RedirectResponse;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;


class AuthController extends BaseController
{

    public function login(ServerRequest $request){
        $webInfo=WebManager::get($request);
        $params=$request->getParsedBody();
        error_log(print_r($request->getParsedBody(), true));
        $response=Authenticator::login($params, $request->getServerParams()["KUG_FID"]);
        if ($response->status_code == 200){
            $user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
            return new TimberResponse('views/templates/auth/signin.twig', ["webInfo"=>$webInfo,"status_code"=>200, "user"=>$user, "redirect_to"=>$params["redirect_to"]]);
        } else {
            return new TimberResponse('views/templates/auth/signin.twig', ["webInfo"=>$webInfo, "status_code"=>$response->status_code]);
        }
    }


    public function logout(ServerRequest $request){
        $webInfo=WebManager::get($request);
        error_log(print_r($request->getParsedBody(), true));
        $kugUrl=$request->getServerParams()["KUG_FID"];
        $user=Authenticator::logout($kugUrl);
        //return new TimberResponse('views/templates/auth/success-logout.twig', ["webInfo"=>$webInfo, "user"=>$user]);
        return new RedirectResponse($webInfo["baseurl"]."me");
    }

    public function signup(ServerRequest $request){
        $webInfo=WebManager::get($request);
        $params = $request->getParsedBody();
        //error_log(print_r($request, true));
        $response=Authenticator::signup($request->getServerParams()["KUG_FID"], $params);
        $temp=json_decode($response->getBody()->getContents(), true);
        error_log(print_r($temp, true));



        $success=true;

        if ($success){
            return new TimberResponse('views/templates/auth/success-signup.twig', ["webInfo"=>$webInfo,"email"=>$params["username"], "temp"=>$temp]);
        } else {
            return "success=0";
        }
    }

    public function confirm(ServerRequest $request, $registrationkey){
        $webInfo=WebManager::get($request);

        error_log(print_r($registrationkey, true));
        $response=Authenticator::confirm($request->getServerParams()["KUG_FID"], $registrationkey);
        $success=true;
        //http://localhost:8000/confirm/062253b03f714c1ce120de3197a6832c
        if ($success){
            return new TimberResponse('views/templates/auth/confirm.twig', ["webInfo"=>$webInfo]);
        } else {
            return "success=0";
        }
    }





}
