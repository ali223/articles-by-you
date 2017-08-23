<?php
namespace App;

use Exception;
use App\Controllers\PostsController;
use App\Controllers\UserPostsController;
use App\Controllers\UsersController;
use App\Controllers\PagesController;
use App\Controllers\View;
use App\Models\Database;
use App\Models\BlogPostDB;
use App\Models\BlogUserDB;
use App\Models\BlogCommentDB;

use App\Utilities\SessionUtility;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

class Route{

	private $routes = [];

	public function add($uri, $controllerName ) {
		$this->routes[$uri] = $controllerName;
	}

	public function direct($uri){
		if(array_key_exists($uri, $this->routes)) {

			$controller = explode('@', $this->routes[$uri])[0];
			$action = explode('@', $this->routes[$uri])[1];

			return $this->callAction($controller, $action);
		} 

			return $this->callAction("PagesController", "error");
	}

	protected function callAction($controller, $action) {

		switch($controller) {

	      case 'PagesController':
	          $view = new View("views/header.php", '', "views/footer.php");
	          $sessionUtility = new sessionUtility();
	          $controller = new PagesController($view, $sessionUtility);
	          break;

	      case 'PostsController':
	          $database = new Database(DB_DSN, DB_USER, DB_PASSWORD);
	          
	          $blogPostDatabase = new BlogPostDB($database);
	          $blogUserDatabase = new BlogUserDB($database);
	          $blogCommentDatabase = new BlogCommentDB($database);
	          
	          $view = new View("views/header.php", '', "views/footer.php");

	          $controller = new PostsController($blogUserDatabase, $blogPostDatabase, $blogCommentDatabase, $view);

	           break;

	      case 'UserPostsController':
	          $database = new Database(DB_DSN, DB_USER, DB_PASSWORD);
	          
	          $blogPostDatabase = new BlogPostDB($database);
	          $blogUserDatabase = new BlogUserDB($database);
	   
	   		  $sessionUtility = new SessionUtility();

	          $view = new View("views/header.php", '', "views/footer.php");

	          $controller = new UserPostsController($blogUserDatabase, $blogPostDatabase, $sessionUtility, $view);

	          break;

         case 'UsersController':

	          $database = new Database(DB_DSN, DB_USER, DB_PASSWORD);
	          
	          $blogPostDatabase = new BlogPostDB($database);
	          $blogUserDatabase = new BlogUserDB($database);
	          	  
	          $view = new View("views/header.php", '', "views/footer.php");

	          $sessionUtility = new sessionUtility();

	          $controller = new UsersController($blogUserDatabase, $blogPostDatabase, $view, $sessionUtility);

	           break;
	        }	
	
			if(! method_exists($controller, $action)) {
				throw new Exception("method not found");
			}
			return $controller->$action();
	}
	
}