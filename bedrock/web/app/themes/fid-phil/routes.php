<?php

use Rareloop\Lumberjack\Facades\Router;

/**
 *  Catalog Routes
 */
Router::get('', 'SiteController@showHome');
Router::get('/portal', 'AuthController@forwardAfterLogin');
Router::get('/records/{recordId}', 'SiteController@showRecord');



/**
 * Misc. Routes
 */
Router::get('/news', 'SiteController@showNews');

/**
 * User Routes
 */
#Router::get('/me', 'UserConstroller:getProfile');


/**
 * Authentication Routes
 */
Router::post('/login', 'AuthController@login');
Router::get('/portal/login', 'AuthController@login');
Router::post('/logout', 'AuthController@logout');
Router::post('/signup', 'AuthController@signup');


/**
 * Sandbox Routes
 */
Router::get('/cookies', 'SiteController@showCookies');






