<?php

namespace App\Http\Controllers;
use App\Http\Authentication\Authenticator;
use App\Http\Operations\UserManager;
use App\Http\Operations\WebManager;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;

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
        $user=UserManager::get($request);
        //$user=$user=Authenticator::auth($request->getServerParams()["KUG_FID"]);

        return new TimberResponse('views/templates/user/profile.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
    }










}
