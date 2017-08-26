<?php
namespace App\Services;

use App\Models\BlogUserDB;
use App\Models\BlogUser;
use App\Utilities\InputUtility;
use App\Validators\FormValidator;

class UserRegistration
{
	protected $userDatabase;
	protected $input;
	protected $blogUser;
	protected $validator;

	public function __construct(BlogUserDB $userDatabase, InputUtility $input, BlogUser $blogUser, FormValidator $validator)
	{
		$this->userDatabase = $userDatabase;
		$this->input = $input;
		$this->blogUser = $blogUser;
		$this->validator = $validator;
	}

	public function register()
	{
        $this->blogUser->setData($this->input->posts());

        $this->blogUser->userRegDate = time();

        $errorMessages = $this->validateUserForm($this->input->posts());

        if ($this->userDatabase->userExists($this->blogUser->userName)) {
            $errorMessages[] = "The User Name {$this->blogUser->userName} alreadys exists. Please choose a different user name.";
        }
           
        if ($errorMessages) {
        	throw new UserRegistrationException("User Registration Error", $errorMessages);
        }      

        if ($this->userDatabase->addUser($this->blogUser)) {
        	return $this->blogUser;
        }
        
        throw new UserRegistrationException("User Registration Error", ['User could not be registered due to system error']);

	}

	public function getOldPostData()
	{
		return $this->blogUser->setData($this->input->posts());
	}

    protected function validateUserForm($postData)
    {
        return ($this->validator->setPostData($postData))
            ->validateRequireds([
                'userName' => 'User Name is required',
                'userPassword' => 'Password is required',
                'userPassword2' => 'Re-enter Password is required',
                'userFirstName' => 'First Name is required',
                'userLastName' => 'Last Name is required',
                'userEmail' => 'Email Address is required'
            ])
            ->validateMatches(
                ['userPassword', 'userPassword2'], 
                'Passwords must match'
            )
            ->validateEmail('userEmail')
            ->validateAlphaNumeric('userName',
                        'User Name : Only letters a-z and numbers 0-9 allowed. Must start with letters, and then numbers, e.g gemini233')
            ->validateURL('userUrl')
            ->getValidationErrors();

    }

}