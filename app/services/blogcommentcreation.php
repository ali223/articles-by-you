<?php
namespace App\Services;

use App\Models\BlogCommentDB;
use App\Models\BlogComment;
use App\Utilities\InputUtility;
use App\Validators\FormValidator;

class BlogCommentCreation
{
    protected $commentDatabase;
	protected $input;
    protected $blogComment;
	protected $validator;

	public function __construct(BlogCommentDB $commentDatabase, InputUtility $input, BlogComment $blogComment, FormValidator $validator)
	{
        $this->commentDatabase = $commentDatabase;
		$this->input = $input;
        $this->blogComment = $blogComment;
		$this->validator = $validator;
	}

	public function createComment()
	{

        $errorMessages = $this->validateCommentForm($this->input->posts());

        if ($errorMessages) {
            throw new BlogCommentCreationException('Comment Form Errors', $errorMessages);            
        }

        $blogComment = $this->blogComment->setData($this->input->posts());

        $blogComment->commentDate = time();

        $blogComment->commentIsVisible = 1;

        $this->commentDatabase->addComment($blogComment);

        return $this->blogComment;

	}

	public function getOldPostData()
	{
		return $this->blogComment->setData($this->input->posts());
	}

    protected function validateCommentForm($postData)
    {
         return ($this->validator->setPostData($postData))
                ->validateRequireds([
                    'commentName' => 'Please enter your name',
                    'commentText' => 'Please enter your comment',
                    'commentPostId' => 'Please enter your comment post id'
                ])->getValidationErrors();
            
    }

}