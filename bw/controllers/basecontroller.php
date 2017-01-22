<?php

namespace BW\controllers;


class BaseController {
    
    protected function filterInput($data) {

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }
  

}
    

