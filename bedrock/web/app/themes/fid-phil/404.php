<?php

/**
 * The template for displaying 404 pages (Not Found)
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

namespace App;

use App\Http\Controllers\Controller;
use App\Http\Operations\UserInfo;
use App\Http\Operations\WebInfo;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Http\ServerRequest;
use Timber\Timber;

/**
 * Class names can not start with a number so the 404 controller has a special cased name
 */
class Error404Controller extends Controller
{
    public function handle(ServerRequest $request)
    {
        $webInfo=WebInfo::get($request);
        $userInfo=UserInfo::get($request);
        return new TimberResponse('templates/errors/404.twig', ["webInfo"=>$webInfo, "userInfo"=>$userInfo], 404);
    }
}
