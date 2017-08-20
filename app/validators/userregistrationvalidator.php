<?php

namespace App\validators;

use App\tools\BlogUser;
use App\tools\BlogUserDB;


class UserRegistrationValidator {
    use FilterInputTrait;



    public function validateUserForm(Array $userForm,  BlogUser $blogUser, BlogUserDB $blogUserDatabase) {   

        $errorMessages = [];

        if (empty($userForm['txtusername'])) {

            $errorMessages[] = "User Name is required";
        } else {

            $blogUser->userName = $this->filterInput($userForm['txtusername']);

            if (!preg_match("/^[a-z]+\d*$/", $blogUser->userName)) {
                $errorMessages[] = "User Name : Only letters a-z and numbers 0-9 allowed. Must start with letters, and then numbers, e.g gemini233";
            }
        }



        if (empty($userForm['txtuserfirstname'])) {

            $errorMessages[] = "First Name is required";
        } else {

            $blogUser->userFirstName = $this->filterInput($userForm['txtuserfirstname']);
            if (!preg_match("/^[a-zA-Z ]*$/", $blogUser->userFirstName)) {
                $er_POSTrorMessages[] = "First Name : Only letters and white space allowed";
            }
        }



        if (empty($_POST['txtuserlastname'])) {

            $errorMessages[] = "Last Name is required";
        } else {

            $blogUser->userLastName = $this->filterInput($userForm['txtuserlastname']);
            if (!preg_match("/^[a-zA-Z ]*$/", $blogUser->userLastName)) {
                $errorMessages[] = "Last Name : Only letters and white space allowed";
            }
        }



        $blogUser->userUrl = $this->filterInput($userForm['txtuserurl']);

        if (!filter_var($blogUser->userUrl, FILTER_VALIDATE_URL)) {
            $errorMessages[] = "Please provide your website address in correct format e.g. (http://www.example.com)";
        }

        if (empty($userForm['txtuseremail'])) {

            $errorMessages[] = "Email is required";
        } else {

            $blogUser->userEmail = $this->filterInput($userForm['txtuseremail']);
            if (!filter_var($blogUser->userEmail, FILTER_VALIDATE_EMAIL)) {
                $errorMessages[] = "Please provide email address in correct format.";
            }
        }



        if (empty($userForm['txtuserpassword']) || empty($userForm['txtuserpassword2'])) {

            $errorMessages[] = "Password is required";
        } else {
            if (!($userForm['txtuserpassword'] == $userForm['txtuserpassword2'])) {
                $errorMessages[] = "Please make sure that your chosen password and re-entered password match.";
            } else {
                $blogUser->userPassword = sha1($this->filterInput($userForm['txtuserpassword']));
            }
        }

        if ($blogUserDatabase->userExists($blogUser->userName)) {
            $errorMessages[] = "The User Name $blogUser->userName alreadys exists. Please choose a different user name.";
        }


        $blogUser->userRegDate = time();

        return $errorMessages;
    }

}
