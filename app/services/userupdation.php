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

        if( ! $this->userDatabase->updateUser($this->blogUser)) {
            throw new UserUpdationException('Error updating user', 
                ['Could not update user profile due to a system error']);
        }

        return true;

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

    public function updatePassword()
    {
        $errorMessages = $this->validatePasswordForm($this->input->posts());

        if($errorMessages) {
            throw new UserUpdationException('Passwords form error', $errorMessages);
        }

        $passwordCurrent = sha1($this->input->post('txtuserpasswordcurrent'));

        $passwordNew = sha1($this->input->post('txtuserpasswordnew1'));

        $username = $this->session->getLoggedInUsername();

        if (! $this->userDatabase
                ->authenticateUser($username, $passwordCurrent)) {

            throw new UserUpdationException('Error Updating Password', 
                ['The current password entered is not valid.']);            
        }


        
        if(! $this->userDatabase
                    ->updatePassword($username, $passwordNew)) {
            
            throw new UserUpdationException('Error Updating Password', 
                ['Could not update password due to a system error.']);
        }

        return true;

    }

    protected function validatePasswordForm($postData)
    {
        return ($this->validator->setPostData($postData))
        ->validateRequireds([
            'txtuserpasswordcurrent' => 'Please enter your current password.',
            'txtuserpasswordnew1' => 'Please enter your new password.',
            'txtuserpasswordnew2' => 'Please confirm your new password'
        ])
        ->validateMatches(
            ['txtuserpasswordnew1', 'txtuserpasswordnew2'], 
            'New and Confirmed Passwords must match')
        ->getValidationErrors();

    }

}