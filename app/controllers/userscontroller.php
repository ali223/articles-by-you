<?php

namespace app\controllers;

use app\models\BlogUser;
use app\models\BlogPost;
use app\models\BlogUserDB;
use app\models\BlogPostDB;

use app\validators\FilterInputTrait;
use app\validators\FormValidator;

use app\utilities\RedirectTrait;


class UsersController {
    use FilterInputTrait, RedirectTrait;

    protected $blogPostDatabase;
    protected $blogUserDatabase;
    protected $blogCommentDatabase;
    protected $view;
    protected $sessionUtility;
    
    public function __construct(BlogUserDB $blogUserDatabase, BlogPostDB $blogPostDatabase, View $view, SessionUtility $sessionUtility) {
             

        $this->blogPostDatabase = $blogPostDatabase;
        $this->blogUserDatabase = $blogUserDatabase;
        $this->view = $view;
        $this->sessionUtility = $sessionUtility;
       
    }


    public function userregistrationform() {

        $pageTitle = "Welcome to Articles By U -- Registration Form";

        $this->view->setData('pageTitle', $pageTitle);
        $this->view->setContentFile("views/users/regform.php");
        $this->view->renderView();

        
    }

    public function create() {

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            $this->userRegistrationForm();
            return;
        }

        $validator = $this->validateUserForm($_POST);

        $blogUser = $this->createBlogUserFromPostData($_POST);

        $errorMessages = $validator->getValidationErrors();

        if ($this->blogUserDatabase->userExists($blogUser->userName)) {
            $errorMessages[] = "The User Name $blogUser->userName alreadys exists. Please choose a different user name.";
        }

           
        if ($errorMessages) {

           $this->view->setData("blogUser",$blogUser);
           $this->view->setData("errorMessages", $errorMessages);
           $this->view->setContentFile("views/users/regform.php");
           $this->view->renderView();

           return;
        }      

        if ($this->blogUserDatabase->addUser($blogUser)) {
            $this->view->setData("blogUser",$blogUser);
            $this->view->setData("errorMessages", $errorMessages);
            $this->view->setContentFile("views/users/userCreated.php");
            $this->view->renderView();

            return;
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
            ->validateURL('userUrl');

    }

    protected function createBlogUserFromPostData($postData)
    {
        $blogUser = new BlogUser();

        foreach($postData as $field => $data) {
            $blogUser->$field = 
                $field == 'userPassword' ? sha1($data) : $data;
        }

        $blogUser->userRegDate = time();

        return $blogUser;

    }

    public function login() {
        if ($this->sessionUtility->isLoggedIn()) {
            return $this->redirectTo('/home');            
        }

        $errorMessages = [];

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            
            $this->view->setContentFile("views/users/login.php");
            $this->view->renderView();            
            return;
        }

        //echo "data submitted";
        if (empty($_POST['txtusername']) || empty($_POST['txtuserpassword'])) {
            
            $errorMessages[] = "Please enter your User Name and Password";
            
            $this->view->setData("errorMessages",$errorMessages);
            $this->view->setContentFile("views/users/login.php");
            $this->view->renderView();            
            
            return;
            
        } else {
            $username = $this->filterInput($_POST['txtusername']);
            $password = $this->filterInput($_POST['txtuserpassword']);
        }


        if (!($this->blogUserDatabase->authenticateUser($username, sha1($password)))) {

            $errorMessages[] = "Login Failed : Username and password combination not valid.";
            
            $this->view->setData("errorMessages",$errorMessages);
            $this->view->setContentFile("views/users/login.php");
            $this->view->renderView();            
            
            return;
        }

        //the following lines get executed, if the user has been authenticated successfully

        $this->sessionUtility->loginUser($username);
        
        return $this->redirectTo('/home');
    }

    public function userhome() {

        $this->redirectIfUserNotLoggedIn();

        $message = '';

        if($this->sessionUtility->has('message')) {
            $message = $this->sessionUtility->getAndRemove('message');
        }
       
        $blogPostsList = $this->blogPostDatabase->getPostsByUser($this->sessionUtility->getLoggedInUsername());
        
        $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
        $this->view->setData("message", $message);
        $this->view->setData("blogPostsList", $blogPostsList);
        
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setContentFile("views/users/userhome.php");
        $this->view->renderView();
               
    }

    public function logout() {
        if ($this->sessionUtility->isLoggedIn()) {
            $this->sessionUtility->endSession();
            $logoutMessage = "You have successfully logged out of your acccount.";
            $this->view->setData("logoutMessage",$logoutMessage);
        }
        
        $this->view->setHeaderFile("views/header.php");
        $this->view->setContentFile("views/users/login.php");
        $this->view->renderView();
        
    }

    public function userprofile() {

        $this->redirectIfUserNotLoggedIn();

        $blogUser = new BlogUser();

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {

            $blogUser = $this->blogUserDatabase->getUserByUsername($this->sessionUtility->getLoggedInUsername());
            
            $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
            $this->view->setData("blogUser",$blogUser);
            $this->view->setHeaderFile("views/userheader.php");
            $this->view->setContentFile("views/users/userprofile.php");
            $this->view->renderView();
            
            return;
        }

        $blogUser->userName = $this->sessionUtility->getLoggedInUsername();

        $blogUser->userFirstName = 
                $this->filterInput($_POST['txtuserfirstname']);

        $blogUser->userLastName = 
                $this->filterInput($_POST['txtuserlastname']);

        $blogUser->userUrl = 
            $this->filterInput($_POST['txtuserurl']);

        $blogUser->userEmail = 
            $this->filterInput($_POST['txtuseremail']);


        $errorMessages = (new FormValidator($_POST))
            ->validateRequireds([
                'txtuserfirstname' => 'First Name is required',
                'txtuserlastname' => 'Last Name is required',
                'txtuseremail' => 'Email Address is required'
            ])
            ->validateEmail('txtuseremail')
            ->validateURL('txtuserurl')
            ->getValidationErrors();
       
        if (!empty($errorMessages)) {
            $this->view->setData("username",$this->sessionUtility->getLoggedInUsername());
            $this->view->setData("errorMessages",$errorMessages);
            $this->view->setData("blogUser", $blogUser);
            $this->view->setHeaderFile("views/userheader.php");
            $this->view->setContentFile("views/users/userprofile.php");
            $this->view->renderView();
            return;
        }

        $this->blogUserDatabase->updateUser($blogUser);
        $successMessage = "Your profile has been updated successfully.";
        $this->userhome($successMessage);
    }

    public function userpassword() {

        $this->redirectIfUserNotLoggedIn();
        
        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            $this->view->setData("username",$this->sessionUtility->getLoggedInUsername());
            $this->view->setHeaderFile("views/userheader.php");
            $this->view->setContentFile("views/users/userpassword.php");
            $this->view->renderView();
            
            return;
        }

        $errorMessages = [];
   
        if (empty($_POST['txtuserpasswordcurrent'])) {
            $errorMessages[] = "Please enter your current password.";
        } else {
            $userpasswordcurrent = sha1($this->filterInput($_POST['txtuserpasswordcurrent']));
        }

        if (empty($_POST['txtuserpasswordnew1']) || empty($_POST['txtuserpasswordnew2'])) {
            $errorMessages[] = "Please enter both your new and confirmed passwords.";
        } else {
            $userpasswordnew1 = sha1($this->filterInput($_POST['txtuserpasswordnew1']));
            $userpasswordnew2 = sha1($this->filterInput($_POST['txtuserpasswordnew2']));

            if ($userpasswordnew1 != $userpasswordnew2) {
                $errorMessages[] = "Your new and confirmed passwords do not match.";
            } else {
                $userpassword = $userpasswordnew1;
            }
        }

        $username = $this->sessionUtility->getLoggedInUsername();
        if (empty($errorMessages) &&
                $this->blogUserDatabase->authenticateUser($username, $userpasswordcurrent)) {
            $result = $this->blogUserDatabase->updatePassword($username, $userpassword);
            //echo "<br /> result is $result";
            if ($result) {
                $successMessage = "Your password has been changed successfully";
                $this->userhome($successMessage);
                return;
            }
        } else {
            $errorMessages[] = "The current password entered is not valid.";
            //echo "The current password entered is not valid.";
        }
    
        $this->view->setData("username",$this->sessionUtility->getLoggedInUsername());
        $this->view->setData("errorMessages",$errorMessages);
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setContentFile("views/users/userpassword.php");
        $this->view->renderView();
      
    }

}
