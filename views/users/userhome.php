<?phpnamespace app;?><div class="content">    <h2>Welcome  <?= isset($username) ? $username : ' User' ?></h2>    <h2><?= isset($message) ? $message: null; ?></h2>    <?php    if(empty($blogPostsList)) {          echo "<h3> You do not have any articles at the moment</h3>";    } else {     echo "<h3>  Here are your articles </h3>";    }    ?>    <table class="userpoststable" border="1">        <tr>            <th>Post Title</th>            <th>Date Posted</th>            <th>Times Read</th>            <th>Status</th>            <th colspan="3">Actions</th>        </tr>                   <?php               if(!empty($blogPostsList)) :            foreach($blogPostsList as $singleBlogPost) :        ?>        <tr>            <td><?=$singleBlogPost->postTitle; ?></td>            <td><?=$singleBlogPost->postDate; ?></td>            <td><?=$singleBlogPost->postReads; ?></td>            <td><?=($singleBlogPost->postIsVisible==1)? "Published" : "Draft" ?></td>            <td>                <a href="/viewarticle?id=<?= isset($singleBlogPost->postId) ? $singleBlogPost->postId : '' ?>">View                </a>            </td>            <td>                <a href="/editarticle?id=<?= isset($singleBlogPost->postId) ? $singleBlogPost->postId : '' ?>">Edit                </a>            </td>            <td>                <a onclick=" return confirm('Do you want to delete this article?')" href="/deletearticle?id=<?= isset($singleBlogPost->postId) ? $singleBlogPost->postId : '' ?>">Delete                </a>            </td>        </tr>    <?php            endforeach;        endif;    ?>    </table></div>