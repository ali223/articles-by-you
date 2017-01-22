<?php

namespace BW;
use BW\controllers\sessionUtility;


use BW\controllers\PagesController;

spl_autoload_register();

echo $_SERVER['REQUEST_URI'];



$route = new Route();

$route->add('/');

$route->add('/about');

$route->add('/contact');

echo "<pre>", print_r($route), "</pre>";

$route->submit();

require "config.php";

//  $controllers = array('posts' => ['index', 'show','search'], 
//      'users' => ['userhome','usereditarticle','userdeletearticle', 'userprofile','userpassword',
//                 'userviewarticle','usernewarticle','userregistrationform', 'create', 'login', 'dologin','logout'],
//                 'pages'=>['contact'] );


// if(isset($_GET['controller']) && isset($_GET['action'])) {

//         $controller = htmlspecialchars(strtolower($_GET['controller']));

//         $action = htmlspecialchars(strtolower($_GET['action']));

// } else {

//         $controller = 'posts';

//         $action = 'index';

// }



// if (array_key_exists($controller, $controllers)) {

//     if (in_array($action, $controllers[$controller])) {

//       call($controller, $action);

//     } else {

//       call('pages', 'error');

//     }

//   } else {

//     call('pages', 'error');

//   }



// function call($controller, $action) {

//    // create a new instance of the needed controller
    
//     // $database = new tools\Database(DB_DSN, DB_USER, DB_PASSWORD);
    
//     // $blogPostDatabase = new tools\blogpostdb($database);
//     // $blogUserDatabase = new tools\bloguserdb($database);
//     // $blogCommentDatabase = new tools\blogcommentdb($database);
    
//     // $view = new controllers\View("views/header.php", '', "views/footer.php");

//     // $sessionUtility = new sessionUtility();
    

//     switch($controller) {

//       case 'pages':

//           $view = new controllers\View("views/header.php", '', "views/footer.php");

//           $sessionUtility = new sessionUtility();

//           $controller = new controllers\PagesController($view, $sessionUtility);

//           break;

//       case 'posts':

//           $database = new tools\Database(DB_DSN, DB_USER, DB_PASSWORD);
          
//           $blogPostDatabase = new tools\blogpostdb($database);

//           $blogUserDatabase = new tools\bloguserdb($database);


//           $blogCommentDatabase = new tools\blogcommentdb($database);
          
//           $view = new controllers\View("views/header.php", '', "views/footer.php");


//           $controller = new controllers\PostsController($blogUserDatabase, $blogPostDatabase, $blogCommentDatabase, $view);

//            break;

//       case 'users':

//           $database = new tools\Database(DB_DSN, DB_USER, DB_PASSWORD);
          
//           $blogPostDatabase = new tools\blogpostdb($database);

//           $blogUserDatabase = new tools\bloguserdb($database);

//           $blogCommentDatabase = new tools\blogcommentdb($database);
          
//           $view = new controllers\View("views/header.php", '', "views/footer.php");

//           $sessionUtility = new sessionUtility();



//          $controller = new controllers\UsersController($blogUserDatabase, $blogPostDatabase, $blogCommentDatabase, $view, $sessionUtility);

//          break;

        

//     }



//     // call the action

//     $controller->$action();

//   }

        

/*

switch($action)  {

    case 'getpost':

           $getpostid = htmlspecialchars(strtolower($_GET['postid']));

echo "<br /> action is $action post id is $getpostid";

           break;

    default:

                $blogpostdbobj = new tools\blogpostdb($db); 

                $result = $blogpostdbobj->getAllPosts();

                include("homepage.php");             

}



*/









?>



