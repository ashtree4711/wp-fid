<?php

namespace App\Http\Controllers;
use App\Http\Operations\KugCatalog;
use App\Http\Operations\UserInfo;
use App\Http\Operations\WebInfo;
use GuzzleHttp\Client;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;

use Rareloop\Lumberjack\Post;
use Timber\Timber;

class SiteController extends BaseController
{



    public function showHome(ServerRequest $request){
        //error_log(print_r($request, true));
        $webInfo=WebInfo::get($request);
        $userInfo=UserInfo::get($request);




        if (isset($request->getServerParams()["HTTP_HX_CURRENT_URL"])){
            error_log(print_r($request->getServerParams()["HTTP_HX_CURRENT_URL"], true));
        }
        $params=$request->query();

        $data=KugCatalog::getResults($params, $webInfo["currentUrl"]);
        $data = json_decode(json_encode($data), true);
        $data["params"]=$params;
        error_log(print_r($userInfo, true));
        if (isset($request->getServerParams()["HTTP_HX_CURRENT_URL"]) or isset($params["freetext"])){
            return new TimberResponse('views/templates/home.twig', ["data"=>$data, "webInfo"=>$webInfo, "userInfo"=>$userInfo]);
        } else {
            return new TimberResponse('views/templates/landing.twig', ["data"=>$data, "webInfo"=>$webInfo, "userInfo"=>$userInfo]);
        }



    }

    public function showRecord(ServerRequest $request, $recordId){
        $webInfo=WebInfo::get($request);
        $userInfo=UserInfo::get($request);
        $data=KugCatalog::getResultByUrl($request->getQueryParams()["href"]);
        return new TimberResponse('views/templates/single-record.twig', ["data"=>$data, "webInfo"=>$webInfo, "userInfo"=>$userInfo]);
    }



    public function showNews(ServerRequest $request){
        $webInfo=WebInfo::get($request);
        $userInfo=UserInfo::get($request);
        session_start();
        if (isset($_SESSION["userid"])){
            $webInfo["login"]=true;
            error_log("UserID: ".$_SESSION["userid"]);
            $webInfo["data"]=$_SESSION["user"];
        }
        $context = Timber::get_context();
        $context['posts'] = Post::whereStatus('publish')
            ->limit(5)
            ->get();

        return new TimberResponse('views/templates/news.twig', ["context"=>$context, "webInfo"=>$webInfo, "userInfo"=>$userInfo]);
    }


    public function showCookies(ServerRequest $request){

        /*$curl = curl_init("http://localhost:20445/portal/fidphil/users/id/3.json?l=de");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Cookie: sessionID=".$_COOKIE["sessionID"]));
        $c_response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($httpCode == 404) {
        } else {
            //error_log(print_r(json_decode($c_response), true));
        }
        curl_close($curl);

        ;*/
        //error_log($_COOKIE["sessionID"]);
        $client = new Client();
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
            [
                'sessionID' => $_COOKIE["fidsession"]
            ],
            'localhost'
        );
        $res = $client->request("GET", "http://localhost:20445/portal/fidphil/users/id/".$_COOKIE["fiduser"].".json?l=de", ['cookies' => $jar]);

        error_log(print_r($res->getBody()->getContents(), true));

        return "fsdsds";

    }





}
