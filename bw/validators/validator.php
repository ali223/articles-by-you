<?php
namespace BW\validators;

abstract class Validator {
	 protected function filterInput($formData) {

        $formData = trim($formData);
        $formData = stripslashes($formData);
        $formData = htmlspecialchars($formData);

        return $formData;
    }

}