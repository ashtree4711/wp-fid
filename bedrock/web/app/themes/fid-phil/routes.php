<?php

use Rareloop\Lumberjack\Facades\Router;

/**
 *  General Routes
 */
Router::get('', 'SiteController@showLandingPage');
Router::get('/impressum', 'SiteController@showLandingPage');
Router::get('/kuratorium', 'SiteController@showKuratorium');
Router::get('/kuratorium/{author}', 'SiteController@showKuratoriumEntry');


Router::get('/news', 'SiteController@showNews');
Router::get('/partner', 'SiteController@showLandingPage');

/**
 * Index / Search
 */
Router::get('/search', 'SearchController@showResults');
Router::get('/records/{recordId}', 'SearchController@showRecord');


/**
 * Documents / Viewer
 */
Router::get('/records/{recordId}/show', 'DocumentController@showDocument');


/**
 * User Routes
 */
Router::get('/me', 'UserController@showProfile');
Router::post('/me', 'UserController@updateProfile');
Router::get('/signup', 'UserController@signup');


/**
 * Authentication Routes
 */
//Router::get('/portal', 'AuthController@forwardAfterLogin');
Router::post('/login', 'AuthController@login');
Router::get('/portal/login', 'AuthController@login');
Router::post('/logout', 'AuthController@logout');
Router::post('/signup', 'AuthController@signup');
Router::get('/confirm/{registrationkey}', 'AuthController@confirm');


/**
 * Sandbox Routes
 */
Router::post('/leaflets/{id}', 'TempController@newLeafletEntry');
Router::delete('/leaflets/{id}', 'TempController@removeLeafletEntry');
//Router::get('/cookies', 'SiteController@showCookies');






