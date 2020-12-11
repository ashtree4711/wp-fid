<?php
namespace App\Http\Operations;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use function Zend\Diactoros\parseCookieHeader;

class KugCatalog
{
    public static function getResults($params, $kugUrl, $baseurl){
        $results=array();
        if (isset($params["freetext"])){
            $results=self::search($params, $kugUrl);
        }
        if ($results!=null){
            $results->facets=Facets::convert($results->facets, $params, $baseurl);
            $results->pagination=Pagination::create($results->meta, $baseurl);
            $results->pagination["url"]=UrlBuilder::build($params, $baseurl, true, true, true, true, true, false)."&page=";
        }
        return $results;
    }

    /**
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getResultByUrl($recordId, $kugFid){
        $client = new Client();
        $res = $client->request("GET", $kugFid."databases/id/eds/titles/id/".$recordId.".json");
        $data = json_decode($res->getBody()->getContents(), true);

        $data["availability"]=self::getAvailability($data);
        $data["record_id"]=$recordId;
        return $data;
    }

    private static function getAvailability($data){
        if (isset($data["fields"]["T0585"][0]["content"])){
            $isxn = $data["fields"]["T0585"][0]["content"];
            $isxLink="http://193.30.112.134/F/?func=find-c&ccl_term=ISN=".$isxn;
        } elseif (isset($data["fields"]["T0540"][0]["content"])) {
            $isxn = $data["fields"]["T0540"][0]["content"];
            $isxLink="http://193.30.112.134/F/?func=find-c&ccl_term=IBN=".$isxn;
        } else {
            $isxn = "4711";
            $isxLink = "";
        }
        error_log($isxn);
        $url = "https://fernleihe.boss.bsz-bw.de/Holding/Query?isxn=".$isxn."&network=HBZ";
        $client = new Client();
        $res = $client->request("GET", $url );

        $response = json_decode($res->getBody()->getContents(), true);
        error_log(print_r($response, true));
        if ($response["numfound"]!=0){
            $holdings=array_values(array_values($response)[0])[0];

            error_log(print_r($holdings, true));
        } else {
            $holdings=[];
        }
        $returnData["holdings"]=$holdings;
        $returnData["hbz_link"]=$isxLink;




        return $returnData;

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
        // conceptional ignoring
        /*if (isset($params["subject"])){
            foreach ($params["subject"] as $subject){
                $url=$url.";f[fsubj]=".$subject;
            }
        }*/
        if (isset($params["hits"])){
            $url=$url.";num=".$params["hits"];
        }
        if (isset($params["sort"])){
            $url=$url.";srt=".$params["sort"];
        }
        if (isset($params["fulltext"])){
            $url=$url.";fulltext=".$params["fulltext"];
        }
        if (isset($params["page"])){
            $url=$url.";page=".$params["page"];
        }

        error_log(print_r($url, true));

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
        if (isset($_SESSION["sessionID"])){
            $jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
                [
                    'sessionID' => $_SESSION["sessionID"]
                ],
                'localhost'
            );
            //error_log(print_r("LOCAL COOKIE:" .$_COOKIE["sessionID"], true));
            try {
                $res = $client->request("GET", $url, ['cookies' => $jar]);
                error_log("HAS_COOKIE: ".$_SESSION["sessionID"]);
                error_log(print_r(parseCookieHeader($res->getHeaders()["Set-Cookie"][0]), true));
                //setcookie("sessionID", parseCookieHeader($res->getHeaders()["Set-Cookie"][0])["sessionID"]);


            } catch (ClientException $exception){

                error_log(print_r($exception->getResponse()->getStatusCode(), true));
            }
            return json_decode($res->getBody()->getContents());
            } else {
           $jar=array();
            try {
                $res = $client->request("GET", $url, ['cookies' => $jar]);
                error_log("HAS_NO_COOKIE");
                error_log(print_r(parseCookieHeader($res->getHeaders()["Set-Cookie"][0]), true));
                $_SESSION["sessionID"]=parseCookieHeader($res->getHeaders()["Set-Cookie"][0])["sessionID"];

            } catch (ClientException $exception){

                error_log(print_r($exception->getResponse()->getStatusCode(), true));
            }
            return json_decode($res->getBody()->getContents());

        }



    }
}