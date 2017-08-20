<?php

namespace App\validators;


trait FilterInputTrait {
    
    protected function filterInput($data) {

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }
  

}
    

