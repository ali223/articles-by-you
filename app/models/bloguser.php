<?php

namespace App\Models;

class BlogUser
{

    public $userId = '';
    public $userName = '';
    public $userFirstName = '';
    public $userLastName = '';
    public $userType = '';
    public $userUrl = '';
    public $userEmail = '';
    public $userRegDate = '';
    public $userPhoto = '';
    public $userPassword = '';

    

    public function __construct($userData = []) 
    {

        
        $this->setData($userData);

        // $this->userId = isset($userData['userId']) ? $userData['userId'] : null;
        // $this->userName= $name;
        // $this->userFirstName= $firstName;
        // $this->userLastName= $lastName;
        // $this->userType= $type;
        // $this->userUrl= $url;
        // $this->userEmail= $email;
        // $this->userRegDate= $regDate;
        // $this->userPhoto= $photo;
        // $this->userPassword = $password;
    }

    public function setData($userData = [])
    {
        foreach($userData as $field => $data) {
            if(isset($this->$field)) {
                $this->$field = 
                $field == 'userPassword' ? sha1($data) : $data;
            }
        }

        return $this;

    }


}
