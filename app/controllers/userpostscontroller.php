<?php
namespace app\controllers;

use app\models\BlogPostDB;
use app\models\BlogUserDB;
use app\models\BlogPost;
use app\models\BlogComment;
use app\validators\FilterInputTrait;
use app\validators\FormValidator;

use app\utilities\RedirectTrait;


class UserPostsController 
{
    use FilterInputTrait, RedirectTrait;

    protected $blogPostDatabase;
    protected $blogUserDatabase;
    protected $view;
    protected $sessionUtility;

    public function __construct(BlogUserDB $blogUserDatabase, BlogPostDB $blogPostDatabase, SessionUtility $sessionUtility, View $view) 
    {

        $this->blogUserDatabase = $blogUserDatabase;
        $this->blogPostDatabase = $blogPostDatabase;
        
        $this->view = $view;
        $this->sessionUtility = $sessionUtility;
    }
   
   public function show()
   {
        if (!$this->sessionUtility->isLoggedIn()) {
            return $this->redirectTo('/login');
        }

        $errorMessages = [];

        if (!isset($_GET['id'])) {
            return $this->redirectTo('/home');
        }

        $id = $this->filterInput($_GET['id']);

        $blogPost = $this->blogPostDatabase->getPost($id);

        if (!$blogPost) {
            return $this->redirectTo('/home');
        }

        $blogUser = $this->blogUserDatabase->getUserById($blogPost->postUserId);
        
        $this->view->setData("blogPost", $blogPost);
        $this->view->setData("blogUser", $blogUser);
        $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setContentFile("views/users/userviewarticle.php");
        $this->view->renderView();
   }

}

?>