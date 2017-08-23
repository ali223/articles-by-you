<?phpnamespace App\Controllers;use App\Models\BlogPostDB;use App\Models\BlogCommentDB;use App\Models\BlogUserDB;use App\Models\BlogPost;use App\Models\BlogComment;use App\Validators\FilterInputTrait;use App\Validators\FormValidator;class PostsController {    use FilterInputTrait;    protected $blogPostDatabase;    protected $blogUserDatabase;    protected $blogCommentDatabase;    protected $view;    public function __construct(BlogUserDB $blogUserDatabase, BlogPostDB $blogPostDatabase, BlogCommentDB $blogCommentDatabase, View $view) {        $this->blogUserDatabase = $blogUserDatabase;        $this->blogPostDatabase = $blogPostDatabase;        $this->blogCommentDatabase = $blogCommentDatabase;        $this->view = $view;    }       public function index() {        $blogPostsList = $this->blogPostDatabase->getVisiblePosts();        foreach ($blogPostsList as $singleBlogPost) {            $blogUsersList[$singleBlogPost->postUserId] = $this->blogUserDatabase->getUserById($singleBlogPost->postUserId);        }        $this->view->setData("blogPostsList",$blogPostsList);        $this->view->setData("blogUsersList",$blogUsersList);                $this->view->setContentFile("views/posts/index.php");        $this->view->renderView();    }    public function show() {        if ($_SERVER['REQUEST_METHOD'] == 'POST') {               $validator = new FormValidator($_POST);            $errorMessages = $validator->validateRequireds([                'txtcommentname' => 'Please enter your name',                'txtcommenttext' => 'Please enter your comment',                'txtcommentpostid' => 'Please enter your comment post id'            ])->getValidationErrors();            $blogComment = $this->createBlogCommentFromPostData($_POST);            if(empty($errorMessages)) {                $this->blogCommentDatabase->addComment($blogComment);            } else {                $this->view->setData("errorMessages",$errorMessages);                $this->view->setData('blogComment', $blogComment);            }        }        if (!isset($_GET['id'])) {            $this->error("No post id mentioned");            return;        }        $id = htmlspecialchars($_GET['id']);        $blogPost = $this->blogPostDatabase->getPost($id);        if (!$blogPost instanceof BlogPost) {            $this->error("Cannot find the post with id $id");            return;        }        $blogUser = $this->blogUserDatabase->getUserById($blogPost->postUserId);        $this->blogPostDatabase->updatePostRead($blogPost->postId);        $blogCommentsList = $this->blogCommentDatabase->getCommentsByPost($blogPost->postId);        $pageTitle = "Welcome to Articles By U --- $blogPost->postTitle";        $this->view->setData("pageTitle",$pageTitle);        $this->view->setData("blogPost",$blogPost);        $this->view->setData("blogUser",$blogUser);        $this->view->setData("blogCommentsList",$blogCommentsList);                $this->view->setContentFile("views/posts/show.php");        $this->view->renderView();         }    public function error($errorMessage) {        $this->view->setData("errorMessage",$errorMessage);        $this->view->setContentFile("views/posts/error.php");        $this->view->renderView();            }    public function createBlogCommentFromPostData($postData)    {        $blogComment = new BlogComment();        $blogComment->commentName =                     $this->filterInput($postData['txtcommentname']);        $blogComment->commentText =                     $this->filterInput($postData['txtcommenttext']);        $blogComment->commentPostId =                     $this->filterInput($postData['txtcommentpostid']);        $blogComment->commentDate = time();        $blogComment->commentIsVisible = 1;        return $blogComment;    }    public function search() {        if ($_SERVER['REQUEST_METHOD'] == 'POST') {            $searchtext = '';            if (!empty($_POST['txtsearch'])) {                $searchtext = $this->filterInput($_POST['txtsearch']);                $this->view->setData('searchtext', $searchtext);            }            $blogposts = $this->blogPostDatabase->getPostsByText($searchtext);            $this->view->setData('blogposts', $blogposts);        }        $this->view->setContentFile("views/posts/search.php");        $this->view->renderView();    }}?>