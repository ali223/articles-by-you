<?php

namespace BW\controllers;

use BW\tools\bloguser;
use BW\tools\blogpost;
use BW\tools\blogcomment;


use BW\tools\bloguserdb;
use BW\tools\blogpostdb;
use BW\tools\blogcommentdb;

use BW\validators\UserRegistrationValidator;
use BW\validators\userProfileValidator;
use BW\validators\userPostValidator;

class UsersController extends BaseController {

    protected $blogPostDatabase;
    protected $blogUserDatabase;
    protected $blogCommentDatabase;
    protected $view;
    protected $sessionUtility;
    
    public function __construct(bloguserdb $blogUserDatabase, blogpostdb $blogPostDatabase, blogcommentdb $blogCommentDatabase, View $view, SessionUtility $sessionUtility) {
             

        $this->blogPostDatabase = $blogPostDatabase;
        $this->blogUserDatabase = $blogUserDatabase;
        $this->blogCommentDatabase = $blogCommentDatabase;
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

        // echo "<br /> user create called";
        // echo "<pre>", print_r($_POST), "</pre>";

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            $this->userRegistrationForm();
            return;
        }


        $errorMessages = [];

        // hard-coded dependencies ?? method injection ??

        $blogUser = new bloguser();

        $userRegistrationValidator = new UserRegistrationValidator();

        $errorMessages = $userRegistrationValidator->validateUserForm($_POST, $blogUser, $this->blogUserDatabase);        


        // introduce CSRF check
        
        if ($errorMessages) {

           $this->view->setData("blogUser",$blogUser);
           $this->view->setData("errorMessages", $errorMessages);
           $this->view->setContentFile("views/users/regform.php");
           $this->view->renderView();

           
            return;
        }

        if ($this->blogUserDatabase->addUser($blogUser)) {
            //echo "<Br /> user registered successfully.";
            $this->view->setData("blogUser",$blogUser);
            $this->view->setData("errorMessages", $errorMessages);
            $this->view->setContentFile("views/users/userCreated.php");
            $this->view->renderView();

            return;
 
        }

    }

    public function login() {
        if ($this->sessionUtility->isLoggedIn()) {
            $this->userhome();
            return;
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
        $this->sessionUtility->storeInSession($username);
        $this->userhome();
    }

    public function userhome($message = null) {
        if (!($this->sessionUtility->isLoggedIn())) {
            $this->login();
            return;
        }

        // the following lines get executed, only if there is a user currently logged in

        
        $blogPostsList = $this->blogPostDatabase->getPostsByUser($this->sessionUtility->getLoggedInUsername());
        
        $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
        $this->view->setData("message", $message);
        $this->view->setData("blogPostsList", $blogPostsList);
        
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setContentFile("views/users/userhome.php");
        $this->view->renderView();
               
    }

    public function userviewarticle() {
        if (!$this->sessionUtility->isLoggedIn()) {
            $this->login();
            return;
        }

        $errorMessages = [];

        if (!isset($_GET['id'])) {
            $this->userhome();
            return;
        }

        $id = $this->filterInput($_GET['id']);
        $blogPost = $this->blogPostDatabase->getPost($id);

        if (!$blogPost) {
            $this->userhome();
            return;
        }

        $blogUser = $this->blogUserDatabase->getUserById($blogPost->postuserid);

        
        $this->view->setData("blogPost", $blogPost);
        $this->view->setData("blogUser", $blogUser);
        $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setContentFile("views/users/userviewarticle.php");
        $this->view->renderView();
        
    }

    public function usernewarticle() {

        if (!($this->sessionUtility->isLoggedIn())) {
            $this->login();
            return;
        }

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            
            $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
            $this->view->setHeaderFile("views/userheader.php");            
            $this->view->setContentFile("views/users/usernewarticle.php");
            $this->view->renderView();
            
         
            return;
        }

        $errorMessages = [];

         $formType = isset($_POST['formtype']) ? $this->filterInput($_POST['formtype']) : '';


        $blogPost = new blogpost();

        $userPostValidator = new UserPostValidator();

        $errorMessages = $userPostValidator->validatePostForm($_POST, $blogPost, $_FILES,$formType);
        
        
        // get the currently logged in user's user id and store it in the new post data
        $blogUser = $this->blogUserDatabase->getUserByUsername($this->sessionUtility->getLoggedInUsername());
        $blogPost->postuserid = $blogUser->userid;


       
        //echo "<pre>", print_r($blogpostobj), "</pre>";
        if (!empty($errorMessages)) {
            $this->view->setData("errorMessages",$errorMessages);
            $this->view->setData("blogPost", $blogPost);
            $this->view->setData("blogUser", $blogUser);
            $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
            
            $this->view->setHeaderFile("views/userheader.php");
            $this->view->setContentFile(($formType == 'edit') ? "views/users/usereditarticle.php" : "views/users/usernewarticle.php");
            $this->view->renderView();
            return;
        }


        if ($formType == "new") {
            $blogPost->postreads = 0;
            $this->blogPostDatabase->addPost($blogPost);
            $successMessage = "Your New Article titled $blogPost->posttitle has been created successfully";
        } elseif ($formType == "edit") {
            $this->blogPostDatabase->updatePost($blogPost);
            $successMessage = "Your Article titled $blogPost->posttitle has been updated successfully";
        }

        $this->userhome($successMessage);
    }

    public function usereditarticle() {

        if (!($this->sessionUtility->isLoggedIn())) {
            $this->login();
            return;
        }

        if (!(isset($_GET['id']))) {
            $this->userhome();
            return;
        }

        $id = $this->filterInput($_GET['id']);

        $blogPost = $this->blogPostDatabase->getPost($_GET['id']);

        if (!$blogPost) {
            $this->userhome();
            return;
        }
        //$buser = $this->bloguserdbobj->getUserById($bpost->postuserid);            
        $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
        $this->view->setData("blogPost",$blogPost);
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setContentFile("views/users/usereditarticle.php");
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

    public function error($errormsgs) {
        
    }

    public function userdeletearticle() {
        if (!$this->sessionUtility->isLoggedIn()) {
            $this->login();
            return;
        }

        if (!(isset($_GET['id']))) {
            $this->userhome();
            return;
        }


        $id = $this->filterInput($_GET['id']);
        $blogPost = $this->blogPostDatabase->getPost($id);

        if (!$blogPost) {
            $this->userhome();
            return;
        }

        $this->blogPostDatabase->deletePost($id);
        $successMessage = "Your Article titled $blogPost->posttitle has been deleted successfully";
        $this->userhome($successMessage);
    }

    public function userprofile() {

        if (!$this->sessionUtility->isLoggedIn()) {
            $this->login();
            return;
        }

        //echo "profile updated";
        $blogUser = new \BW\tools\bloguser();
        //echo "user profile";
        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            // get the details of current user to pre-fill the profile form


        $blogUser = $this->blogUserDatabase->getUserByUsername($this->sessionUtility->getLoggedInUsername());
            
            $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
            $this->view->setData("blogUser",$blogUser);
            $this->view->setHeaderFile("views/userheader.php");
            $this->view->setContentFile("views/users/userprofile.php");
            $this->view->renderView();
            
            return;
        }

        $blogUser->username = $this->sessionUtility->getLoggedInUsername();

        $userProfileValidator = new userProfileValidator();

        $errorMessages = $userProfileValidator->validateProfileForm($_POST, $blogUser);

       
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
        if (!$this->sessionUtility->isLoggedIn()) {
            $this->login();
            return;
        }
        
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

    protected function filterInput($data) {

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }


}



?>