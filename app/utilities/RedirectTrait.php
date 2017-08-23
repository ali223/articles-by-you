<?php

namespace App\Utilities;

trait RedirectTrait
{
	protected function redirectTo($location)
	{
		if(!empty($location)) {
			header("Location: {$location}");
		}
	}

    protected function redirectIfUserNotLoggedIn()
    {
        if (!$this->sessionUtility->isLoggedIn()) {
            return $this->redirectTo('/login');
        }
    }

    protected function redirectIfUserLoggedIn()
    {
        if ($this->sessionUtility->isLoggedIn()) {
            return $this->redirectTo('/home');
        }
    }
	
}