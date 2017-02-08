<?php

namespace BW\validators;


trait FilterInputTrait {
    
    protected function filterInput($data) {

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }
  

}
    

