<?php
namespace App\Services;

use Exception;

class UserException extends Exception
{
	protected $errorMessages;

	public function __construct($message, $errorMessages = [])
	{
		parent::__construct($message);

		$this->errorMessages = $errorMessages;

	}

	public function getErrorMessages()
	{
		return $this->errorMessages;
	}
}