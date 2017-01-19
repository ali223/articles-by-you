<?php

namespace BW\validators;

use BW\tools\bloguser;
use BW\tools\bloguserdb;


class UserRegistrationValidator extends Validator{


    public function validateUserForm(Array $userForm,  bloguser $blogUser, bloguserdb $blogUserDatabase) {   

        $errorMessages = [];

        if (empty($userForm['txtusername'])) {

            $errorMessages[] = "User Name is required";
        } else {

            $blogUser->username = $this->filterInput($userForm['txtusername']);

            if (!preg_match("/^[a-z]+\d*$/", $blogUser->username)) {
                $errorMessages[] = "User Name : Only letters a-z and numbers 0-9 allowed. Must start with letters, and then numbers, e.g gemini233";
            }
        }



        if (empty($userForm['txtuserfirstname'])) {

            $errorMessages[] = "First Name is required";
        } else {

            $blogUser->userfirstname = $this->filterInput($userForm['txtuserfirstname']);
            if (!preg_match("/^[a-zA-Z ]*$/", $blogUser->userfirstname)) {
                $er_POSTrorMessages[] = "First Name : Only letters and white space allowed";
            }
        }



        if (empty($_POST['txtuserlastname'])) {

            $errorMessages[] = "Last Name is required";
        } else {

            $blogUser->userlastname = $this->filterInput($userForm['txtuserlastname']);
            if (!preg_match("/^[a-zA-Z ]*$/", $blogUser->userlastname)) {
                $errorMessages[] = "Last Name : Only letters and white space allowed";
            }
        }



        $blogUser->userurl = $this->filterInput($userForm['txtuserurl']);

        if (!filter_var($blogUser->userurl, FILTER_VALIDATE_URL)) {
            $errorMessages[] = "Please provide your website address in correct format e.g. (http://www.example.com)";
        }

        if (empty($userForm['txtuseremail'])) {

            $errorMessages[] = "Email is required";
        } else {

            $blogUser->useremail = $this->filterInput($userForm['txtuseremail']);
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
                $blogUser->userpassword = sha1($this->filterInput($userForm['txtuserpassword']));
            }
        }

        if ($blogUserDatabase->userExists($blogUser->username)) {
            $errorMessages[] = "The User Name $blogUser->username alreadys exists. Please choose a different user name.";
        }


        $blogUser->regdate = time();

        return $errorMessages;
    }

}
