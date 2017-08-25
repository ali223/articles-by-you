<?php

namespace App\Controllers;

use App\Models\BlogUser;
use App\Models\BlogUserDB;

use App\Validators\FilterInputTrait;
use App\Validators\FormValidator;

use App\Utilities\RedirectTrait;
use App\Utilities\SessionUtility;
use App\Utilities\InputUtility;

class UsersController 
{
    use FilterInputTrait, RedirectTrait;

    protected $blogUserDatabase;
    protected $view;
    protected $sessionUtility;
    
    public function __construct(BlogUserDB $blogUserDatabase, View $view, SessionUtility $sessionUtility) 
    {
             
        $this->blogUserDatabase = $blogUserDatabase;
        $this->view = $view;
        $this->sessionUtility = $sessionUtility;
       
    }


    public function create() 
    {

        $pageTitle = "Welcome to Articles By U -- Registration Form";

        $this->view->show('users/regform', compact('pageTitle'));
        
    }

    public function store(InputUtility $input) 
    {

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            $this->userRegistrationForm();
            return;
        }

        $blogUser = $this->createBlogUserFromPostData($input->posts());

        $blogUser->userRegDate = time();

        $errorMessages = $this->validateUserForm($input->posts());

        if ($this->blogUserDatabase->userExists($blogUser->userName)) {
            $errorMessages[] = "The User Name $blogUser->userName alreadys exists. Please choose a different user name.";
        }
           
        if ($errorMessages) {

            return $this->view->show('users/regform', [
                    'blogUser' => $blogUser,
                    'errorMessages' => $errorMessages
                ]);

        }      

        if ($this->blogUserDatabase->addUser($blogUser)) {

            return $this->view->show('users/userCreated', [
                    'blogUser' => $blogUser,
                    'errorMessages' => $errorMessages                
                ]);
        }

    }

    protected function validateUserForm($postData)
    {
        return (new FormValidator($postData))
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

    protected function createBlogUserFromPostData($postData)
    {
        $blogUser = new BlogUser();

        foreach($postData as $field => $data) {
            $blogUser->$field = 
                $field == 'userPassword' ? sha1($data) : $data;
        }

        return $blogUser;

    }

    public function login() 
    {
        $this->redirectIfUserLoggedIn();

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            return $this->view->show('users/login');
        }

        $errorMessages = (new FormValidator($_POST))
                ->validateRequireds([
                    'txtusername' => 'Please enter your username',
                    'txtuserpassword' => 'Please enter your password'
                ])->getValidationErrors();

        if($errorMessages) {
            return $this->view->show('users/login', 
                            compact('errorMessages'));         
        }

        $username = $this->filterInput($_POST['txtusername']);
        $password = $this->filterInput($_POST['txtuserpassword']);


        if (!($this->blogUserDatabase->authenticateUser($username, sha1($password)))) {

            $errorMessages[] = "Login Failed : Username and password combination not valid.";
            
            return $this->view->show('users/login', 
                            compact('errorMessages'));         
        }

        $this->sessionUtility->loginUser($username);
        
        return $this->redirectTo('/home');
    }

    public function logout() 
    {
        $this->redirectIfUserNotLoggedIn();
        
        $this->sessionUtility->endSession();
            
        $logoutMessage = 
            "You have successfully logged out of your acccount.";
            
        return $this->view->show('users/login', compact('logoutMessage'));
    }

    public function userProfile() 
    {

        $this->redirectIfUserNotLoggedIn();

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {

            $blogUser = $this->blogUserDatabase->getUserByUsername($this->sessionUtility->getLoggedInUsername());
            
            $this->view->setHeaderFile("views/userheader.php");
            
            return $this->view->show('users/userprofile' ,[
                'username' => $this->sessionUtility->getLoggedInUsername(),
                'blogUser' => $blogUser,
            ]);
        }

        $blogUser = $this->createBlogUserFromPostData($_POST);

        $blogUser->userName = $this->sessionUtility->getLoggedInUsername();

        $errorMessages = (new FormValidator($_POST))
            ->validateRequireds([
                'userFirstName' => 'First Name is required',
                'userLastName' => 'Last Name is required',
                'userEmail' => 'Email Address is required'
            ])
            ->validateEmail('userEmail')
            ->validateURL('userUrl')
            ->getValidationErrors();
       
        if (!empty($errorMessages)) {

            $this->view->setHeaderFile("views/userheader.php");

            return $this->view->show('users/userprofile' ,[
                'username' => $this->sessionUtility->getLoggedInUsername(),
                'blogUser' => $blogUser,
                'errorMessages' => $errorMessages
            ]);

        }

        $this->blogUserDatabase->updateUser($blogUser);

        $this->sessionUtility->put('message', "Your profile has been updated successfully");

        return $this->redirectTo('/home');
    }

    public function userPassword() 
    {

        $this->redirectIfUserNotLoggedIn();
        
        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {

            $this->view->setHeaderFile("views/userheader.php");

            return $this->view->show('users/userpassword' , [
                'username' => $this->sessionUtility->getLoggedInUsername()
            ]);

        }

        $errorMessages = (new FormValidator($_POST))
            ->validateRequireds([
                'txtuserpasswordcurrent' => 'Please enter your current password.',
                'txtuserpasswordnew1' => 'Please enter your new password.',
                'txtuserpasswordnew2' => 'Please confirm your new password'
            ])
            ->validateMatches(
                ['txtuserpasswordnew1', 'txtuserpasswordnew2'], 
                'New and Confirmed Passwords must match')
            ->getValidationErrors();

        $passwordCurrent = sha1($this->filterInput($_POST['txtuserpasswordcurrent']));

        $passwordNew = sha1($this->filterInput($_POST['txtuserpasswordnew1']));

        $username = $this->sessionUtility->getLoggedInUsername();

        if (empty($errorMessages) && ! $this->blogUserDatabase
                ->authenticateUser($username, $passwordCurrent)) {
            $errorMessages[] = "The current password entered is not valid.";
        }


        if($errorMessages) {
            
            $this->view->setHeaderFile("views/userheader.php");

            return $this->view->show('users/userpassword' ,[
                'username' => $this->sessionUtility->getLoggedInUsername(),
                'errorMessages' => $errorMessages,
            ]);

        }     
        
        $result = $this->blogUserDatabase
                    ->updatePassword($username, $passwordNew);

        $message = $result ? "Your password has been changed successfully."              : "The password could not be updated.";

        $this->sessionUtility->put('message', $message);
            
        return $this->redirectTo('/home');
             
    }
}
