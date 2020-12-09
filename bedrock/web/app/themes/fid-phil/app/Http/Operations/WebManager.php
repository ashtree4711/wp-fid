<?php


namespace App\Http\Operations;

use Rareloop\Lumberjack\Http\ServerRequest;

class WebManager
{
    public static function get(ServerRequest $request){
        session_start();
        $webInfo["login"]=false;
        $webInfo["baseurl"]=$request->getServerParams()["WP_HOME"]."/";
        if (isset($request->getServerParams()["HTTP_REFERER"])){
            $webInfo["currentUrl"]=$request->getServerParams()["WP_HOME"].$request->getServerParams()["REQUEST_URI"];
            setcookie("currentUrl", $request->getServerParams()["WP_HOME"].$request->getServerParams()["REQUEST_URI"]);
        } else {
            $webInfo["currentUrl"]=$request->getServerParams()["WP_HOME"].$request->getServerParams()["REQUEST_URI"];
            setcookie("currentUrl", $request->getServerParams()["WP_HOME"].$request->getServerParams()["REQUEST_URI"]);
        }


        return $webInfo;
    }
}