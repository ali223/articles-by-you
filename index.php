<?php

namespace BW;


spl_autoload_register();



$routes = require 'configroutes.php';

 $uri = htmlspecialchars(
          parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );

$routes->direct($uri);


