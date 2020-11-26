<?php

namespace App\Http\Controllers;
use App\Http\Authentication\Authenticator;
use App\Http\Operations\UserManager;
use App\Http\Operations\WebManager;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;
use Rareloop\Lumberjack\Post;
use Timber\Timber;
use Wikisource\Api\Wikisource;
use Wikisource\Api\WikisourceApi;


class SiteController extends BaseController
{



    public function showLandingPage(ServerRequest $request){

        $webInfo=WebManager::get($request);
        $user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
        return new TimberResponse('views/templates/landing.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
    }


    public function showKuratorium(ServerRequest $request){
        $webInfo=WebManager::get($request);
        $user=UserManager::get($request);
        $wsApi = new WikisourceApi();
        $data = $wsApi->fetchWikisources();

        //$user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
        return new TimberResponse('views/templates/kuratorium.twig', [ "webInfo"=>$webInfo, "user"=>$user, "data"=>$data]);
    }

    public function showKuratoriumEntry(ServerRequest $request){
        $webInfo=WebManager::get($request);
        $user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
        return new TimberResponse('views/templates/kuratorium_entry.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
    }



    public function showNews(ServerRequest $request){
        $webInfo=WebManager::get($request);
        $user=UserManager::get($request);
        if (isset($_SESSION["userid"])){
            $webInfo["login"]=true;
            error_log("UserID: ".$_SESSION["userid"]);
            $webInfo["data"]=$_SESSION["user"];
        }
        $context = Timber::get_context();
        $context['posts'] = Post::whereStatus('publish')
            ->limit(5)
            ->get();

        return new TimberResponse('views/templates/news.twig', ["context"=>$context, "webInfo"=>$webInfo, "user"=>$user]);
    }








}
