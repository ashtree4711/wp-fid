<?php

namespace App\Http\Controllers;
use App\Http\Authentication\Authenticator;
use App\Http\Operations\UserManager;
use App\Http\Operations\WebManager;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;
use Timber\User;

class UserController extends BaseController
{
   public function showProfile(ServerRequest $request){
       $webInfo=WebManager::get($request);
       $user=UserManager::get($request);
       //$user=$user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
       return new TimberResponse('views/templates/user/profile.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
   }

    public function updateProfile(ServerRequest $request){
        $webInfo=WebManager::get($request);
        UserManager::update($request);
        $user=UserManager::get($request);

        error_log("TSSTDTSS");
        error_log(print_r($request->getParsedBody(), true));
        //$user=$user=Authenticator::auth($request->getServerParams()["KUG_FID"]);

        return new TimberResponse('views/templates/user/profile.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
    }










}
