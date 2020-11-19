<?php
namespace App\Http\Operations;

class Pagination
{
    public static function create($meta){
        $pagination["current"]=$meta->page;
        $pagination["first"]=self::firstPage();
        $pagination["last"]=self::lastPage($meta);
        $pagination["previous"]=self::previousPage($meta);
        $pagination["next"]=self::nextPage($meta);
        $pagination["all"]=self::allPages($meta);
        return $pagination;
    }

    private static function firstPage(){
        return 1;
    }

    private static function lastPage($meta){

        return round($meta->hits / $meta->num);
    }

    private static function previousPage($meta){
        if ($meta->page == 1){
            return 1;
        } else {
            return $meta->page-1;
        }
    }

    private static function nextPage($meta){
        if ($meta->page == self::lastPage($meta)){
            return $meta->page;
        } else {
            return $meta->page+1;
        }
    }

    private static function allPages($meta){
        $pages=array();
        for ($i=1; $i<=self::lastPage($meta); $i++){
            array_push($pages, $i);
        }
        return $pages;
    }


}