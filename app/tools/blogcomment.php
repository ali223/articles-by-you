<?phpnamespace App\tools;class BlogComment{    public $commentId;    public $commentPostId;    public $commentText;    public $commentDate;    public $commentIsVisible;    public $commentName;    public function __construct($id=null, $postId=null, $text=null, $date=null, $isVisible=null, $name=null) {        $this->commentId = $id;        $this->commentPostid = $postId;        $this->commentText = $text;        $this->commentDate = $date;        $this->commentIsVisible = $isVisible;        $this->commentName = $name;                    }}