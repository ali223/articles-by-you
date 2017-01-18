<?php

namespace BW\controllers;

class UsersController extends BaseController {

    public function userregistrationform() {

        $pageTitle = "Welcome to Articles By U -- Registration Form";

        
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

        $blogUser = new \BW\tools\bloguser();

        $userValidation = new UserValidation();

        $errorMessages = $userValidation->validateUserForm($_POST, $blogUser);        

        if ($this->blogUserDatabase->userExists($blogUser->username)) {
            $errorMessages[] = "The User Name $blogUser->username alreadys exists. Please choose a different user name.";
        }

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
        if ($this->isLoggedIn()) {
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
            $username = $this->test_input($_POST['txtusername']);
            $password = $this->test_input($_POST['txtuserpassword']);
        }


        if (!($this->blogUserDatabase->authenticateUser($username, sha1($password)))) {

            $errorMessages[] = "Login Failed : Username and password combination not valid.";
            
            $this->view->setData("errorMessages",$errorMessages);
            $this->view->setContentFile("views/users/login.php");
            $this->view->renderView();            
            
            return;
        }

        //the following lines get executed, if the user has been authenticated successfully
        $this->storeInSession($username);
        $this->userhome();
    }

    public function userhome($message = null) {
        if (!($this->isLoggedIn())) {
            $this->login();
            return;
        }

        // the following lines get executed, only if there is a user currently logged in

        
        $blogPostsList = $this->blogPostDatabase->getPostsByUser($this->getLoggedInUsername());
        
        $this->view->setData("username", $this->getLoggedInUsername());
        $this->view->setData("message", $message);
        $this->view->setData("blogPostsList", $blogPostsList);
        
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setContentFile("views/users/userhome.php");
        $this->view->renderView();
               
    }

    public function userviewarticle() {
        if (!$this->isLoggedIn()) {
            $this->login();
            return;
        }

        $errorMessages = [];

        if (!isset($_GET['id'])) {
            $this->userhome();
            return;
        }

        $id = $this->test_input($_GET['id']);
        $blogPost = $this->blogPostDatabase->getPost($id);

        if (!$blogPost) {
            $this->userhome();
            return;
        }

        $blogUser = $this->blogUserDatabase->getUserById($blogPost->postuserid);

        
        $this->view->setData("blogPost", $blogPost);
        $this->view->setData("blogUser", $blogUser);
        $this->view->setData("username", $this->getLoggedInUsername());
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setContentFile("views/users/userviewarticle.php");
        $this->view->renderView();
        
    }

    public function usernewarticle() {

        if (!($this->isLoggedIn())) {
            $this->login();
            return;
        }

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            
            $this->view->setData("username", $this->getLoggedInUsername());
            $this->view->setHeaderFile("views/userheader.php");            
            $this->view->setContentFile("views/users/usernewarticle.php");
            $this->view->renderView();
            
         
            return;
        }


        // to prevent form resubmission
        /* $messageidentity = md5(implode(",",$_POST));

          if(!(session_status()==PHP_SESSION_ACTIVE)) {
          session_start();
          }
          $sessionmessageidentity = isset($_SESSION['messageidentity']) ? $_SESSION['messageidentity'] : '';

          if($messageidentity == $sessionmessageidentity) {
          $this->userhome();
          return;
          }
          $_SESSION['messageidentity'] = $messageidentity;
         */
        //echo "article submitted";
        //echo "<pre>", print_r($_POST), "</pre>";
        $errorMessages = [];
        $blogPost = new \BW\tools\blogpost();
        
        $formtype = isset($_POST['formtype']) ? $this->test_input($_POST['formtype']) : '';


        if (empty($_POST['txtposttitle'])) {
            $errorMessages[] = "Please enter the Article Title";
        } else {
            $blogPost->posttitle = $this->test_input($_POST['txtposttitle']);
        }

        if (empty($_POST['txtpostdesc'])) {
            $errorMessages[] = "Please enter the Article Description";
        } else {
            $blogPost->postdesc = $this->test_input($_POST['txtpostdesc']);
        }

        if (empty($_POST['txtposttext'])) {
            $errorMessages[] = "Please enter the Article Text";
        } else {
            $blogPost->posttext = $this->test_input($_POST['txtposttext']);
        }

        if ($_POST['txtpostisvisible'] == '') {
            $errorMessages[] = "Please enter the if the Article is a Draft or to be published";
        } else {
            $blogPost->postisvisible = $this->test_input($_POST['txtpostisvisible']);
        }

        // store the current date and time as the postdate
        $blogPost->postdate = time();

        // get the currently logged in user's user id and store it in the new post data
        $blogUser = $this->blogUserDatabase->getUserByUsername($this->getLoggedInUsername());
        $blogPost->postuserid = $blogUser->userid;



        // check if the file was uploaded successfully
        $uploadok = 0;

        if ($_FILES['txtpostimage']['error'] == UPLOAD_ERR_OK) {
            $targetdir = "images/";
            $targetfile = $targetdir . basename($_FILES['txtpostimage']['name']);

            // check to make sure that the uploaded file is actually an image file
            $check = getimagesize($_FILES['txtpostimage']['tmp_name']);

            if ($check !== false) {
                //echo "file is an image";
                move_uploaded_file($_FILES['txtpostimage']['tmp_name'], $targetfile);
                $blogPost->postimage = $targetfile;
            } else {
                $errorMessages[] = "The uploaded file is not an image";
            }
        } elseif ($_FILES['txtpostimage']['error'] == UPLOAD_ERR_FORM_SIZE) {
            $errorMessages[] = "Uploaded image file size must be 5mb or less.";
        } elseif ($_FILES['txtpostimage']['error'] == UPLOAD_ERR_NO_FILE) {
            if (($formtype == 'new')) {
                $errorMessages[] = "No Article Image was uploaded";
            }
        }

        if ($formtype == "edit") {
            if (empty($_POST['txtpostid'])) {
                $errorMessages[] = "Please enter the Article Id";
            } else {
                $blogPost->postid = $this->test_input($_POST['txtpostid']);
            }
        }

        //echo "<pre>", print_r($blogpostobj), "</pre>";
        if (!empty($errorMessages)) {
            $this->view->setData("errorMessages",$errorMessages);
            $this->view->setData("blogPost", $blogPost);
            $this->view->setData("blogUser", $blogUser);
            $this->view->setData("username", $this->getLoggedInUsername());
            
            $this->view->setHeaderFile("views/userheader.php");
            $this->view->setContentFile(($formtype == 'edit') ? "views/users/usereditarticle.php" : "views/users/usernewarticle.php");
            $this->view->renderView();
            return;
        }


        if ($formtype == "new") {
            $blogPost->postreads = 0;
            $this->blogPostDatabase->addPost($blogPost);
            $successMessage = "Your New Article titled $blogPost->posttitle has been created successfully";
        } elseif ($formtype == "edit") {
            $this->blogPostDatabase->updatePost($blogPost);
            $successMessage = "Your Article titled $blogPost->posttitle has been updated successfully";
        }

        $this->userhome($successMessage);
    }

    public function usereditarticle() {

        if (!($this->isLoggedIn())) {
            $this->login();
            return;
        }

        if (!(isset($_GET['id']))) {
            $this->userhome();
            return;
        }

        $id = $this->test_input($_GET['id']);

        $blogPost = $this->blogPostDatabase->getPost($_GET['id']);

        if (!$blogPost) {
            $this->userhome();
            return;
        }
        //$buser = $this->bloguserdbobj->getUserById($bpost->postuserid);            
        $this->view->setData("username", $this->getLoggedInUsername());
        $this->view->setData("blogPost",$blogPost);
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setContentFile("views/users/usereditarticle.php");
        $this->view->renderView();
        
    }

    public function logout() {
        if ($this->isLoggedIn()) {
            $this->endSession();
            //echo "<br /> User logged out";
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
        if (!$this->isLoggedIn()) {
            $this->login();
            return;
        }

        if (!(isset($_GET['id']))) {
            $this->userhome();
            return;
        }


        $id = $this->test_input($_GET['id']);
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
        if (!$this->isLoggedIn()) {
            $this->login();
            return;
        }

        //echo "profile updated";
        $blogUser = new \BW\tools\bloguser();
        //echo "user profile";
        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            // get the details of current user to pre-fill the profile form


            $blogUser = $this->blogUserDatabase->getUserByUsername($this->getLoggedInUsername());
            
            $this->view->setData("username", $this->getLoggedInUsername());
            $this->view->setData("blogUser",$blogUser);
            $this->view->setHeaderFile("views/userheader.php");
            $this->view->setContentFile("views/users/userprofile.php");
            $this->view->renderView();
            
            return;
        }

        $blogUser->username = $this->getLoggedInUsername();

        if (empty($_POST['txtuserfirstname'])) {

            $errorMessages[] = "First Name is required";
        } else {

            $blogUser->userfirstname = $this->test_input($_POST['txtuserfirstname']);
            if (!preg_match("/^[a-zA-Z ]*$/", $blogUser->userfirstname)) {
                $errorMessages[] = "First Name : Only letters and white space allowed";
            }
        }



        if (empty($_POST['txtuserlastname'])) {

            $errorMessages[] = "Last Name is required";
        } else {

            $blogUser->userlastname = $this->test_input($_POST['txtuserlastname']);
            if (!preg_match("/^[a-zA-Z ]*$/", $blogUser->userlastname)) {
                $errorMessages[] = "Last Name : Only letters and white space allowed";
            }
        }



        $blogUser->userurl = $this->test_input($_POST['txtuserurl']);
        if (!filter_var($blogUser->userurl, FILTER_VALIDATE_URL)) {
            $errorMessages[] = "Please provide your website address in correct format e.g. (http://www.example.com)";
        }

        if (empty($_POST['txtuseremail'])) {

            $errorMessages[] = "Email is required";
        } else {

            $blogUser->useremail = $this->test_input($_POST['txtuseremail']);
            if (!filter_var($blogUser->useremail, FILTER_VALIDATE_EMAIL)) {
                $errorMessages[] = "Please provide email address in correct format.";
            }
        }

        if (!empty($errorMessages)) {
            $this->view->setData("username",$this->getLoggedInUsername());
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
        if (!$this->isLoggedIn()) {
            $this->login();
            return;
        }
        
            if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
                $this->view->setData("username",$this->getLoggedInUsername());
                $this->view->setHeaderFile("views/userheader.php");
                $this->view->setContentFile("views/users/userpassword.php");
                $this->view->renderView();
                
                return;
            }
           
                if (empty($_POST['txtuserpasswordcurrent'])) {
                    $errorMessages[] = "Please enter your current password.";
                } else {
                    $userpasswordcurrent = sha1($this->test_input($_POST['txtuserpasswordcurrent']));
                }

                if (empty($_POST['txtuserpasswordnew1']) || empty($_POST['txtuserpasswordnew2'])) {
                    $errorMessages[] = "Please enter both your new and confirmed passwords.";
                } else {
                    $userpasswordnew1 = sha1($this->test_input($_POST['txtuserpasswordnew1']));
                    $userpasswordnew2 = sha1($this->test_input($_POST['txtuserpasswordnew2']));

                    if ($userpasswordnew1 != $userpasswordnew2) {
                        $errorMessages[] = "Your new and confirmed passwords do not match.";
                    } else {
                        $userpassword = $userpasswordnew1;
                    }
                }

                $username = $this->getLoggedInUsername();
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
            
                $this->view->setData("username",$this->getLoggedInUsername());
                $this->view->setData("errorMessages",$errorMessages);
                $this->view->setHeaderFile("views/userheader.php");
                $this->view->setContentFile("views/users/userpassword.php");
                $this->view->renderView();
      
    }

}


class UserValidation{

    private function testInput($data) {

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }

    public function validateUserForm(Array $userForm,  $blogUser) {   

        $errorMessages = [];

        if (empty($userForm['txtusername'])) {

            $errorMessages[] = "User Name is required";
        } else {

            $blogUser->username = $this->testInput($userForm['txtusername']);

            if (!preg_match("/^[a-z]+\d*$/", $blogUser->username)) {
                $errorMessages[] = "User Name : Only letters a-z and numbers 0-9 allowed. Must start with letters, and then numbers, e.g gemini233";
            }
        }



        if (empty($userForm['txtuserfirstname'])) {

            $errorMessages[] = "First Name is required";
        } else {

            $blogUser->userfirstname = $this->testInput($userForm['txtuserfirstname']);
            if (!preg_match("/^[a-zA-Z ]*$/", $blogUser->userfirstname)) {
                $er_POSTrorMessages[] = "First Name : Only letters and white space allowed";
            }
        }



        if (empty($_POST['txtuserlastname'])) {

            $errorMessages[] = "Last Name is required";
        } else {

            $blogUser->userlastname = $this->testInput($userForm['txtuserlastname']);
            if (!preg_match("/^[a-zA-Z ]*$/", $blogUser->userlastname)) {
                $errorMessages[] = "Last Name : Only letters and white space allowed";
            }
        }



        $blogUser->userurl = $this->testInput($userForm['txtuserurl']);

        if (!filter_var($blogUser->userurl, FILTER_VALIDATE_URL)) {
            $errorMessages[] = "Please provide your website address in correct format e.g. (http://www.example.com)";
        }

        if (empty($userForm['txtuseremail'])) {

            $errorMessages[] = "Email is required";
        } else {

            $blogUser->useremail = $this->testInput($userForm['txtuseremail']);
            if (!filter_var($blogUser->useremail, FILTER_VALIDATE_EMAIL)) {
                $errorMessages[] = "Please provide email address in correct format.";
            }
        }



        if (empty($userForm['txtuserpassword']) || empty($userForm['txtuserpassword2'])) {

            $errorMessages[] = "Password is required";
        } else {
            if (!($userForm['txtuserpassword'] == $userForm['txtuserpassword2'])) {
                $errorMessages[] = "Please make sure that your chosen password and re-entered password match.";
            } else {
                $blogUser->userpassword = sha1($this->testInput($userForm['txtuserpassword']));
            }
        }

        $blogUser->regdate = time();

        return $errorMessages;
    }

}

?>