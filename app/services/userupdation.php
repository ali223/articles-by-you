<?php
namespace App\Services;

use App\Models\BlogUserDB;
use App\Models\BlogUser;
use App\Utilities\InputUtility;
use App\Utilities\SessionUtility;
use App\Validators\FormValidator;

class UserUpdation
{
	protected $userDatabase;
	protected $input;
	protected $validator;
    protected $session;
    protected $blogUser;

	public function __construct(BlogUserDB $userDatabase, InputUtility $input, SessionUtility $session, FormValidator $validator, BlogUser $blogUser)
	{
		$this->userDatabase = $userDatabase;
		$this->input = $input;
		$this->validator = $validator;
        $this->session = $session;
        $this->blogUser = $blogUser;
	}

	public function updateProfile()
	{
        $this->blogUser->setData($this->input->posts());

        $this->blogUser->userName = $this->session->getLoggedInUsername();

        $errorMessages =  $this->validateUserForm($this->input->posts());

        if ($errorMessages) {
            throw new UserUpdationException('Form Error updating user', $errorMessages);
        }

        if($this->userDatabase->updateUser($this->blogUser)) {
            return true;
        }

        throw new UserUpdationException('Error updating user');

	}


    public function getOldPostData()
    {
        return $this->blogUser->setData($this->input->posts());
    }


    protected function validateUserForm($postData) 
    {
        return ($this->validator->setPostData($postData))
        ->validateRequireds([
            'userFirstName' => 'First Name is required',
            'userLastName' => 'Last Name is required',
            'userEmail' => 'Email Address is required'
        ])
        ->validateEmail('userEmail')
        ->validateURL('userUrl')
        ->getValidationErrors();
    }

}