<?php

namespace App\Http\Controllers;
use App\Http\Authentication\Authenticator;
use App\Http\Operations\KugCatalog;
use App\Http\Operations\UserManager;
use App\Http\Operations\WebManager;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\RedirectResponse;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;
use Timber\User;

class TempController extends BaseController
{


    public function newLeafletEntry(ServerRequest $request, $id){
        $webInfo=WebManager::get($request);
        UserManager::update($request);
        if (isset($_SESSION["leaflets"])){
            array_push($_SESSION["leaflets"], $id);
        } else {
            $_SESSION["leaflets"] = [];
            array_push($_SESSION["leaflets"], $id);
        }
        $user=UserManager::get($request);


        error_log(print_r($request->getAttributes(), true));
        error_log(print_r($_SESSION["leaflets"], true));


        //return new TimberResponse('views/templates/home.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
        return new TimberResponse('views/navbar.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
    }

    public function removeLeafletEntry(ServerRequest $request, $id){
        $webInfo=WebManager::get($request);
        $user=UserManager::get($request);

        if (isset($_SESSION["leaflets"])){
            $_SESSION["leaflets"]=array_diff($_SESSION["leaflets"], array($id));
            $_SESSION["leaflets"]=array_values($_SESSION["leaflets"]);
        } else {

        }
        $user["leaflets_full"]=[];
        for ($i=0; $i<count($_SESSION["leaflets"]); $i++) {

            array_push($user["leaflets_full"], KugCatalog::getResultByUrl("httP://localhost:20445/portal/fidphil/users/id/11/databases/id/eds/titles/id/".$_SESSION["leaflets"][$i].".json"));
        }
        $webInfo["subroute"]="leaflets";
        return new TimberResponse('views/templates/user/profile.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
    }










}
