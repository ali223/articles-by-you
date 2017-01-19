<?php

namespace BW\validators;

use BW\tools\bloguser;
use BW\tools\bloguserdb;


class UserProfileValidator extends Validator{

   
    public function validateProfileForm(Array $userForm,  bloguser $blogUser) {   

        $errorMessages = [];

         if (empty($userForm['txtuserfirstname'])) {

            $errorMessages[] = "First Name is required";
        } else {

            $blogUser->userfirstname = $this->filterInput($userForm['txtuserfirstname']);
            if (!preg_match("/^[a-zA-Z ]*$/", $blogUser->userfirstname)) {
                $errorMessages[] = "First Name : Only letters and white space allowed";
            }
        }



        if (empty($userForm['txtuserlastname'])) {

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


   
        return $errorMessages;
    }

}
