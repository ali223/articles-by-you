<?php
namespace App\Services;

use App\Models\BlogUserDB;
use App\Utilities\InputUtility;
use App\Validators\FormValidator;

class UserAuthentication
{
	protected $userDatabase;
	protected $input;
	protected $validator;

	public function __construct(BlogUserDB $userDatabase, InputUtility $input, FormValidator $validator)
	{
		$this->userDatabase = $userDatabase;
		$this->input = $input;
		$this->validator = $validator;
	}

	public function authenticate()
	{
        $errorMessages = ($this->validator->setPostData($this->input->posts()))
                ->validateRequireds([
                    'txtusername' => 'Please enter your username',
                    'txtuserpassword' => 'Please enter your password'
                ])->getValidationErrors();

        if($errorMessages) {
            throw new UserAuthenticationException('Required data missing', $errorMessages);
        }

        $username = $this->input->post('txtusername');
        $password = $this->input->post('txtuserpassword');

        if (!($this->userDatabase->authenticateUser($username, sha1($password)))) {

            $errorMessages[] = "Login Failed : Username and password combination not valid.";

            throw new UserAuthenticationException('Authentication failed', $errorMessages);
            
        }

        return $username;

	}

}