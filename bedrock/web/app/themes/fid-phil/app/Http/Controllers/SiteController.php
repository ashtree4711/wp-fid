<?php

namespace App\Http\Controllers;
use App\Http\Authentication\Authenticator;
use App\Http\Operations\KugCatalog;
use App\Http\Operations\UserManager;
use App\Http\Operations\WebManager;
use GuzzleHttp\Client;
use PHPHtmlParser\Dom;
use Rareloop\Lumberjack\Http\Controller as BaseController;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;
use Rareloop\Lumberjack\Post;
use simplehtmldom_1_5\simple_html_dom;
use function simplehtmldom_1_5\str_get_html;
use Timber\Timber;
use simplehtmldom_1_5\simple_html_dom as simple;


class SiteController extends BaseController
{



    public function showLandingPage(ServerRequest $request){

        $webInfo=WebManager::get($request);
        $user=UserManager::get($request);
        return new TimberResponse('views/templates/landing.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
    }


    public function showKuratorium(ServerRequest $request){
        $webInfo=WebManager::get($request);
        $user=UserManager::get($request);


        //$user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
        return new TimberResponse('views/templates/kuratorium.twig', [ "webInfo"=>$webInfo, "user"=>$user]);
    }

    public function showKuratoriumEntry(ServerRequest $request, $author){
        $webInfo=WebManager::get($request);
        $user=Authenticator::auth($request->getServerParams()["KUG_FID"]);
        $params["freetext"]="Kant";
        $data=KugCatalog::getResults($params, $request->getServerParams()["KUG_FID"], $webInfo["baseurl"]);
        $client = new Client();
        $res = $client->request("GET", "https://lobid.org/gnd/118559796.json" );
        $gnd = json_decode($res->getBody()->getContents(), true);
        $client = new Client();
        $res = $client->request("GET", "https://www.wikidata.org/wiki/Special:EntityData/Q9312.json" );
        $wikidata = json_decode($res->getBody()->getContents(), true);
        $client = new Client();
        $res = $client->request("GET", "https://de.wikisource.org/w/api.php?action=parse&page=Immanuel_Kant&prop=text&formatversion=1&format=json" );
        $wikisource = json_decode($res->getBody()->getContents(), true);


        $wikisource=$wikisource["parse"]["text"]["*"];
        //error_log($wikisource);
        $dom = new \DOMDocument();
        $dom->loadHTML($wikisource);
        $array_data=[];
        $index = 0;
        $array_data=$this->showDOMNode($dom, $array_data, $index);

        //error_log(print_r($array_data, true));
        $data->gnd=$gnd;
        $data->wikidata=$wikidata;
        $data->wikisource=$array_data;




        return new TimberResponse('views/templates/kuratorium_entry.twig', [ "webInfo"=>$webInfo, "user"=>$user, "data"=>$data]);
    }

    function showDOMNode(\DOMNode $domNode, $array_data, $index) {
        foreach ($domNode->childNodes as $node)
        {


            if ($node->nodeName == "h2"){
                if ($node->nodeValue != "Inhaltsverzeichnis" and $node->nodeValue != "Weblinks[Bearbeiten]"){

                    $pattern = "[Bearbeiten]";
                    $val=preg_replace($pattern, "", $node->nodeValue);
                    $val=preg_replace('/\[.*\]/', '', $val);
                    //error_log(print_r($node->nodeName.':'.$val, true));
                    $element["cat"]=$val;
                    $element["entries"]=[];

                    //error_log($index);
                    array_push($array_data, $element);
                    $index=$index+1;
                }
            }

            if ($node->nodeName == "li"){
                if (isset($array_data[$index-1]["entries"])){
                    //error_log($index." | ".$node->nodeName." | ".$node->nodeValue);
                    array_push($array_data[$index-1]["entries"], $node->nodeValue);
                }


            }

            if($node->hasChildNodes()) {
                $array_data=$this->showDOMNode($node, $array_data, $index);
            }
        }

        return $array_data;
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
