<?php
namespace App\Services;

use App\Models\BlogUserDB;
use App\Models\BlogUser;
use App\Models\BlogPostDB;
use App\Models\BlogPost;
use App\Utilities\InputUtility;
use App\Utilities\SessionUtility;
use App\Validators\FormValidator;

class BlogPostUpdation
{
	protected $userDatabase;
    protected $postDatabase;
	protected $input;
	protected $blogUser;
    protected $blogPost;
	protected $validator;
    protected $session;

	public function __construct(BlogUserDB $userDatabase, BlogPostDB $postDatabase, InputUtility $input, BlogUser $blogUser, BlogPost $blogPost, FormValidator $validator, SessionUtility $session)
	{
		$this->userDatabase = $userDatabase;
        $this->postDatabase = $postDatabase;
		$this->input = $input;
		$this->blogUser = $blogUser;
        $this->blogPost = $blogPost;
		$this->validator = $validator;
        $this->session = $session;
	}

	public function updatePost()
	{
        $this->blogPost = $this->blogPost->setData($this->input->posts());

        $this->blogPost->postDate = strtotime($this->blogPost->postDate);

        $errorMessages = $this->validatePostForm($this->input->posts());
        
        $this->blogUser = $this->userDatabase->getUserByUsername($this->session->getLoggedInUsername());

        $this->blogPost->postUserId = $this->blogUser->userId;
     
        if ($errorMessages) {
            throw new BlogPostUpdationException('Post Form Errors', $errorMessages);            
        }
            
        $this->blogPost->postImage = $this->uploadFile($_FILES, 'postImage');

        $this->postDatabase->updatePost($this->blogPost);

        return $this->blogPost;

	}

	public function getOldPostData()
	{
		return $this->blogPost->setData($this->input->posts());
	}

    protected function validatePostForm($postData)
    {
         $this->validator->setPostData($this->input->posts())
                ->validateRequireds([
                    'postId' => 'Please enter the Article Id',
                    'postTitle' => 'Please enter the Article Title',
                    'postDesc' => 'Please enter the Article Description',
                    'postText' => 'Please enter the Article Text'
                ]);

        if($_FILES['postImage']['name']) {
            $validator->validateUploadedFile($_FILES, 'postImage');
        }

        return $this->validator->getValidationErrors();
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

}