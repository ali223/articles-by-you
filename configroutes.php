<?php

namespace BW;


$route = new Route();

$route->add('/'       	  ,    'PostsController@index');
$route->add('/index'   	  ,    'PostsController@index');
$route->add('/showpost'   ,    'PostsController@show');
$route->add('/search'     ,    'PostsController@search');


$route->add('/home'             ,    'UsersController@userhome');
$route->add('/editarticle'      ,    'UsersController@usereditarticle');
$route->add('/deletearticle'    ,    'UsersController@userdeletearticle');
$route->add('/profile'          ,    'UsersController@userprofile');
$route->add('/password'         ,    'UsersController@userpassword');
$route->add('/viewarticle'      ,    'UsersController@userviewarticle');
$route->add('/newarticle'       ,    'UsersController@usernewarticle');

$route->add('/registrationform' ,    'UsersController@userregistrationform');

$route->add('/createuser'       ,    'UsersController@create');

$route->add('/login'            ,    'UsersController@login');
$route->add('/dologin'          ,    'UsersController@dologin');
$route->add('/logout'           ,    'UsersController@logout');


$route->add('/error'            ,     'PagesController@error');
$route->add('/contact'          ,     'PagesController@contact');


return $route;