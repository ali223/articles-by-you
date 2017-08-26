<?php

namespace App\Controllers;

use App\Models\BlogUser;
use App\Models\BlogUserDB;

use App\Utilities\RedirectTrait;
use App\Utilities\SessionUtility;
use App\Utilities\InputUtility;

use App\Services\UserRegistration;
use App\Services\UserAuthentication;
use App\Services\UserUpdation;

use App\Services\UserRegistrationException;
use App\Services\UserUpdationException;
use App\Services\UserAuthenticationException;

class UsersController 
{
    use RedirectTrait;

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

    public function store(UserRegistration $registration) 
    {

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            $this->create();
            return;
        }

        try {

            $blogUser = $registration->register();

            return $this->view
                ->show('users/userCreated', compact('blogUser'));

        } catch(UserRegistrationException $exception) {
            return $this->view->show('users/regform', [
                    'blogUser' => $registration->getOldPostData(),
                    'errorMessages' => $exception->getErrorMessages()
                ]);
        }

    }

    public function login(UserAuthentication $authentication) 
    {
        $this->redirectIfUserLoggedIn();

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            return $this->view->show('users/login');
        }

        try {

            $username = $authentication->authenticate();

            $this->sessionUtility->loginUser($username);
        
            return $this->redirectTo('/home');

        } catch (UserAuthenticationException $exception) {
            return $this->view->show('users/login', [
                    'errorMessages' => $exception->getErrorMessages()
                ]);         
        }

    }

    public function logout() 
    {
        $this->redirectIfUserNotLoggedIn();
        
        $this->sessionUtility->endSession();
            
        $logoutMessage = 
            "You have successfully logged out of your acccount.";
            
        return $this->view->show('users/login', compact('logoutMessage'));
    }

    public function userProfile(UserUpdation $updation) 
    {

        $this->redirectIfUserNotLoggedIn();

        $this->view->setHeaderFile("views/userheader.php");

        $this->view->setData('username', $this->sessionUtility->getLoggedInUsername());

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {

            $blogUser = $this->blogUserDatabase->getUserByUsername($this->sessionUtility->getLoggedInUsername());            
            
            return $this->view->show('users/userprofile', compact('blogUser'));
        }

        try {

            $updation->updateProfile();

            $this->sessionUtility->put('message', "Your profile has been updated successfully");

            return $this->redirectTo('/home');

        } catch(UserUpdationException $exception) {

            return $this->view->show('users/userprofile' ,[
                'blogUser' => $updation->getOldPostData(),
                'errorMessages' => $exception->getErrorMessages()
            ]);

        }
    }

    public function userPassword(UserUpdation $updation) 
    {

        $this->redirectIfUserNotLoggedIn();

        $this->view->setHeaderFile("views/userheader.php");

        $this->view->setData('username', $this->sessionUtility->getLoggedInUsername());

        
        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {

            return $this->view->show('users/userpassword');
        }

        try {

            $updation->updatePassword();

            $this->sessionUtility->put('message', 'Your password has been updated successfully.');
            
            return $this->redirectTo('/home');

        } catch(UserUpdationException $exception) {
         
            return $this->view->show('users/userpassword' ,[
                'errorMessages' => $exception->getErrorMessages()
            ]);

        }     
    }
}
