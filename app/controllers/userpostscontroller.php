<?php
namespace App\Controllers;

use App\Models\BlogPostDB;
use App\Models\BlogUserDB;
use App\Models\BlogPost;
use App\Models\BlogComment;
use App\Validators\FilterInputTrait;
use App\Validators\FormValidator;

use App\Utilities\RedirectTrait;
use App\Utilities\SessionUtility;

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
        $this->view->setHeaderFile("views/userheader.php");
        $this->view->setData("username", $this->sessionUtility->getLoggedInUsername());

    }    

    public function index() 
    {

        $this->redirectIfUserNotLoggedIn();

        $message = '';

        if($this->sessionUtility->has('message')) {
            $message = $this->sessionUtility->getAndRemove('message');
        }
       
        $blogPostsList = $this->blogPostDatabase->getPostsByUser($this->sessionUtility->getLoggedInUsername());
        

        return $this->view->show('users/userhome', [
                'message' => $message,
                'blogPostsList' => $blogPostsList
            ]);
               
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
        
        return $this->view->show('users/userviewarticle', [
                'blogPost' => $blogPost,
                'blogUser' => $blogUser
            ]);

    }

    public function create() 
    {

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {
                                
            return $this->view->show('users/usernewarticle');
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

            return $this->view->show('users/usernewarticle', [
                'errorMessages' => $errorMessages,
                'blogPost' => $blogPost,
                'blogUser' => $blogUser
            ]);
        }
            
        $blogPost->postImage = $this->uploadFile($_FILES, 'txtpostimage');

        $this->blogPostDatabase->addPost($blogPost);

        $this->sessionUtility->put('message', "Your New Article titled $blogPost->postTitle has been created successfully");

        return $this->redirectTo('/home');
    }

    public function update() 
    {

        if (!($_SERVER['REQUEST_METHOD'] == 'POST')) {

            return $this->view->show('users/usereditarticle');
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
            return $this->view->show('users/usereditarticle', [
                'errorMessages' => $errorMessages,
                'blogPost' => $blogPost,
                'blogUser' => $blogUser
            ]);            
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
            $targetdir . basename($fileData[$field]['name']);

        if(move_uploaded_file($fileData[$field]['tmp_name'], $targetFile)) {
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

    public function edit() 
    {

        if (!(isset($_GET['id']))) {
            return $this->redirectTo('/home');
        }

        $id = $this->filterInput($_GET['id']);

        $blogPost = $this->blogPostDatabase->getPost($_GET['id']);

        if (!$blogPost) {
            return $this->redirectTo('/home');
        }

        return $this->view->show('users/usereditarticle', 
                            compact('blogPost'));
        
    }

    public function destroy() 
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