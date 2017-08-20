<?php
namespace App;

use Exception;
use App\controllers\PostsController;
use App\controllers\UsersController;
use App\controllers\SessionUtility;
use App\controllers\PagesController;
use App\controllers\View;
use App\tools\Database;
use App\tools\BlogPostDB;
use App\tools\BlogUserDB;
use App\tools\BlogCommentDB;

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

         case 'UsersController':

	          $database = new Database(DB_DSN, DB_USER, DB_PASSWORD);
	          
	          $blogPostDatabase = new BlogPostDB($database);
	          $blogUserDatabase = new BlogUserDB($database);
	          $blogCommentDatabase = new BlogCommentDB($database);
	  
	          $view = new View("views/header.php", '', "views/footer.php");

	          $sessionUtility = new sessionUtility();

	          $controller = new UsersController($blogUserDatabase, $blogPostDatabase, $blogCommentDatabase, $view, $sessionUtility);

	           break;
	        }	
	
			if(! method_exists($controller, $action)) {
				throw new Exception("method not found");
			}
			return $controller->$action();
	}
	
}