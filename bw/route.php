<?php
namespace BW;
use BW\controllers\PostsController;

use BW\controllers\SessionUtility;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';


class Route{

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

	          $view = new controllers\View("views/header.php", '', "views/footer.php");

	          $sessionUtility = new sessionUtility();

	          $controller = new controllers\PagesController($view, $sessionUtility);

	          break;

	      case 'PostsController':

	          $database = new tools\Database(DB_DSN, DB_USER, DB_PASSWORD);
	          
	          $blogPostDatabase = new tools\blogpostdb($database);

	          $blogUserDatabase = new tools\bloguserdb($database);


	          $blogCommentDatabase = new tools\blogcommentdb($database);
	          
	          $view = new controllers\View("views/header.php", '', "views/footer.php");


	          $controller = new controllers\PostsController($blogUserDatabase, $blogPostDatabase, $blogCommentDatabase, $view);

	           break;


         case 'UsersController':

	          $database = new tools\Database(DB_DSN, DB_USER, DB_PASSWORD);
	          
	          $blogPostDatabase = new tools\blogpostdb($database);

	          $blogUserDatabase = new tools\bloguserdb($database);

	          $blogCommentDatabase = new tools\blogcommentdb($database);
	          
	          $view = new controllers\View("views/header.php", '', "views/footer.php");

	          $sessionUtility = new sessionUtility();



	         $controller = new controllers\UsersController($blogUserDatabase, $blogPostDatabase, $blogCommentDatabase, $view, $sessionUtility);

	         break;


	        }	
	//	$controller = '\\BW\\controllers\\' . $controller;


				if(! method_exists($controller, $action)) {

					throw new Exception(
						"{$controller} does not respond to the {$action} action."
					);

				}

				return $controller->$action();

	}
	
}