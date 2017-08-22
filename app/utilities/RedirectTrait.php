<?php

namespace app\utilities;

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
	
}