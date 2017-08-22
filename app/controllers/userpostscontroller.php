<?php
namespace app\controllers;

use app\models\BlogPostDB;
use app\models\BlogUserDB;
use app\models\BlogPost;
use app\models\BlogComment;
use app\validators\FilterInputTrait;
use app\validators\FormValidator;

use app\utilities\RedirectTrait;
use app\utilities\SessionUtility;

class UserPostsController 
{
    use FilterInputTrait, RedirectTrait;

    protected $blogPostDatabase;
    protected $blogUserDatabase;
    protected $view;
    protected $sessionUtility;

    public function __construct(BlogUserDB $blogUserDatabase, BlogPostDB $blogPostDatabase, SessionUtility $sessionUtility, View $view) 
    {

        $this->sessionUtility = $sessionUtility;

        $this->redirectIfUserNotLoggedIn();

        $this->blogUserDatabase = $blogUserDatabase;
        $this->blogPostDatabase = $blogPostDatabase;

        $this->view = $view;

    }    
   
    public function show()
    {

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

    public function usernewarticle() 
    {

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            
            $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
            $this->view->setHeaderFile("views/userheader.php");            
            $this->view->setContentFile("views/users/usernewarticle.php");
            $this->view->renderView();
                     
            return;
        }

        
        $blogPost = $this->createBlogPostFromPostData($_POST);

        $validator = new FormValidator($_POST);

        $validator->validateRequireds([
                'txtposttitle' => 'Please enter the Article Title',
                'txtpostdesc' => 'Please enter the Article Description',
                'txtposttext' => 'Please enter the Article Text'
        ])->validateUploadedFile($_FILES, 'txtpostimage');

        $errorMessages = $validator->getValidationErrors();

        $blogUser = $this->blogUserDatabase->getUserByUsername($this->sessionUtility->getLoggedInUsername());

        $blogPost->postUserId = $blogUser->userId;
     
        if (!empty($errorMessages)) {
            $this->view->setData("errorMessages",$errorMessages);
            $this->view->setData("blogPost", $blogPost);
            $this->view->setData("blogUser", $blogUser);
            $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
            
            $this->view->setHeaderFile("views/userheader.php");
            $this->view->setContentFile("views/users/usernewarticle.php");
            $this->view->renderView();
            return;
        }
            
        $blogPost->postImage = $this->uploadFile($_FILES, 'txtpostimage');

        $this->blogPostDatabase->addPost($blogPost);

        $this->sessionUtility->put('message', "Your New Article titled $blogPost->postTitle has been created successfully");

        return $this->redirectTo('/home');
    }

    public function userupdatearticle() 
    {

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
            
            $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
            $this->view->setHeaderFile("views/userheader.php");            
            $this->view->setContentFile("views/users/usereditarticle.php");
            $this->view->renderView();
                     
            return;
        }

        
        $blogPost = $this->createBlogPostFromPostData($_POST);

        $blogPost->postId = $this->filterInput($_POST['txtpostid']);

        $validator = new FormValidator($_POST);

        $validator->validateRequireds([
                'txtpostid' => 'Please enter the Article Id',
                'txtposttitle' => 'Please enter the Article Title',
                'txtpostdesc' => 'Please enter the Article Description',
                'txtposttext' => 'Please enter the Article Text',
        ]);

        if($_FILES['txtpostimage']['name']) {
            $validator->validateUploadedFile($_FILES, 'txtpostimage');
        }

        $errorMessages = $validator->getValidationErrors();

        $blogUser = $this->blogUserDatabase->getUserByUsername($this->sessionUtility->getLoggedInUsername());

        $blogPost->postUserId = $blogUser->userId;
     
        if (!empty($errorMessages)) {
            $this->view->setData("errorMessages",$errorMessages);
            $this->view->setData("blogPost", $blogPost);
            $this->view->setData("blogUser", $blogUser);
            $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
            
            $this->view->setHeaderFile("views/userheader.php");
            $this->view->setContentFile("views/users/usereditarticle.php");
            $this->view->renderView();
            return;
        }
            
        $blogPost->postImage = $this->uploadFile($_FILES, 'txtpostimage');

        $this->blogPostDatabase->updatePost($blogPost);

        $this->sessionUtility->put('message', "Your Article titled $blogPost->postTitle has been updated successfully");

        return $this->redirectTo('/home');

    }


    protected function uploadFile($fileData, $field)
    {
        $targetdir = "images/";

        $targetFile = 
            $targetdir . basename($_FILES['txtpostimage']['name']);

        if(move_uploaded_file($_FILES['txtpostimage']['tmp_name'], $targetFile)) {
            return $targetFile;
        }

        return '';

    }

    protected function createBlogPostFromPostData($postData)
    {
        $blogPost = new BlogPost();

        $blogPost->postTitle = 
                    $this->filterInput($postData['txtposttitle']);

        $blogPost->postDesc = 
                    $this->filterInput($postData['txtpostdesc']);

        $blogPost->postText = 
                    $this->filterInput($postData['txtposttext']);

        $blogPost->postIsVisible = 
                    $this->filterInput($postData['txtpostisvisible']);

        $blogPost->postReads = 0;            

        $blogPost->postDate = time();

        return $blogPost;
    }

    public function usereditarticle() 
    {

        if (!(isset($_GET['id']))) {
            return $this->redirectTo('/home');
        }

        $id = $this->filterInput($_GET['id']);

        $blogPost = $this->blogPostDatabase->getPost($_GET['id']);

        if (!$blogPost) {
            return $this->redirectTo('/home');
        }

        $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());
        $this->view->setData("blogPost",$blogPost);
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setContentFile("views/users/usereditarticle.php");
        $this->view->renderView();
        
    }

    public function userdeletearticle() 
    {

        if (!(isset($_GET['id']))) {
            return $this->redirectTo('/home');   
        }


        $id = $this->filterInput($_GET['id']);
        $blogPost = $this->blogPostDatabase->getPost($id);

        if (!$blogPost) {
            return $this->redirectTo('/home');               
        }

        $this->blogPostDatabase->deletePost($id);

        $this->sessionUtility->put('message', "Your Article titled $blogPost->postTitle has been deleted successfully");

        return $this->redirectTo('/home');
    }

}

?>