<?php


namespace App\Http\Operations;

use Rareloop\Lumberjack\Http\ServerRequest;

class WebInfo
{
    public static function get(ServerRequest $request){
        $webInfo["login"]=false;
        $webInfo["baseurl"]=$request->getServerParams()["WP_HOME"]."/";
        $webInfo["currentUrl"]=$request->getServerParams()["HTTP_REFERER"];
        setcookie("currentUrl", $request->getServerParams()["HTTP_REFERER"]);
        return $webInfo;
    }
}