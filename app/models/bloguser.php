<?php

namespace App\Models;

class BlogUser
{

    public $userId;
    public $userName;
    public $userFirstName;
    public $userLastName;
    public $userType;
    public $userUrl;
    public $userEmail;
    public $userRegDate;
    public $userPhoto;
    public $userPassword;

    

    public function __construct($id=null, $name=null, $firstName=null, $lastName=null, $type=null, $url=null, $email=null, $regDate=null, $photo=null, $password=null) 
    {

        $this->userId = $id;
        $this->userName= $name;
        $this->userFirstName= $firstName;
        $this->userLastName= $lastName;
        $this->userType= $type;
        $this->userUrl= $url;
        $this->userEmail= $email;
        $this->userRegDate= $regDate;
        $this->userPhoto= $photo;
        $this->userPassword = $password;
    }

}
