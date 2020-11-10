<?php
namespace App\Http\Operations;

class FacetManager
{
    public static function convert($kugFacets, $params, $kugUrl){

        $facets=self::convertToArray($kugFacets, $params, $kugUrl);
        error_log(print_r($params, true));

        return $facets;
    }


    private static function convertToArray($kugFacets, $params, $kugUrl){
        $kugArray= (array) $kugFacets;
        $newArray=array();
        foreach ($kugArray as $key => $value) {
            if ($key=="mediatype"){
                $facet["key"]=$key;
                $facet["name_de"]="Medientypen";
                $facet["name_en"]="Media Types";
                $facet["selected"]=self::getSelected($key, $params, $kugUrl);
                $facet["value"]=self::getFacetHits($key, $value, $params);
                array_push($newArray, $facet);
            }
            if ($key=="language"){
                $facet["key"]=$key;
                $facet["name_de"]="Sprachen";
                $facet["name_en"]="Languages";
                $facet["selected"]=self::getSelected($key, $params, $kugUrl);
                $facet["value"]=self::getFacetHits($key, $value, $params);
                array_push($newArray, $facet);
            }
            /*if ($key=="subject"){
                $facet["key"]=$key;
                $facet["name_de"]="Kategorien";
                $facet["name_en"]="Categories";
                error_log("SUBJECT");
                $facet["value"]=self::getFacetHits($key, $value, $params);
                array_push($newArray, $facet);

            }*/
        }


        return $newArray;

    }

    private static function getSelected($key, $params, $kugUrl){
        $selected=array();
        if (isset($params[$key])){
            $selected=self::createDisableUrl($key, $params, $kugUrl);
        }
        return $selected;

    }

    private static function getFacetHits($key, $facetValues, $params){
        //error_log(print_r($params, true));
        if ($facetValues) {
            $facetValues = self::reduceFacetHits($facetValues);
            $facetValues = self::createSelectUrl($key, $facetValues, $params);
        }
        //error_log(print_r($facetValues, true));

        return $facetValues;
    }

    private static function reduceFacetHits($facetValues){
        $maxFacetHits=20;

        if (count($facetValues)>$maxFacetHits){
            $reducesFacetsHits=array();
            for ($i=0; $i < $maxFacetHits; $i++){
                array_push($reducesFacetsHits, $facetValues[$i]);
            }
            return $reducesFacetsHits;
        } else {
            return $facetValues;
        }
    }


    private static function createSelectUrl($key, $values, $params){
        # just start if values available
        if ($values){
            for ($i=0; $i<count($values); $i++){
                # $chosen is indicator of the facet as already a param --> is chosen
                $chosen=false;
                if (isset($params[$key])){
                    for ($j=0; $j<count($params[$key]); $j++){
                        # if the current value is already chosen, turn true
                        if ($values[$i][0]==$params[$key][$j]){
                            $chosen=true;
                        }
                    }
                    # if chosen==true, unset it. It no longer a normal facet
                    if ($chosen){
                        unset($values[$i]);
                    }
                }
            }
        }

        return $values;
    }

    private static function createDisableUrl($key, $params, $kugUrl){
        $facetValues=array();
        //error_log(print_r($params, true));
        for ($i=0; $i<count($params[$key]); $i++ ){
            if (isset($params[$key])){
                foreach ($params[$key] as $index=>$paramValue){
                    $paramsDuplicate=$params;
                    unset($paramsDuplicate[$key][$index]);
                    $facetValues[$i][0]=$params[$key][$i];
                    $facetValues[$i][1]=self::buildFidUrl($paramsDuplicate, $kugUrl);
                }
            }
        }

       //error_log(print_r($facetValues, true));
        return $facetValues;
    }

    private static function createDisableUrl2($key, $facetValues, $params){
        for ($i=0; $i<count($facetValues); $i++ ){
            if (isset($params[$key])){
                foreach ($params[$key] as $index=>$paramValue){
                    $paramsDuplicate=$params;
                    if ($paramValue == $facetValues[$i][0]){
                        unset($paramsDuplicate[$key][$index]);
                        $facetValues[$i][2]=self::buildFidUrl($paramsDuplicate);
                    }
                }
            }
        }
        return $facetValues;
    }

    private static function buildFidUrl($params, $url){
        $url=$url."?freetext=".$params["freetext"];
        if (isset($params["mediatype"])){
            foreach ($params["mediatype"] as $mediatype){
                $url=$url."&mediatype[]=".$mediatype;
            }
        }
        if (isset($params["language"])){
            foreach ($params["language"] as $language){
                $url=$url."&language[]=".$language;
            }
        }

        // 'Umlaute'-translation not good
        if (isset($params["subject"])){
            foreach ($params["subject"] as $subject){
                $url=$url."&subject[]=".$subject;
            }
        }
        return $url;
    }

}