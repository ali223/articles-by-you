<?php

namespace App\Validators;


trait FilterInputTrait {
    
    protected function filterInput($data) {

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }
  

}
    

