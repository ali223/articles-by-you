<?php
namespace App;

use Exception;

use App\Models\Database;
use App\Utilities\InputUtility;

use Illuminate\Container\Container;

class Route
{

	protected $routes = [];

	public function add($uri, $controllerName ) 
	{
		$this->routes[$uri] = $controllerName;
	}

	public function direct($uri)
	{
		if(array_key_exists($uri, $this->routes)) {

			list($controller, $action) = explode('@', $this->routes[$uri]);
			
			return $this->callAction($controller, $action);
		} 

		return $this->callAction("PagesController", "error");
	}

	protected function callAction($controller, $action) 
	{

		$container = Container::getInstance();

		$container->bind(Database::class, function () {
			return new Database(DB_DSN, DB_USER, DB_PASSWORD);
		});

		$container->bind(InputUtility::class, function () {
			return InputUtility::createFromGlobals();
		});


		$controller = $container->make('App\\Controllers\\' . $controller);

		return $container->call([$controller, $action]);

	}
	
}