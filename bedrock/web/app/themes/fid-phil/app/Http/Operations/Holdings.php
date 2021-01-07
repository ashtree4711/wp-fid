<?php


namespace App\Http\Operations;

use Rareloop\Lumberjack\Http\ServerRequest;

class Holdings
{
    public static function get(ServerRequest $request){
        $dbHost=$request->getServerParams()["DB_FID_HOST"];
        $dbName=$request->getServerParams()["DB_FID_NAME"];
        $dbPW=$request->getServerParams()["DB_FID_PW"];
        $dbUser=$request->getServerParams()["DB_FID_USER"];

        $conn = new \mysqli($dbHost, $dbUser, $dbPW);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql="SELECT t.* FROM fidphil.holdings_isil t ORDER BY location ASC";
        $result = $conn->query($sql);
        $holdings = [];
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                array_push($holdings, $row);
            }
        } else {
            error_log( "0 results");
        }
        $conn->close();


        return $holdings;
    }
}