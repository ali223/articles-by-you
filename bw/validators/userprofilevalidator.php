<?php

namespace BW\validators;

use BW\tools\BlogUser;
use BW\tools\BlogUserDB;


class UserProfileValidator{
    use FilterInputTrait;


   
    public function validateProfileForm(Array $userForm,  BlogUser $blogUser) {   

        $errorMessages = [];

         if (empty($userForm['txtuserfirstname'])) {

            $errorMessages[] = "First Name is required";
        } else {

            $blogUser->userFirstName = $this->filterInput($userForm['txtuserfirstname']);
            if (!preg_match("/^[a-zA-Z ]*$/", $blogUser->userFirstName)) {
                $errorMessages[] = "First Name : Only letters and white space allowed";
            }
        }



        if (empty($userForm['txtuserlastname'])) {

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


   
        return $errorMessages;
    }

}
