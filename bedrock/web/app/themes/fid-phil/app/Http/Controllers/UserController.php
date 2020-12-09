<?php

namespace App\Http\Controllers;
use App\Http\Authentication\Authenticator;
use App\Http\Operations\KugCatalog;
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
       $user["leaflets_full"]=[];
       if (isset($_SESSION["leaflets"])){
           for ($i=0; $i<count($_SESSION["leaflets"]); $i++) {

               array_push($user["leaflets_full"], KugCatalog::getResultByUrl($request->getServerParams()["KUG_FID"]."users/id/".$_SESSION["userID"]."/databases/id/eds/titles/id/".$_SESSION["leaflets"][$i].".json"));
           }
       }


       // MOCKUP
       if (isset($request->getQueryParams()["subroute"])){
           $webInfo["subroute"]=$request->getQueryParams()["subroute"];
           error_log(print_r($webInfo["subroute"], true));
       }

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

    public function signup(ServerRequest $request){
        $webInfo=WebManager::get($request);
        $user=UserManager::get($request);
        //$user=$user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
        return new TimberResponse('views/templates/user/registration.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
    }










}
