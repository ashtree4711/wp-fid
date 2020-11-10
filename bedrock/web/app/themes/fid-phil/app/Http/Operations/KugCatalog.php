<?php
namespace App\Http\Operations;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class KugCatalog
{
    public static function getResults($params, $kugUrl){
        $results=array();
        if (isset($params["freetext"])){
            $results=self::search($params, $kugUrl);
        }
        if ($results!=null){
            $results->facets=FacetManager::convert($results->facets, $params);
        }
        return $results;
    }

    /**
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getResultByUrl($url){
        $client = new Client();
        $res = $client->request("GET", $url);
        return json_decode($res->getBody()->getContents());
    }

    private static function buildQueryUrl($params, $kugUrl){
        $url=$kugUrl."search.json?l=de;db=eds";
        $url=$url."&fs=".$params["freetext"];
        if (isset($params["mediatype"])){
            foreach ($params["mediatype"] as $mediatype){
                $url=$url.";f[ftyp]=".$mediatype;
            }
        }
        if (isset($params["language"])){
            foreach ($params["language"] as $language){
                $url=$url.";f[flang]=".$language;
            }
        }
        // 'Umlaute'-translation not good
        if (isset($params["subject"])){
            foreach ($params["subject"] as $subject){
                $url=$url.";f[fsubj]=".$subject;
            }
        }
        if (isset($params["hits"])){
            $url=$url.";num=".$params["hits"];
        }
        if (isset($params["sort"])){
            $url=$url.";srt=".$params["sort"];
        }

        return $url;
    }

    /**
     * @param $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function search($params, $kugUrl){
        $url=self::buildQueryUrl($params, $kugUrl);
        $client = new Client();
        $res = $client->request("GET", $url);
        return json_decode($res->getBody()->getContents());

    }
}