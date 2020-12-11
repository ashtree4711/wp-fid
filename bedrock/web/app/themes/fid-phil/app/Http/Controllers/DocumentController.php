<?php

namespace App\Http\Controllers;
use App\Http\Authentication\Authenticator;
use App\Http\Operations\KugCatalog;
use App\Http\Operations\UrlBuilder;
use App\Http\Operations\UserManager;
use App\Http\Operations\WebManager;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;

class DocumentController extends BaseController
{

    public function showDocument(ServerRequest $request, $recordId){
        $webInfo=WebManager::get($request);
        $user=UserManager::get($request);
        //$user=Authenticator::auth($request->getServerParams()["KUG_FID"]);

        $data=KugCatalog::getResultByUrl($recordId,  $request->getServerParams()["KUG_FID"]);


        if ($user["login"]==1){
            return new TimberResponse('views/templates/document_viewer.twig', ["data"=>$data, "webInfo"=>$webInfo, "user"=>$user]);
        }
        else {
            return new TimberResponse('views/templates/errors/401.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
        }

    }











}
