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

class SearchController extends BaseController
{
    /**
     * @param ServerRequest $request
     * @return TimberResponse
     */
    public function showResults(ServerRequest $request){
        $webInfo=WebManager::get($request);
        $user=UserManager::get($request);
        //$user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
        $params=$request->query();
        $data=KugCatalog::getResults($params, $request->getServerParams()["KUG_FID"], $webInfo["baseurl"]);
        $data = json_decode(json_encode($data), true);
        $data["params"]=$params;
        $webInfo["noPageUrl"]=UrlBuilder::build($params, $webInfo["baseurl"], true, true, true, true, true, false );
        return new TimberResponse('views/templates/records.twig', ["data"=>$data, "webInfo"=>$webInfo, "user"=>$user]);
    }

    /**
     * @param ServerRequest $request
     * @param $recordId
     * @return TimberResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function showRecord(ServerRequest $request, $recordId){
        $webInfo=WebManager::get($request);
        $user=UserManager::get($request);
        $data=KugCatalog::getResultByUrl($request->getQueryParams()["href"]);
        return new TimberResponse('views/templates/search/single-record.twig', ["data"=>$data, "webInfo"=>$webInfo, "user"=>$user]);
    }










}
