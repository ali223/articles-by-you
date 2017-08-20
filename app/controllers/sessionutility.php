<?php
namespace App\controllers;

class SessionUtility {

    public function __construct()
    {
        if (!(session_status()==PHP_SESSION_ACTIVE))
          session_start();      
    }

    public function isLoggedIn(){
        
        return isset($_SESSION['username']);
    }
    
    public function getLoggedInUsername() {
        return isset($_SESSION['username']) ? $_SESSION['username'] : null;
    }
    
   public function storeInSession($username){
        $_SESSION['username'] = $username;       
   }
   public function endSession(){
        $_SESSION['username'] = '';
        session_destroy();       
   }

}