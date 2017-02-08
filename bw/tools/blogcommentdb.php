<?phpnamespace BW\tools;use PDO;use PDOException;use BW\validators\FilterInputTrait;class BlogCommentDB {    use FilterInputTrait;    private $mydb;    public function __construct(Database $db) {        $this->mydb = $db->getConnection();    }    public function addComment(BlogComment $blogComment) {        try {            $stmt = $this->mydb->prepare("insert into blog_comments"                    . "(commentpostid, commenttext, commentdate , commentisvisible, commentname)"                    . "values(:pcommentpostid, :pcommenttext, :pcommentdate, :pcommentisvisible, :pcommentname)");            $stmt->bindValue(":pcommentpostid", $blogComment->commentPostId);            $stmt->bindValue(":pcommenttext", $blogComment->commentText);            $stmt->bindValue(":pcommentdate", $blogComment->commentDate);            $stmt->bindValue(":pcommentisvisible", $blogComment->commentIsVisible);            $stmt->bindValue(":pcommentname", $blogComment->commentName);            $result = $stmt->execute();            return $stmt->rowCount();        } catch (PDOException $pe) {            error_log("<br /> error occurred " . $pe->getMessage());            return false;        }    }    public function getCommentsByPost($postId) {        if(!isset($postId)) return false;        try {            $stmt = $this->mydb->prepare("select * from blog_comments where commentpostid=:pcommentpostid");            $stmt->bindValue(":pcommentpostid", $postId);            $stmt->execute();            $stmt->setFetchMode(PDO::FETCH_ASSOC);            $blogCommentsList = array();            while ($row = $stmt->fetch()) {                $blogComment = new BlogComment();                $blogComment->commentId = $this->filterInput($row['commentid']);                $blogComment->commentText = $this->filterInput($row['commenttext']);                $blogComment->commentDate = $this->filterInput($row['commentdate']);                $blogComment->commentIsVisible = $this->filterInput($row['commentisvisible']);                $blogComment->commentName = $this->filterInput($row['commentname']);                $blogCommentsList[] = $blogComment;            }            return $blogCommentsList;        } catch (PDOException $pe) {            error_log("<br /> Error occurred " . $pe->getMessage());        }    }}