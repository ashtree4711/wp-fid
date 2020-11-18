<?php
namespace App\Http\Operations;

class UrlBuilder
{
    public static function build($params, $baseUrl, $mediatype=true, $language=true, $hits=true, $sort=true, $fulltext=true, $pages=true){
        $url=$baseUrl."search?";
        $url=$url."&freetext=".$params["freetext"];
        if ($mediatype){
            if (isset($params["mediatype"])){
                foreach ($params["mediatype"] as $mediatype){
                    $url=$url."&mediatype[]=".$mediatype;
                }
            }
        }
        if ($language){
            if (isset($params["language"])){
                foreach ($params["language"] as $language){
                    $url=$url."&language[]=".$language;
                }
            }
        }
        if ($hits){
            if (isset($params["hits"])){
                $url=$url."&hits=".$params["hits"];
            }
        }
        if ($sort){
            if (isset($params["sort"])){
                $url=$url."&sort=".$params["sort"];
            }
        }
        if ($fulltext){
            if (isset($params["fulltext"])){
                $url=$url."&fulltext=".$params["fulltext"];
            }
        }
        if ($pages){
            if (isset($params["page"])){
                $url=$url."&page=".$params["page"];
            }
        }


        error_log(print_r($url, true));

        return $url;
    }


}