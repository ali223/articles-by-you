<?php
namespace App\Services;

use App\Models\BlogPostDB;
use App\Models\BlogPost;
use App\Utilities\InputUtility;
use App\Utilities\SessionUtility;
use App\Validators\FormValidator;

class BlogPostUpdation
{
    protected $postDatabase;
	protected $input;
    protected $blogPost;
	protected $validator;
    protected $session;

	public function __construct(BlogPostDB $postDatabase, InputUtility $input, BlogPost $blogPost, FormValidator $validator, SessionUtility $session)
	{
        $this->postDatabase = $postDatabase;
		$this->input = $input;
        $this->blogPost = $blogPost;
		$this->validator = $validator;
        $this->session = $session;
	}

	public function updatePost()
	{
        $errorMessages = $this->validatePostForm($this->input->posts(), $this->input->files());

        if ($errorMessages) {
            throw new BlogPostUpdationException('Post Form Errors', $errorMessages);            
        }

        $this->blogPost = $this->blogPost->setData($this->input->posts());

        $this->blogPost->postDate = strtotime($this->blogPost->postDate);
        
        $this->blogPost->postUserId = $this->session->getLoggedInUserId();
                 
        $this->blogPost->postImage = $this->uploadFile($this->input->files(), 'postImage');

        $this->postDatabase->updatePost($this->blogPost);

        return $this->blogPost;

	}

	public function getOldPostData()
	{
		return $this->blogPost->setData($this->input->posts());
	}

    protected function validatePostForm($postData, $fileData = [])
    {
         $this->validator->setPostData($this->input->posts())
                ->validateRequireds([
                    'postId' => 'Please enter the Article Id',
                    'postTitle' => 'Please enter the Article Title',
                    'postDesc' => 'Please enter the Article Description',
                    'postText' => 'Please enter the Article Text'
                ]);

        if($fileData['postImage']['name']) {
            $this->validator->validateUploadedFile($fileData, 'postImage');
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