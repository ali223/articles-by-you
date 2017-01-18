<?php

namespace BW\controllers;


class BaseController {
    protected $database;
    protected $blogPostDatabase;
    protected $blogUserDatabase;
    protected $blogCommentDatabase;
    protected $view;
    
    public function __construct($database, $blogPostDatabase, $blogUserDatabase, $blogCommentDatabase, $view) {
        // use dependency injection instead
        
        $this->database = $database;

        $this->blogPostDatabase = $blogPostDatabase;
        $this->blogUserDatabase = $blogUserDatabase;
        $this->blogCommentDatabase = $blogCommentDatabase;
        $this->view = $view;
       
    }
    public function isLoggedIn(){
        if (!(session_status()==PHP_SESSION_ACTIVE))
            session_start();
        
        return isset($_SESSION['username']);
    }
    
    public function getLoggedInUsername() {
       if (!(session_status()==PHP_SESSION_ACTIVE))
            session_start();
        return isset($_SESSION['username']) ? $_SESSION['username'] : null;
    }
    
   public function storeInSession($username){
       if (!(session_status()==PHP_SESSION_ACTIVE))
            session_start();
       $_SESSION['username'] = $username;       
   }
   public function endSession(){
        if (!(session_status()==PHP_SESSION_ACTIVE))
            session_start();
          $_SESSION['username'] = '';
          session_destroy();
       
   }
   
     protected function test_input($data) {

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }

}
    

