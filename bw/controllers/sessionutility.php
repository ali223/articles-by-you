<?php
namespace BW\controllers;

class SessionUtility {

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

}