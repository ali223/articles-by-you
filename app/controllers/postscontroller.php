<?php
namespace App\Controllers;

use App\Models\BlogPostDB;
use App\Models\BlogCommentDB;
use App\Models\BlogUserDB;
use App\Models\BlogPost;
use App\Models\BlogComment;
use App\Validators\FormValidator;

use App\Utilities\InputUtility;

use App\Services\BlogCommentCreation;
use App\Services\BlogCommentCreationException;


class PostsController 
{

    protected $blogPostDatabase;
    protected $blogUserDatabase;
    protected $blogCommentDatabase;
    protected $view;

    public function __construct(BlogUserDB $blogUserDatabase, BlogPostDB $blogPostDatabase, BlogCommentDB $blogCommentDatabase, View $view) 
    {

        $this->blogUserDatabase = $blogUserDatabase;
        $this->blogPostDatabase = $blogPostDatabase;
        $this->blogCommentDatabase = $blogCommentDatabase;
        $this->view = $view;
    }
   
    public function index() 
    {
        $blogPostsList = $this->blogPostDatabase->getVisiblePosts();

        foreach ($blogPostsList as $singleBlogPost) {
            $blogUsersList[$singleBlogPost->postUserId] = $this->blogUserDatabase->getUserById($singleBlogPost->postUserId);
        }

        return $this->view->show('posts/index', [
                'blogPostsList' => $blogPostsList,
                'blogUsersList' => $blogUsersList
            ]);
    }

    public function show(InputUtility $input, BlogCommentCreation $commentCreation) 
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            try {

                $blogComment = $commentCreation->createComment();

            } catch (BlogCommentCreationException $exception) {

                $this->view->setData("errorMessages", 
                            $exception->getErrorMessages());
                
                $this->view->setData('blogComment', 
                            $commentCreation->getOldPostData());
            }
   
        }

        $postId = $input->get('id');

        if (is_null($postId)) {
            $errorMessage = "No post id mentioned";
            return $this->view
                    ->show('posts/error', compact('errorMessage'));
        }

        $blogPost = $this->blogPostDatabase->getPost($postId);

        if (!$blogPost instanceof BlogPost) {
            $errorMessage = "Cannot find the post with id {$postId}";
            return $this->view
                    ->show('posts/error', compact('errorMessage'));
        }

        $blogUser = $this->blogUserDatabase->getUserById($blogPost->postUserId);

        $this->blogPostDatabase->updatePostRead($blogPost->postId);

        $blogCommentsList = $this->blogCommentDatabase->getCommentsByPost($blogPost->postId);

        $pageTitle = "Welcome to Articles By U --- $blogPost->postTitle";

        return $this->view->show('posts/show', [
                'blogPost' => $blogPost,
                'blogUser' => $blogUser,
                'pageTitle' => $pageTitle,
                'blogCommentsList' => $blogCommentsList
            ]);

    }

    public function search(InputUtility $input) 
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $searchText = $input->post('txtsearch');

            if ($searchText) {
                $this->view->setData('searchText', $searchText);
            }

            $blogPosts = $this->blogPostDatabase->getPostsByText($searchText);

            $this->view->setData('blogPosts', $blogPosts);
        }

        return $this->view->show('posts/search');
    }

}