<?php
namespace App\Utilities;

class SessionUtility 
{

    public function __construct()
    {
        if (!(session_status()==PHP_SESSION_ACTIVE))
          session_start();      
    }

    public function isLoggedIn()
    {     
        return isset($_SESSION['username']);
    }

    public function getLoggedInUserId() 
    {
        return isset($_SESSION['userid']) ? $_SESSION['userid'] : null;
    }
    
    public function getLoggedInUsername() 
    {
        return isset($_SESSION['username']) ? $_SESSION['username'] : null;
    }

    public function loginUser($username)
    {
        $_SESSION['username'] = $username;
    }
    
    public function put($key, $value)
    {
        $_SESSION[$key] = $value;       
    }

    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function getAndRemove($key)
    {
        if(isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            $_SESSION[$key] = null;
            return $value;
        }

        return false;

    }


    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function remove($key)
    {
        if(isset($_SESSION[$key])) {
            $_SESSION[$key] = null;
            return true;
        }

        return false;
    }

    public function endSession()
    {
        $_SESSION = [];
        session_destroy();       
    }

}