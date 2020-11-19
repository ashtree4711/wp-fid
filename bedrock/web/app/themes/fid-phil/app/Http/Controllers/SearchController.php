<?php

namespace App\Http\Controllers;
use App\Http\Operations\KugCatalog;
use App\Http\Operations\UrlBuilder;
use App\Http\Operations\UserInfo;
use App\Http\Operations\WebInfo;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;

class SearchController extends BaseController
{
    /**
     * @param ServerRequest $request
     * @return TimberResponse
     */
    public function showResults(ServerRequest $request){
        $webInfo=WebInfo::get($request);
        $userInfo=UserInfo::get($request);
        $params=$request->query();
        $data=KugCatalog::getResults($params, $request->getServerParams()["KUG_FID"], $webInfo["baseurl"]);
        $data = json_decode(json_encode($data), true);
        $data["params"]=$params;
        $webInfo["noPageUrl"]=UrlBuilder::build($params, $webInfo["baseurl"], true, true, true, true, true, false );
        return new TimberResponse('views/templates/home.twig', ["data"=>$data, "webInfo"=>$webInfo, "userInfo"=>$userInfo]);
    }

    /**
     * @param ServerRequest $request
     * @param $recordId
     * @return TimberResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function showRecord(ServerRequest $request, $recordId){
        $webInfo=WebInfo::get($request);
        $userInfo=UserInfo::get($request);
        $data=KugCatalog::getResultByUrl($request->getQueryParams()["href"]);
        return new TimberResponse('views/templates/single-record.twig', ["data"=>$data, "webInfo"=>$webInfo, "userInfo"=>$userInfo]);
    }










}
