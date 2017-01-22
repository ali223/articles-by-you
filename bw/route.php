<?php
namespace BW;


class Route {

	private $_routes = [];



	public function add($uri, $controllerName ) {

		$this->_routes[$uri] = $controllerName;
	}


	public function submit(){

		$uriGetParam = $_SERVER['REQUEST_URI'];

		echo $uriGetParam;


		$actions = explode('/', $uriGetParam);

		echo "<pre>", print_r($actions), "</pre>";

		$controllerName =  isset($actions[0]) ?  '\BW\controllers\\' .$actions[0] : '';

		$actionName = isset($actions[1]) ? $actions[1] : '';

		echo '<br />Controller Name is ' . $controllerName;

		echo '<br />Action Name is ' . $actionName;

          $view = new controllers\View("views/header.php", '', "views/footer.php");

          $sessionUtility = new controllers\sessionUtility();

		$controller = new $controllerName($view, $sessionUtility);
		$controller->$actionName();
		

		foreach($this->_uri as $key => $value){

			echo "<br /> value is $value and uri is $uriGetParam";

			if(preg_match("#^$value$#", $uriGetParam)) {
				echo '<br />match';


			}

		}




	}
	

}