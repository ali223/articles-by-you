<?php
namespace App\Services;

use Exception;

class AppException extends Exception
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