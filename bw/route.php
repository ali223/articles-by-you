<?php
namespace BW;


class Route {

	private $_uri = [];



	public function add($uri ) {

		$this->_uri[] = trim($uri,'/');
	}


	public function submit(){

		$uriGetParam = isset($_GET['uri']) ? $_GET['uri'] : '';


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