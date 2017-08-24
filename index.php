<?php
require "vendor/autoload.php";

require_once 'config.php';

$routes = require 'configroutes.php';

 $uri = htmlspecialchars(
          parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );

$routes->direct($uri);