<?php
namespace App\Http\Operations;

class Facets
{
    public static function convert($kugFacets, $params, $baseurl){

        $facets=self::convertToArray($kugFacets, $params, $baseurl);


        return $facets;
    }


    private static function convertToArray($kugFacets, $params, $baseurl){
        $kugArray= (array) $kugFacets;
        $newArray=array();
        foreach ($kugArray as $key => $value) {
            if ($key=="mediatype"){
                $facet["key"]=$key;
                $facet["name_de"]="Medientypen";
                $facet["name_en"]="Media Types";
                $facet["selected"]=self::getSelected($key, $params, $baseurl);
                $facet["value"]=self::getFacetHits($key, $value, $params);
                array_push($newArray, $facet);
            }
            if ($key=="language"){
                $facet["key"]=$key;
                $facet["name_de"]="Sprachen";
                $facet["name_en"]="Languages";
                $facet["selected"]=self::getSelected($key, $params, $baseurl);
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

    private static function getSelected($key, $params, $baseurl){
        $selected=array();
        if (isset($params[$key])){
            $selected=self::createDisableUrl($key, $params, $baseurl);
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
        $selectionValues=$values;
        if (isset($params["page"])){
            unset($params["page"]);
        }
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
                        unset($selectionValues[$i]);
                    }
                }
            }
        }

        return $selectionValues;
    }

    private static function createDisableUrl($key, $params, $baseurl){
        $facetValues=array();
        if (isset($params["page"])){
            unset($params["page"]);
        }
        for ($i=0; $i<count($params[$key]); $i++ ){
            if (isset($params[$key])){
                foreach ($params[$key] as $index=>$paramValue){
                    $paramsDuplicate=$params;
                    if ($params[$key][$i]==$paramValue){
                        unset($paramsDuplicate[$key][$index]);
                        $facetValues[$i][0]=$params[$key][$i];
                        $facetValues[$i][1]=self::buildFidUrl($paramsDuplicate, $baseurl);
                    }

                }
            }
        }
        return $facetValues;
    }


    private static function buildFidUrl($params, $url){
        $url=$url."search?freetext=".$params["freetext"];
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