<?php

namespace BW\tools;

 class BlogUser{

    public $userid;

    public $username;

    public $userfirstname;

    public $userlastname;

    public $usertype;

    public $userurl;

    public $useremail;

    public $userregdate;

    public $userphoto;

    public $userpassword;

    

    public function __construct($id=null, $name=null, $firstname=null, $lastname=null, $type=null, $url=null, $email=null, $regdate=null, $photo=null, $password=null) {

        $this->userid = $id;

        $this->username= $name;

        $this->userfirstname= $firstname;

        $this->userlastname= $lastname;

        $this->usertype= $type;

        $this->userurl= $url;

        $this->useremail= $email;

        $this->userregdate= $regdate;

        $this->userphoto= $photo;

        $this->userpassword = $password;

    }

}



