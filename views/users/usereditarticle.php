<?phpnamespace BW;?><div class="content">    <h2>Welcome  <?= isset($username) ? $username : ' User' ?></h2>    <?php//print_r($errormsgs);if(!empty($errorMessages)) {    echo '<ul class="errorslist">';    foreach($errorMessages as $singleError) {        echo "<li>$singleError</li>";            }    echo "</ul>";}?>        <?php //echo "<pre>", print_r($result), "</pre>"; ?>    <form method="post" enctype="multipart/form-data" action="/newarticle">        <table class="usereditposttable">                <tr>            <th class="userpostheading" colspan="2">Edit Your Article</th>        </tr>        <tr>            <td colspan="2"><em>* Required fields</em></td>        </tr>        <tr>            <td>Article Title</td>            <td><input type="text" name="txtposttitle" value="<?= isset($blogPost->posttitle) ? $blogPost->posttitle : null; ?>" />*</td>                    </tr>        <tr>            <td>Article Description</td>            <td><input type="text" name="txtpostdesc" value="<?= isset($blogPost->postdesc) ? $blogPost->postdesc : null; ?>" />*</td>                    </tr>        <tr>            <td>Article Text</td>            <td><textarea name="txtposttext" rows="10" cols="80"><?= isset($blogPost->posttext) ? $blogPost->posttext : null; ?></textarea>*</td>                    </tr>        <tr>            <td>Article Image</td>            <!--maximum upload file size is set to 5mb -->            <td><input type="hidden" name="MAX_FILE_SIZE" value="51200" />                <span>Your Uploaded Image: <?= isset($blogPost->postimage) ? basename($blogPost->postimage) : null; ?>  </span>                <input type="file" name="txtpostimage" value="Choose new image"/>*</td>                    </tr>        <tr>            <td>Article Options</td>            <td><input type="radio" name="txtpostisvisible" value="0" <?=  (isset($blogPost->postisvisible) && $blogPost->postisvisible == 0) ? "checked" : "" ?>/> Save as Draft                <input type="radio" name="txtpostisvisible" value="1" <?=  (isset($blogPost->postisvisible) && $blogPost->postisvisible == 1) ? "checked" : "" ?>/> Publish *            </td>                    </tr>        <tr>                        <th colspan="2"><input type="hidden" name="formtype" value="edit" />                <input type="hidden" name="txtpostid" value="<?= isset($blogPost->postid) ? $blogPost->postid : null; ?>" />                <input type="submit" value="Update Your Article" /></th>        </tr>    </table>    </form></div>