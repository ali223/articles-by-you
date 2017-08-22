<?php

namespace app;


$route = new Route();

$route->add('/'       	  ,    'PostsController@index');
$route->add('/index'   	  ,    'PostsController@index');
$route->add('/showpost'   ,    'PostsController@show');
$route->add('/search'     ,    'PostsController@search');


$route->add('/home'             ,   'UserPostsController@index');
$route->add('/editarticle'      ,   'UserPostsController@edit');
$route->add('/deletearticle'    ,   'UserPostsController@destroy');
$route->add('/profile'          ,   'UsersController@userProfile');
$route->add('/password'         ,   'UsersController@userPassword');
$route->add('/viewarticle'      ,   'UserPostsController@show');
$route->add('/newarticle'       ,   'UserPostsController@create');
$route->add('/updatearticle'    ,   'UserPostsController@update');

$route->add('/registrationform' ,   'UsersController@create');

$route->add('/createuser'       ,   'UsersController@store');

$route->add('/login'            ,   'UsersController@login');
$route->add('/dologin'          ,   'UsersController@dologin');
$route->add('/logout'           ,   'UsersController@logout');


$route->add('/error'            ,   'PagesController@error');
$route->add('/contact'          ,   'PagesController@contact');


return $route;