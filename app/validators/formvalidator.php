<?php

namespace app\validators;

class FormValidator 
{
	private $validationErrors = [];
	private $postData;

	public function __construct($postData)
	{
		$this->postData = $postData;
	}

	public function validateRequireds($fields)
	{
		foreach($fields as $fieldName => $errorMessage) {
			if(! $this->postData[$fieldName]) {
				$this->validationErrors[] = $errorMessage;
			}
		}

		return $this;
	}

	public function validateMatches($fields, $errorMessage)
	{

		if($this->postData[$fields[0]] != $this->postData[$fields[1]]) {
			$this->validationErrors[] = $errorMessage;		
		}

		return $this;

	}


	public function validateRequired($fieldName, $fieldValue) 
	{
		if(empty($fieldValue)) {
			$this->validationErrors[] = "The {$fieldName} must be filled in";
		}

		return $this;
	}

	public function validateEmail(
					$field, 
					$errorMessage = 'The email address must be in proper format. For example: john@example.com') 
	{
		if($this->postData[$field] && !filter_var($this->postData[$field], FILTER_VALIDATE_EMAIL)) {
			$this->validationErrors[] = $errorMessage;
		}

		return $this;
	}

	public function validateAlphaNumeric($field, $errorMessage) 
	{
		if($this->postData[$field] && !preg_match("/^[a-z]+\d*$/", $this->postData[$field])) {
			$this->validationErrors[] = $errorMessage;
		}

		return $this;
	}

	public function validateURL(						
						$field, 
						$errorMessage = 'Please provide your website address in correct format e.g. (http://www.example.com)') 
	{
		if($this->postData[$field] && !filter_var($this->postData[$field], FILTER_VALIDATE_URL)) {
			$this->validationErrors[] = $errorMessage;
		}

		return $this;
	}



	public function validateMaxLength($fieldName, $fieldValue, $length = 255) 
	{
		if(strlen($fieldValue) > $length) {
			$this->validationErrors[] = "The {$fieldName} must not contain more than {$length} characters";
		}

		return $this;
	}



	public function getValidationErrors() 
	{
		return $this->validationErrors;
	}
}